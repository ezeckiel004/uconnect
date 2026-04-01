<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeConnectController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    /**
     * Create or refresh a Stripe Connect onboarding link for the authenticated association.
     */
    public function onboardingLink(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->type !== 'association') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les associations peuvent configurer Stripe Connect.',
            ], 403);
        }

        try {
            if (empty($user->stripe_connect_account_id)) {
                $account = Account::create([
                    'type' => 'express',
                    'email' => $user->email,
                    'metadata' => [
                        'uconnect_user_id' => (string) $user->id,
                        'uconnect_user_type' => (string) $user->type,
                    ],
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                ]);

                $user->stripe_connect_account_id = $account->id;
                $user->save();
            }

            $refreshUrl = $request->input('refresh_url', rtrim(config('app.url'), '/') . '/onboarding/refresh');
            $returnUrl = $request->input('return_url', rtrim(config('app.url'), '/') . '/onboarding/success');

            $accountLink = AccountLink::create([
                'account' => $user->stripe_connect_account_id,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

            $account = Account::retrieve($user->stripe_connect_account_id);
            $this->syncAccountState($user, $account);

            return response()->json([
                'success' => true,
                'message' => 'Lien d onboarding genere avec succes.',
                'data' => [
                    'url' => $accountLink->url,
                    'expires_at' => $accountLink->expires_at,
                    'account_id' => $user->stripe_connect_account_id,
                    'charges_enabled' => (bool) $account->charges_enabled,
                    'payouts_enabled' => (bool) $account->payouts_enabled,
                    'details_submitted' => (bool) $account->details_submitted,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Connect onboarding link error: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur Stripe: ' . $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Stripe Connect onboarding link unexpected error: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de generer le lien Stripe Connect.',
            ], 500);
        }
    }

    /**
     * Get and sync the current Stripe Connect status for the authenticated association.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->type !== 'association') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les associations peuvent consulter ce statut.',
            ], 403);
        }

        if (empty($user->stripe_connect_account_id)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'connected' => false,
                    'account_id' => null,
                    'charges_enabled' => false,
                    'payouts_enabled' => false,
                    'details_submitted' => false,
                ],
            ]);
        }

        try {
            $account = Account::retrieve($user->stripe_connect_account_id);
            $this->syncAccountState($user, $account);

            return response()->json([
                'success' => true,
                'data' => [
                    'connected' => true,
                    'account_id' => $user->stripe_connect_account_id,
                    'charges_enabled' => (bool) $account->charges_enabled,
                    'payouts_enabled' => (bool) $account->payouts_enabled,
                    'details_submitted' => (bool) $account->details_submitted,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Connect status error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'account_id' => $user->stripe_connect_account_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur Stripe: ' . $e->getMessage(),
            ], 422);
        }
    }

    private function syncAccountState($user, $account): void
    {
        $user->stripe_connect_onboarded = (bool) $account->details_submitted;
        $user->stripe_charges_enabled = (bool) $account->charges_enabled;
        $user->stripe_payouts_enabled = (bool) $account->payouts_enabled;
        $user->save();
    }
}
