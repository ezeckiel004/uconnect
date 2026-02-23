<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cagnote;
use App\Models\CagnoteLike;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CagnoteLikeController extends Controller
{
    /**
     * Like a cagnote
     * POST /api/cagnotes/{id}/like
     */
    public function like(Request $request, $id)
    {
        try {
            $user = $request->user();
            Log::info('=== LIKE START ===');
            Log::info('Auth user: ' . ($user ? $user->id . ' (' . $user->email . ')' : 'NO USER'));
            Log::info('Request tokens: ' . print_r($request->header('Authorization'), true));

            // Check if user is authenticated
            if (!$user) {
                Log::warning('No authenticated user for like request');
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour aimer une cagnote',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if already liked
            $existingLike = CagnoteLike::where('user_id', $user->id)
                ->where('cagnote_id', $id)
                ->first();

            if ($existingLike) {
                Log::info('User ' . $user->id . ' already likes cagnote ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà aimé cette cagnote',
                    'liked' => true,
                    'like_count' => $cagnote->getLikeCount(),
                ], Response::HTTP_CONFLICT);
            }

            // Create like
            CagnoteLike::create([
                'user_id' => $user->id,
                'cagnote_id' => $id,
            ]);

            Log::info('User ' . $user->id . ' liked cagnote ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Cagnote aimée avec succès',
                'liked' => true,
                'like_count' => $cagnote->getLikeCount(),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error liking cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du like',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Unlike a cagnote
     * DELETE /api/cagnotes/{id}/like
     */
    public function unlike(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Check if user is authenticated
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour retirer un like',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Find and delete like
            $like = CagnoteLike::where('user_id', $user->id)
                ->where('cagnote_id', $id)
                ->first();

            if ($like) {
                $like->delete();
                Log::info('User ' . $user->id . ' unliked cagnote ' . $id);
            }

            // Toujours retourner 200 OK avec liked=false quand on essaie de dislike
            return response()->json([
                'success' => true,
                'message' => 'Like retiré avec succès',
                'liked' => false,
                'like_count' => $cagnote->getLikeCount(),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error unliking cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du retrait du like',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check if user has liked a cagnote
     * GET /api/cagnotes/{id}/like-status
     */
    public function checkLikeStatus(Request $request, $id)
    {
        try {
            // Log the exact Authorization header received
            $authHeader = $request->header('Authorization');
            Log::info('=== checkLikeStatus START ===');
            Log::info('Authorization header: ' . ($authHeader ? substr($authHeader, 0, 50) . '...' : 'MISSING'));
            
            $user = $request->user();
            Log::info('User from $request->user(): ' . ($user ? $user->id . ' (' . $user->email . ')' : 'NULL - Authentication failed!'));
            Log::info('Cagnote ID requested: ' . $id);
            
            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                Log::warning('Cagnote not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            Log::info('Found cagnote: ' . $cagnote->title);

            $liked = false;
            $likeCount = $cagnote->getLikeCount();
            Log::info('Total likes for this cagnote: ' . $likeCount);
            
            if ($user) {
                Log::info('Checking if user ' . $user->id . ' liked cagnote ' . $id);
                
                // Direct query method (most reliable)
                $likeCheck = CagnoteLike::where('user_id', $user->id)
                    ->where('cagnote_id', $id)
                    ->count();
                Log::info('Like query result: ' . $likeCheck);
                $liked = $likeCheck > 0;
                
            } else {
                Log::warning('*** NO AUTHENTICATED USER - Token may be invalid or missing! ***');
            }

            Log::info('Final response: liked=' . ($liked ? 'true' : 'false') . ', like_count=' . $likeCount);
            Log::info('=== checkLikeStatus END ===');

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'like_count' => $likeCount,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error checking like status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du like',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

