<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cagnote;
use App\Models\CagnoteLike;
use App\Mail\CagnoteCreatedAdminMail;
use App\Mail\CagnoteCreatedAssociationMail;
use App\Http\Resources\CagnoteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class CagnoteController extends Controller
{
    /**
     * Get all cagnotes for the authenticated association user
     * GET /api/cagnotes
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user || $user->type !== 'association') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $cagnotes = Cagnote::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cagnotes récupérées avec succès',
                'data' => $cagnotes
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching cagnotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des cagnotes',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new cagnote
     * POST /api/cagnotes
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user || $user->type !== 'association') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'category' => 'nullable|string|in:Nourriture,Eau,Infrastructure,Santé,Sociale,SOS,Environnement',
                'objective_amount' => 'required|numeric|min:0.01',
                'start_date' => 'nullable|date',
                'deadline' => 'nullable|date|after:today',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'photos' => 'nullable|array',
                'photos.*' => 'nullable|string',
                // Banking information
                'account_holder_name' => 'nullable|string|max:255',
                'iban' => 'nullable|string|max:34',
                'bic' => 'nullable|string|max:11',
                'bank_name' => 'nullable|string|max:255',
                'account_type' => 'nullable|string|in:individual,association,company,other',
                'account_address' => 'nullable|string',
                'account_phone' => 'nullable|string|max:20',
                'account_email' => 'nullable|email',
            ]);

            // Log validated data for debugging
            Log::info('Cagnote store - Validated data:', [
                'title' => $validated['title'],
                'category' => $validated['category'] ?? 'null',
                'location' => $validated['location'] ?? 'null',
                'city' => $validated['city'] ?? 'null',
            ]);

            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $request->file('image')->store('cagnotes', 'public');
            }

            // Process base64 photos
            $photoUrls = [];
            if (!empty($validated['photos'])) {
                foreach ($validated['photos'] as $photoData) {
                    if (!empty($photoData)) {
                        try {
                            $photoUrl = $this->saveBase64Image($photoData);
                            if ($photoUrl) {
                                $photoUrls[] = $photoUrl;
                            }
                        } catch (\Exception $e) {
                            Log::warning('Error saving base64 photo: ' . $e->getMessage());
                        }
                    }
                }
            }

            $cagnote = Cagnote::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'location' => $validated['location'] ?? null,
                'city' => $validated['city'] ?? null,
                'category' => $validated['category'] ?? 'Nourriture',
                'objective_amount' => $validated['objective_amount'],
                'start_date' => $validated['start_date'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'image_url' => $imageUrl,
                'photos' => $photoUrls,
                'collected_amount' => 0,
                'status' => 'active',
                'publication_status' => 'pending',
                // Banking information
                'account_holder_name' => $validated['account_holder_name'] ?? null,
                'iban' => $validated['iban'] ?? null,
                'bic' => $validated['bic'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'account_type' => $validated['account_type'] ?? null,
                'account_address' => $validated['account_address'] ?? null,
                'account_phone' => $validated['account_phone'] ?? null,
                'account_email' => $validated['account_email'] ?? null,
            ]);

            Log::info('Cagnote created: ' . $cagnote->id . ' for user ' . $user->id);

            // Send email to admin for review
            $admin = \App\Models\User::where('type', 'admin')->first();
            if ($admin) {
                try {
                    Mail::to($admin->email)->send(new CagnoteCreatedAdminMail($cagnote, $user));
                    Log::info('Admin review email sent for cagnote: ' . $cagnote->id);
                } catch (\Exception $e) {
                    Log::error('Error sending admin email for cagnote ' . $cagnote->id . ': ' . $e->getMessage());
                }
            }

            // Add delay to prevent rate limiting
            sleep(1);

            // Send confirmation email to association
            try {
                Mail::to($user->email)->send(new CagnoteCreatedAssociationMail($cagnote, $user));
                Log::info('Association confirmation email sent for cagnote: ' . $cagnote->id);
            } catch (\Exception $e) {
                Log::error('Error sending association email for cagnote ' . $cagnote->id . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Cagnote créée avec succès. Un email de confirmation a été envoyé.',
                'data' => $cagnote
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error creating cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific cagnote
     * GET /api/cagnotes/{id}
     */
    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if user owns this cagnote
            if ($user->id !== $cagnote->user_id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cagnote récupérée avec succès',
                'data' => $cagnote
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a cagnote
     * PUT /api/cagnotes/{id}
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if user owns this cagnote
            if ($user->id !== $cagnote->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'category' => 'nullable|string|in:Nourriture,Eau,Infrastructure,Santé,Sociale,SOS',
                'objective_amount' => 'sometimes|numeric|min:0.01',
                'start_date' => 'nullable|date',
                'deadline' => 'nullable|date|after:today',
                'status' => 'sometimes|in:active,completed,archived',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'photos' => 'nullable|array',
                'photos.*' => 'nullable|string',
                // Banking information
                'account_holder_name' => 'nullable|string|max:255',
                'iban' => 'nullable|string|max:34',
                'bic' => 'nullable|string|max:11',
                'bank_name' => 'nullable|string|max:255',
                'account_type' => 'nullable|string|in:individual,association,company,other',
                'account_address' => 'nullable|string',
                'account_phone' => 'nullable|string|max:20',
                'account_email' => 'nullable|email',
            ]);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($cagnote->image_url) {
                    Storage::disk('public')->delete($cagnote->image_url);
                }
                $validated['image_url'] = $request->file('image')->store('cagnotes', 'public');
            }

            // Process base64 photos if provided
            if (!empty($validated['photos'])) {
                $photoUrls = [];
                foreach ($validated['photos'] as $photoData) {
                    if (!empty($photoData)) {
                        try {
                            $photoUrl = $this->saveBase64Image($photoData);
                            if ($photoUrl) {
                                $photoUrls[] = $photoUrl;
                            }
                        } catch (\Exception $e) {
                            Log::warning('Error saving base64 photo: ' . $e->getMessage());
                        }
                    }
                }
                $validated['photos'] = $photoUrls;
            }

            $cagnote->update($validated);

            Log::info('Cagnote updated: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Cagnote mise à jour avec succès',
                'data' => $cagnote
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error updating cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a cagnote
     * DELETE /api/cagnotes/{id}
     */
    public function destroy(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $cagnote = Cagnote::find($id);

            if (!$cagnote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cagnote non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if user owns this cagnote
            if ($user->id !== $cagnote->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            // Delete image if exists
            if ($cagnote->image_url) {
                Storage::disk('public')->delete($cagnote->image_url);
            }

            $cagnote->delete();

            Log::info('Cagnote deleted: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Cagnote supprimée avec succès'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error deleting cagnote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la cagnote',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save a base64 encoded image to storage
     */
    private function saveBase64Image(string $base64Data): ?string
    {
        try {
            // If it's already a URL (existing photo from database), return as-is
            if (strpos($base64Data, '/storage/') === 0 || strpos($base64Data, 'http') === 0) {
                Log::info('Photo URL conservée: ' . $base64Data);
                return $base64Data;
            }

            // Check if data is base64
            if (!preg_match('/^data:image\/[a-z]+;base64,/', $base64Data)) {
                // If it's not base64 data url format, treat as regular base64
                if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $base64Data)) {
                    return null;
                }
            } else {
                // Remove data URL header
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            }

            // Decode base64
            $imageData = base64_decode($base64Data, true);
            if (!$imageData) {
                return null;
            }

            // Generate unique filename
            $filename = 'cagnotes/photo_' . time() . '_' . uniqid() . '.jpg';

            // Save to storage
            Storage::disk('public')->put($filename, $imageData);

            // Return URL
            return '/storage/' . $filename;
        } catch (\Exception $e) {
            Log::error('Error saving base64 image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all approved cagnotes (public route)
     * GET /api/cagnotes/approved/all
     */
    public function getApprovedCagnotes(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $cagnotes = Cagnote::where('publication_status', 'approved')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cagnotes approuvées récupérées avec succès',
                'data' => CagnoteResource::collection($cagnotes),
                'count' => count($cagnotes)
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching approved cagnotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des cagnotes approuvées',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get approved cagnotes by category
     * GET /api/cagnotes/category/{category}
     */
    public function getApprovedCagnotesByCategory($category): \Illuminate\Http\JsonResponse
    {
        try {
            $cagnotes = Cagnote::where('publication_status', 'approved')
                ->where('category', $category)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cagnotes approuvées récupérées avec succès',
                'data' => CagnoteResource::collection($cagnotes),
                'count' => count($cagnotes),
                'category' => $category
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching approved cagnotes by category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des cagnotes approuvées',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
