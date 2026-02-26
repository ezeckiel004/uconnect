<?php

namespace App\Http\Controllers\Api;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        // Only associations can access messages
        if ($user->type !== 'association') {
            return response()->json([
                'message' => 'Only associations can access messages',
                'error' => 'Unauthorized'
            ], 403);
        }

        $conversations = Conversation::where('association1_id', $user->id)
            ->orWhere('association2_id', $user->id)
            ->with([
                'association1',
                'association2',
                'lastSender',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherAssociation = $conversation->getOtherAssociation($user->id);
                return [
                    'id' => $conversation->id,
                    'association1Id' => $conversation->association1_id,
                    'association2Id' => $conversation->association2_id,
                    'lastMessage' => $conversation->last_message,
                    'lastMessageAt' => $conversation->last_message_at?->toIso8601String(),
                    'lastSenderId' => $conversation->last_sender_id,
                    'lastSenderName' => $conversation->lastSender?->name ?? 'Unknown',
                    'otherAssociation' => [
                        'id' => $otherAssociation->id,
                        'name' => $otherAssociation->name,
                        'email' => $otherAssociation->email,
                        'code' => $otherAssociation->code,
                        'description' => $otherAssociation->description,
                    ],
                    'unreadCount' => $conversation->messages()
                        ->where('sender_id', '!=', $user->id)
                        ->where('is_read', false)
                        ->count(),
                    'createdAt' => $conversation->created_at->toIso8601String(),
                    'updatedAt' => $conversation->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations,
            'count' => $conversations->count(),
        ]);
    }

    /**
     * Search for other associations
     */
    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Only associations can search
        if ($user->type !== 'association') {
            return response()->json([
                'message' => 'Only associations can search',
                'error' => 'Unauthorized'
            ], 403);
        }

        $query = $request->get('query', '');

        $associations = User::where('type', 'association')
            ->where('id', '!=', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%")
                    ->orWhere('code', 'like', "%$query%");
            })
            ->select('id', 'name', 'email', 'code', 'description')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $associations,
            'count' => $associations->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Only associations can create conversations
        if ($user->type !== 'association') {
            return response()->json([
                'message' => 'Only associations can create conversations',
                'error' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'other_association_id' => 'required|integer|exists:users,id|different:' . $user->id,
        ]);

        // Get or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'association1_id' => min($user->id, $validated['other_association_id']),
                'association2_id' => max($user->id, $validated['other_association_id']),
            ]
        );

        $conversation->load([
            'association1',
            'association2',
            'lastSender',
        ]);

        $otherAssociation = $conversation->getOtherAssociation($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $conversation->id,
                'association1Id' => $conversation->association1_id,
                'association2Id' => $conversation->association2_id,
                'lastMessage' => $conversation->last_message,
                'lastMessageAt' => $conversation->last_message_at?->toIso8601String(),
                'lastSenderId' => $conversation->last_sender_id,
                'lastSenderName' => $conversation->lastSender?->name ?? 'Unknown',
                'otherAssociation' => [
                    'id' => $otherAssociation->id,
                    'name' => $otherAssociation->name,
                    'email' => $otherAssociation->email,
                    'code' => $otherAssociation->code,
                    'description' => $otherAssociation->description,
                ],
                'unreadCount' => 0,
                'createdAt' => $conversation->created_at->toIso8601String(),
                'updatedAt' => $conversation->updated_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        // Only associations can view conversations
        if ($user->type !== 'association') {
            return response()->json([
                'message' => 'Only associations can view conversations',
                'error' => 'Unauthorized'
            ], 403);
        }

        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json([
                'message' => 'Conversation not found',
                'error' => 'Not found'
            ], 404);
        }

        // Check if user is part of this conversation
        if ($conversation->association1_id !== $user->id && $conversation->association2_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Forbidden'
            ], 403);
        }

        $conversation->load([
            'association1',
            'association2',
            'lastSender',
            'messages' => function ($query) {
                $query->latest()->limit(100);
            }
        ]);

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $otherAssociation = $conversation->getOtherAssociation($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $conversation->id,
                'association1Id' => $conversation->association1_id,
                'association2Id' => $conversation->association2_id,
                'lastMessage' => $conversation->last_message,
                'lastMessageAt' => $conversation->last_message_at?->toIso8601String(),
                'lastSenderId' => $conversation->last_sender_id,
                'lastSenderName' => $conversation->lastSender?->name ?? 'Unknown',
                'otherAssociation' => [
                    'id' => $otherAssociation->id,
                    'name' => $otherAssociation->name,
                    'email' => $otherAssociation->email,
                    'code' => $otherAssociation->code,
                    'description' => $otherAssociation->description,
                ],
                'unreadCount' => 0,
                'createdAt' => $conversation->created_at->toIso8601String(),
                'updatedAt' => $conversation->updated_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json([
                'message' => 'Conversation not found',
                'error' => 'Not found'
            ], 404);
        }

        // Check if user is part of this conversation
        if ($conversation->association1_id !== $user->id && $conversation->association2_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Forbidden'
            ], 403);
        }

        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully',
        ]);
    }
}
