<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Cagnote;
use App\Mail\DonationReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    /**
     * Calculate Stripe transaction fees based on donation amount
     * Base: 0.35€ + percentage that varies by amount tier
     */
    private function calculateFees($amount)
    {
        $percentage = 1.4; // Default for amounts < 50€
        
        if ($amount >= 500) {
            $percentage = 2.0;
        } elseif ($amount >= 100) {
            $percentage = 1.8;
        } elseif ($amount >= 50) {
            $percentage = 1.6;
        }
        
        $fees = 0.35 + ($amount * $percentage / 100);
        
        return round($fees, 2);
    }

    /**
     * Create a payment intent for a donation
     * POST /api/payments/intent
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $validated = $request->validate([
                'cagnote_id' => 'required|integer|exists:cagnotes,id',
                'amount' => 'required|numeric|min:1|max:999999',
                'donor_email' => 'required|email',
                'donor_name' => 'nullable|string',
                'donor_message' => 'nullable|string|max:500',
                'is_anonymous' => 'boolean',
            ]);

            $cagnote = Cagnote::findOrFail($validated['cagnote_id']);
            $user = $request->user();

            // Calculate fees
            $donationAmount = (float) $validated['amount'];
            $fees = $this->calculateFees($donationAmount);
            $totalAmount = $donationAmount + $fees;

            // Amount in cents for Stripe (total with fees)
            $amountInCents = (int) ($totalAmount * 100);

            Log::info('Creating payment intent', [
                'cagnote_id' => $validated['cagnote_id'],
                'donation_amount' => $donationAmount,
                'fees' => $fees,
                'total_amount' => $totalAmount,
                'user_id' => $user?->id,
            ]);

            // Create payment intent with total amount (including fees)
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'eur',
                'metadata' => [
                    'cagnote_id' => $validated['cagnote_id'],
                    'cagnote_title' => $cagnote->title,
                    'user_id' => $user?->id,
                    'donor_email' => $validated['donor_email'],
                    'donor_name' => $validated['donor_name'] ?? '',
                    'is_anonymous' => $validated['is_anonymous'] ? 'true' : 'false',
                    'donation_amount' => (string) $donationAmount,
                    'fees_amount' => (string) $fees,
                    'total_amount' => (string) $totalAmount,
                ],
                'description' => "Donation to: {$cagnote->title}",
            ]);

            // Create donation record in status pending
            // Store both the donation amount and the fees
            $donation = Donation::create([
                'user_id' => $user?->id,
                'cagnote_id' => $validated['cagnote_id'],
                'amount' => $donationAmount, // Amount the user wants to donate
                'fees' => $fees, // Transaction fees
                'total_amount' => $totalAmount, // Total charged (donation + fees)
                'currency' => 'EUR',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'status' => 'pending',
                'donor_email' => $validated['donor_email'],
                'donor_name' => $validated['donor_name'] ?? null,
                'donor_message' => $validated['donor_message'] ?? null,
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'metadata' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            Log::info('Payment intent created', [
                'donation_id' => $donation->id,
                'intent_id' => $paymentIntent->id,
                'donation_amount' => $donationAmount,
                'fees' => $fees,
                'total_amount' => $totalAmount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'donation_id' => $donation->id,
                    'amount' => $donationAmount,
                    'fees' => $fees,
                    'total_amount' => $totalAmount,
                    'currency' => 'EUR',
                ],
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error creating payment intent', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error('Error creating payment intent: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating payment intent',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Confirm payment and update donation status
     * POST /api/payments/confirm
     */
    public function confirmPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'donation_id' => 'required|integer|exists:donations,id',
                'payment_intent_id' => 'required|string',
            ]);

            $donation = Donation::findOrFail($validated['donation_id']);

            // Retrieve intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($validated['payment_intent_id']);

            Log::info('Confirming payment', [
                'donation_id' => $validated['donation_id'],
                'intent_id' => $validated['payment_intent_id'],
                'status' => $paymentIntent->status,
            ]);

            if ($paymentIntent->status === 'succeeded') {
                // Payment succeeded
                $chargeId = $paymentIntent->charges->data[0]->id ?? null;
                $donation->markAsPaid($chargeId, $paymentIntent->charges->data[0]->receipt_url ?? null);

                Log::info('Donation confirmed', [
                    'donation_id' => $donation->id,
                    'charge_id' => $chargeId,
                ]);

                // Send custom receipt email
                try {
                    Mail::to($donation->donor_email)->send(new DonationReceipt($donation));
                    Log::info('Receipt email sent', [
                        'donation_id' => $donation->id,
                        'email' => $donation->donor_email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send receipt email: ' . $e->getMessage(), [
                        'donation_id' => $donation->id,
                    ]);
                    // Don't fail the response if email fails
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully',
                    'data' => [
                        'donation_id' => $donation->id,
                        'status' => $donation->status,
                        'amount' => $donation->amount,
                    ],
                ], Response::HTTP_OK);

            } elseif ($paymentIntent->status === 'processing') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment is processing',
                    'data' => [
                        'donation_id' => $donation->id,
                        'status' => 'pending',
                    ],
                ], Response::HTTP_OK);

            } else {
                // Payment failed
                $donation->markAsFailed();

                Log::warning('Payment failed', [
                    'donation_id' => $donation->id,
                    'intent_status' => $paymentIntent->status,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed',
                    'data' => [
                        'donation_id' => $donation->id,
                        'status' => 'failed',
                    ],
                ], Response::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $e) {
            Log::error('Error confirming payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error confirming payment',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get donation details
     * GET /api/payments/donation/{id}
     */
    public function getDonation(Request $request, $id)
    {
        try {
            $donation = Donation::with('cagnote', 'user')->findOrFail($id);

            // Authorize: user can only see their own donations or if anonymous
            if ($donation->user_id && $donation->user_id !== auth('api')->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'success' => true,
                'data' => $donation->load('cagnote:id,title,objective_amount,collected_amount'),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching donation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Donation not found',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get all donations by the logged-in user (donor)
     * GET /api/payments/my-donations
     */
    public function getDonorDonations(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $donations = Donation::where('user_id', $user->id)
                ->with(['cagnote:id,title,objective_amount,collected_amount'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($donation) {
                    return [
                        'id' => $donation->id,
                        'amount' => $donation->amount,
                        'currency' => $donation->currency,
                        'status' => $donation->status,
                        'created_at' => $donation->created_at,
                        'cagnote_id' => $donation->cagnote_id,
                        'cagnote_title' => $donation->cagnote?->title,
                        'association_name' => $donation->cagnote?->creator?->name ?? 'Organisation',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $donations,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching donor donations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching donations',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all donations for a cagnote
     * GET /api/payments/cagnote/{id}/donations
     */
    public function getCagnoteDonations($cagnoteId)
    {
        try {
            $cagnote = Cagnote::findOrFail($cagnoteId);

            $donations = Donation::where('cagnote_id', $cagnoteId)
                ->where('status', 'success')
                ->whereNotNull('paid_at')
                ->latest('paid_at')
                ->get()
                ->makeHidden(['stripe_payment_intent_id', 'stripe_charge_id', 'metadata', 'user_id']);

            $totalDonated = $donations->sum('amount');
            $donationCount = $donations->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'cagnote_id' => $cagnoteId,
                    'total_donated' => $totalDonated,
                    'donation_count' => $donationCount,
                    'donations' => $donations,
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching cagnote donations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching donations',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle Stripe webhook events
     * POST /api/payments/webhook
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = json_decode($request->getContent(), true);
            $event = $payload['type'] ?? null;

            Log::info('Webhook received', ['event' => $event]);

            switch ($event) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($payload['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($payload['data']['object']);
                    break;

                case 'payment_intent.requires_action':
                    Log::info('Payment requires action', ['intent_id' => $payload['data']['object']['id']]);
                    break;
            }

            return response()->json(['status' => 'received'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    private function handlePaymentSucceeded($intent)
    {
        $donation = Donation::where('stripe_payment_intent_id', $intent['id'])->first();

        if ($donation && !$donation->isComplete()) {
            $chargeId = $intent['charges']['data'][0]['id'] ?? null;
            $donation->markAsPaid($chargeId);

            Log::info('Payment succeeded via webhook', [
                'donation_id' => $donation->id,
                'amount' => $donation->amount,
            ]);

            // Send custom receipt email
            try {
                Mail::to($donation->donor_email)->send(new DonationReceipt($donation));
                Log::info('Receipt email sent via webhook', [
                    'donation_id' => $donation->id,
                    'email' => $donation->donor_email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send receipt email: ' . $e->getMessage(), [
                    'donation_id' => $donation->id,
                ]);
            }
        }
    }

    private function handlePaymentFailed($intent)
    {
        $donation = Donation::where('stripe_payment_intent_id', $intent['id'])->first();

        if ($donation && !$donation->isComplete()) {
            $donation->markAsFailed();

            Log::warning('Payment failed via webhook', ['donation_id' => $donation->id]);
        }
    }
}
