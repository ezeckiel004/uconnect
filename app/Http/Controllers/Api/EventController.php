<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Create a new event
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'date' => 'required|date',
                'time' => 'required|string',
                'photos' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $event = Event::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'date' => $request->date,
                'time' => $request->time,
                'registration_link' => $request->registration_link,
                'photos' => $request->photos ?? [],
                'status' => 'pending',
            ]);

            Log::info('📅 Event created', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'title' => $event->title,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement créé avec succès',
                'data' => $event,
            ], 201);
        } catch (\Exception $e) {
            Log::error('❌ Error creating event: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get events for the authenticated user (association)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $events = Event::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error fetching events: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific event
     */
    public function show(Request $request, $id)
    {
        try {
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $event,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error fetching event: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an event
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            if ($event->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'location' => 'sometimes|string|max:255',
                'date' => 'sometimes|date',
                'time' => 'sometimes|string',
                'photos' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $event->update($request->only([
                'title',
                'description',
                'location',
                'date',
                'time',
                'photos',
            ]));

            Log::info('📝 Event updated', [
                'event_id' => $event->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'data' => $event,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error updating event: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an event
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            if ($event->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }

            $event->delete();

            Log::info('🗑️ Event deleted', [
                'event_id' => $event->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement supprimé avec succès',
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error deleting event: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get pending events for admin validation
     */
    public function getPendingEvents(Request $request)
    {
        try {
            $events = Event::where('status', 'pending')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error fetching pending events: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve an event (admin only)
     */
    public function approveEvent(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            $event->update([
                'status' => 'approved',
                'validated_at' => now(),
                'validated_by' => $admin->id,
            ]);

            Log::info('✅ Event approved', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'event_title' => $event->title,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement approuvé avec succès',
                'data' => $event,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error approving event: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject an event (admin only)
     */
    public function rejectEvent(Request $request, $id)
    {
        try {
            $admin = $request->user();

            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            $event->update([
                'status' => 'rejected',
                'validated_at' => now(),
                'validated_by' => $admin->id,
                'rejection_reason' => $request->reason,
            ]);

            Log::info('❌ Event rejected', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'event_title' => $event->title,
                'reason' => $request->reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement rejeté avec succès',
                'data' => $event,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error rejecting event: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all approved events (public endpoint)
     */
    public function getApprovedEvents()
    {
        try {
            Log::info('📅 Retrieving approved events (public)');

            $events = Event::where('status', 'approved')
                ->orderBy('date', 'desc')
                ->with('user')
                ->get();

            Log::info('✅ Retrieved ' . count($events) . ' approved events');

            return response()->json([
                'success' => true,
                'message' => 'Événements approuvés récupérés',
                'data' => $events,
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Error retrieving approved events: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
