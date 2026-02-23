<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cagnote;
use App\Models\User;
use App\Mail\CagnoteApprovedMail;
use App\Mail\CagnoteRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminCagnoteController extends Controller
{
    /**
     * Get all pending cagnotes for review
     * GET /api/admin/cagnotes/pending
     */
    public function getPendingCagnotes(Request $request)
    {
        try {
            $user = $request->user();

            // Verify admin role
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $cagnotes = Cagnote::where('publication_status', 'pending')
                ->orWhere('publication_status', 'under_review')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cagnotes en attente de validation',
                'data' => $cagnotes,
                'count' => count($cagnotes)
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching pending cagnotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des cagnotes',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all cagnotes (with filtering capability)
     * GET /api/admin/cagnotes
     */
    public function getAllCagnotes(Request $request)
    {
        try {
            $user = $request->user();

            // Verify admin role
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $cagnotes = Cagnote::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Toutes les cagnotes',
                'data' => $cagnotes,
                'count' => count($cagnotes)
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching all cagnotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des cagnotes',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific cagnote for review
     * GET /api/admin/cagnotes/{id}/review
     */
    public function reviewCagnote(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Verify admin role
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $cagnote = Cagnote::with('user')->find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Update to under_review status
            if ($cagnote->publication_status === 'pending') {
                $cagnote->update(['publication_status' => 'under_review']);
                Log::info('Cagnote ' . $id . ' marked as under_review by admin ' . $user->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cagnote récupérée pour révision',
                'data' => $cagnote
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching cagnote for review: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Approve a cagnote
     * POST /api/admin/cagnotes/{id}/approve
     */
    public function approveCagnote(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Verify admin role
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Update cagnote status
            $cagnote->update([
                'publication_status' => 'approved',
                'status' => 'active',
                'validated_at' => now(),
                'validated_by' => $user->id,
            ]);

            Log::info('Cagnote ' . $id . ' approved by admin ' . $user->id);

            // Send approval email to association
            $association = $cagnote->user;
            try {
                Mail::to($association->email)->send(new CagnoteApprovedMail($cagnote, $association));
                Log::info('Approval email sent to association for cagnote: ' . $id);
            } catch (\Exception $mailException) {
                Log::error('Error sending approval email for cagnote ' . $id . ': ' . $mailException->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Cagnote validée et publiée avec succès',
                'data' => $cagnote
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error approving cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reject a cagnote
     * POST /api/admin/cagnotes/{id}/reject
     */
    public function rejectCagnote(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Verify admin role
            if (!$user || $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|min:10|max:1000',
            ]);

            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Update cagnote status
            $cagnote->update([
                'publication_status' => 'rejected',
                'validated_at' => now(),
                'validated_by' => $user->id,
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            Log::info('Cagnote ' . $id . ' rejected by admin ' . $user->id);

            // Send rejection email to association
            $association = $cagnote->user;
            Mail::to($association->email)->send(
                new CagnoteRejectedMail($cagnote, $association, $validated['rejection_reason'])
            );
            Log::info('Rejection email sent to association for cagnote: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Cagnote refusée. Email de notification envoyé à l\'association.',
                'data' => $cagnote
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error rejecting cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
