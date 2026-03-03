<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WithdrawalProcessedMail;
use App\Mail\WithdrawalRejectedMail;
use App\Models\WithdrawalRequest;
use App\Models\Cagnote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WithdrawalRequestController extends Controller
{
    /**
     * Display withdrawal requests
     * GET /api/withdrawal-requests
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // If user is authenticated as association, show only their withdrawal requests
            if ($user) {
                $withdrawalRequests = WithdrawalRequest::where('user_id', $user->id)
                    ->with(['cagnote', 'user'])
                    ->orderByDesc('created_at')
                    ->paginate(15);
            } else {
                // Unauthenticated users get empty result
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Unauthorized'
                ], Response::HTTP_UNAUTHORIZED);
            }

            Log::info('Withdrawal requests retrieved', [
                'user_id' => $user->id,
                'count' => $withdrawalRequests->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $withdrawalRequests->items(),
                'pagination' => [
                    'current_page' => $withdrawalRequests->currentPage(),
                    'per_page' => $withdrawalRequests->perPage(),
                    'total' => $withdrawalRequests->total(),
                    'last_page' => $withdrawalRequests->lastPage(),
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error retrieving withdrawal requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving withdrawal requests',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get admin withdrawal requests (pending only)
     * GET /api/admin/withdrawal-requests
     */
    public function adminIndex(Request $request)
    {
        try {
            $user = $request->user();
            
            // Check if user is admin
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required'
                ], Response::HTTP_FORBIDDEN);
            }

            // Get all pending withdrawal requests
            $status = $request->query('status', 'pending');
            
            $query = WithdrawalRequest::with(['cagnote', 'user', 'processedBy']);
            
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }
            
            $withdrawalRequests = $query
                ->orderByDesc('created_at')
                ->get();

            Log::info('Admin withdrawal requests retrieved', [
                'admin_id' => $user->id,
                'status' => $status,
                'count' => $withdrawalRequests->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $withdrawalRequests->toArray(),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error retrieving admin withdrawal requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving withdrawal requests',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified withdrawal request
     * GET /api/withdrawal-requests/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $withdrawalRequest = WithdrawalRequest::with(['cagnote', 'user', 'processedBy'])
                ->findOrFail($id);

            // Authorization check
            $user = $request->user();
            if ($user && $user->id !== $withdrawalRequest->user_id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'success' => true,
                'data' => $withdrawalRequest,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error retrieving withdrawal request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Process (approve and mark as processed) a withdrawal request
     * PATCH /api/admin/withdrawal-requests/{id}/process
     */
    public function processWithdrawal(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Check if user is admin
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'transaction_reference' => 'nullable|string|max:255',
            ]);

            $withdrawalRequest = WithdrawalRequest::findOrFail($id);

            // Can only process pending requests
            if ($withdrawalRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending withdrawal requests can be processed',
                ], Response::HTTP_BAD_REQUEST);
            }

            $withdrawalRequest->update([
                'status' => 'processed',
                'processed_at' => now(),
                'processed_by' => $user->id,
                'transaction_reference' => $validated['transaction_reference'] ?? null,
            ]);

            Log::info('Withdrawal request processed', [
                'withdrawal_request_id' => $withdrawalRequest->id,
                'admin_id' => $user->id,
                'transaction_reference' => $validated['transaction_reference'] ?? null,
            ]);

            // Send email to the association
            try {
                $associationUser = $withdrawalRequest->user;
                if ($associationUser && $associationUser->email) {
                    Mail::to($associationUser->email)->send(
                        new WithdrawalProcessedMail($withdrawalRequest, $associationUser)
                    );
                    Log::info('Withdrawal processed email sent', [
                        'user_id' => $associationUser->id,
                        'email' => $associationUser->email,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending withdrawal processed email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request processed successfully',
                'data' => $withdrawalRequest,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error processing withdrawal request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing withdrawal request',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reject a withdrawal request
     * PATCH /api/admin/withdrawal-requests/{id}/reject
     */
    public function rejectWithdrawal(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Check if user is admin
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $withdrawalRequest = WithdrawalRequest::findOrFail($id);

            // Can only reject pending requests
            if ($withdrawalRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending withdrawal requests can be rejected',
                ], Response::HTTP_BAD_REQUEST);
            }

            $withdrawalRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'processed_at' => now(),
                'processed_by' => $user->id,
            ]);

            Log::info('Withdrawal request rejected', [
                'withdrawal_request_id' => $withdrawalRequest->id,
                'admin_id' => $user->id,
                'reason' => $validated['rejection_reason'],
            ]);

            // Send email to the association
            try {
                $associationUser = $withdrawalRequest->user;
                if ($associationUser && $associationUser->email) {
                    Mail::to($associationUser->email)->send(
                        new WithdrawalRejectedMail($withdrawalRequest, $associationUser)
                    );
                    Log::info('Withdrawal rejected email sent', [
                        'user_id' => $associationUser->id,
                        'email' => $associationUser->email,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending withdrawal rejected email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request rejected',
                'data' => $withdrawalRequest,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error rejecting withdrawal request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting withdrawal request',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
