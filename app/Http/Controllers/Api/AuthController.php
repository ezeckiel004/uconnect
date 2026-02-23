<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\AssociationCredentialsNotification;
use App\Mail\AssociationPasswordUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Association Login
     * POST /api/auth/login
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('code', $validated['code'])
                ->where('type', 'association')
                ->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code ou mot de passe incorrect',
                    'errors' => ['credentials' => ['Les identifiants fournis sont invalides.']]
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'code' => $user->code,
                        'phone_number' => $user->phone_number,
                        'description' => $user->description,
                        'first_login' => $user->first_login,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Donor Login
     * POST /api/auth/login-donor
     */
    public function loginDonor(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])
                ->where('type', 'donor')
                ->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect',
                    'errors' => ['credentials' => ['Les identifiants fournis sont invalides.']]
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'code' => $user->code,
                        'phone_number' => $user->phone_number,
                        'description' => $user->description,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Guest Login (Donor Access)
     * POST /api/auth/guest
     */
    public function guestAccess(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Create or get a guest session token
            // For a true guest, we generate a temporary guest user
            $guestToken = 'guest_' . bin2hex(random_bytes(32));

            return response()->json([
                'success' => true,
                'message' => 'Accès donateur accordé',
                'data' => [
                    'user' => [
                        'id' => null,
                        'name' => 'Donateur',
                        'email' => null,
                        'type' => 'donor',
                        'code' => null,
                        'phone_number' => null,
                        'description' => null,
                    ],
                    'token' => $guestToken,
                    'token_type' => 'Guest',
                    'is_guest' => true,
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'accès en tant que donateur',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Authenticated User
     * GET /api/auth/user
     */
    public function user(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'code' => $user->code,
                    'phone_number' => $user->phone_number,
                    'description' => $user->description,
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'utilisateur',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout
     * POST /api/auth/logout
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Refresh Token
     * POST /api/auth/refresh
     */
    public function refreshToken(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Revoke old tokens
            $user->tokens()->delete();

            // Generate new token
            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Token rafraîchi avec succès',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rafraîchissement du token',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register Donor
     * POST /api/auth/register-donor
     */
    public function registerDonor(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone_number' => 'nullable|string',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => 'donor',
                'phone_number' => $validated['phone_number'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'code' => $user->code,
                        'phone_number' => $user->phone_number,
                        'description' => $user->description,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register Association (Self Registration - no code/password needed)
     * POST /api/auth/register-association
     */
    public function registerAssociation(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone_number' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'type' => 'association',
                'phone_number' => $validated['phone_number'] ?? null,
                'description' => $validated['description'] ?? null,
                // code and password will be set by admin later
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie. En attente de l\'attribution des identifiants par l\'administrateur.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'code' => $user->code,
                        'phone_number' => $user->phone_number,
                        'description' => $user->description,
                    ],
                ]
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin Login
     * POST /api/auth/login-admin
     */
    public function loginAdmin(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])
                ->where('type', 'admin')
                ->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect',
                    'errors' => ['credentials' => ['Les identifiants fournis sont invalides.']]
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion administrateur réussie',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'code' => $user->code,
                        'phone_number' => $user->phone_number,
                        'description' => $user->description,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Get All Associations (Admin only)
     * GET /api/admin/associations
     */
    public function getAllAssociations(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $admin = $request->user();

            if (!$admin || $admin->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $associations = User::where('type', 'association')->get([
                'id',
                'name',
                'email',
                'code',
                'phone_number',
                'description',
                'created_at',
                'updated_at'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Listes des associations',
                'data' => $associations
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des associations',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign Association Credentials (Admin only)
     * POST /api/admin/assign-credentials
     */
    public function assignAssociationCredentials(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $admin = $request->user();

            if (!$admin || $admin->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'association_id' => 'required|exists:users,id',
                'code' => 'required|string|unique:users,code',
                'password' => 'required|string|min:8',
            ]);

            $association = User::findOrFail($validated['association_id']);

            // Check if it's actually an association
            if ($association->type !== 'association') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet utilisateur n\'est pas une association'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Update credentials
            $association->update([
                'code' => $validated['code'],
                'password' => Hash::make($validated['password']),
            ]);

            // Send credentials via email
            try {
                Mail::to($association->email)->send(
                    new AssociationCredentialsNotification(
                        $association,
                        $validated['code'],
                        $validated['password']
                    )
                );
            } catch (\Exception $mailError) {
                Log::warning('Email sending failed for association ' . $association->id . ': ' . $mailError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Identifiants attribués avec succès',
                'data' => [
                    'id' => $association->id,
                    'name' => $association->name,
                    'email' => $association->email,
                    'code' => $association->code,
                    'phone_number' => $association->phone_number,
                    'description' => $association->description,
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'attribution des identifiants',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Association Password (Admin only)
     * POST /api/admin/update-password
     */
    public function updateAssociationPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $admin = $request->user();

            if (!$admin || $admin->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'association_id' => 'required|exists:users,id',
                'password' => 'required|string|min:8',
            ]);

            $association = User::findOrFail($validated['association_id']);

            if ($association->type !== 'association') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet utilisateur n\'est pas une association'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $association->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Send password update notification via email
            try {
                Mail::to($association->email)->send(
                    new AssociationPasswordUpdatedNotification(
                        $association,
                        $validated['password']
                    )
                );
            } catch (\Exception $mailError) {
                Log::warning('Password update email sending failed for association ' . $association->id . ': ' . $mailError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès',
                'data' => [
                    'id' => $association->id,
                    'name' => $association->name,
                    'email' => $association->email,
                    'code' => $association->code,
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du mot de passe',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Association Change Password on First Login
     * POST /api/auth/change-first-login-password
     * Protected: auth:api
     */
    public function changeFirstLoginPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user || $user->type !== 'association') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            if (!$user->first_login) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà changé votre mot de passe'
                ], Response::HTTP_BAD_REQUEST);
            }

            $validated = $request->validate([
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($validated['new_password']),
                'first_login' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe changé avec succès',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'first_login' => false,
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

