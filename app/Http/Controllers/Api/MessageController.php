<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\MessageSentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Only associations can send messages
        if ($user->type !== 'association') {
            return response()->json([
                'message' => 'Only associations can send messages',
                'error' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
            'body' => 'required|string|max:5000',
        ]);

        $conversation = Conversation::find($validated['conversation_id']);

        // Check if user is part of this conversation
        if ($conversation->association1_id !== $user->id && $conversation->association2_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Forbidden'
            ], 403);
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $validated['conversation_id'],
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        // Update conversation's last message
        $conversation->update([
            'last_message' => $validated['body'],
            'last_message_at' => now(),
            'last_sender_id' => $user->id,
        ]);

        // Get the other association
        $otherAssociation = $conversation->getOtherAssociation($user->id);

        // Send email notification to the other association
        if ($otherAssociation) {
            $otherAssociation->notify(new MessageSentNotification($message));
        }

        $message->load('sender');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id,
                'conversationId' => $message->conversation_id,
                'senderId' => $message->sender_id,
                'body' => $message->body,
                'isRead' => (bool) $message->is_read,
                'readAt' => $message->read_at?->toIso8601String(),
                'createdAt' => $message->created_at->toIso8601String(),
                'updatedAt' => $message->updated_at->toIso8601String(),
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'email' => $message->sender->email,
                    'code' => $message->sender->code,
                    'description' => $message->sender->description,
                ],
            ],
        ], 201);
    }

    /**
     * Display messages of a conversation.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Only associations can view messages
        if ($user->type !== 'association') {
            return response()->json([
                'message' => 'Only associations can view messages',
                'error' => 'Unauthorized'
            ], 403);
        }

        $conversationId = $request->get('conversation_id');

        if (!$conversationId) {
            return response()->json([
                'message' => 'conversation_id is required',
                'error' => 'Bad request'
            ], 400);
        }

        $conversation = Conversation::find($conversationId);

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

        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'conversationId' => $message->conversation_id,
                    'senderId' => $message->sender_id,
                    'body' => $message->body,
                    'isRead' => (bool) $message->is_read,
                    'readAt' => $message->read_at?->toIso8601String(),
                    'createdAt' => $message->created_at->toIso8601String(),
                    'updatedAt' => $message->updated_at->toIso8601String(),
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'email' => $message->sender->email,
                        'code' => $message->sender->code,
                        'description' => $message->sender->description,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $messages,
            'count' => $messages->count(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        $message = Message::find($id);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found',
                'error' => 'Not found'
            ], 404);
        }

        // Only the sender can delete their message
        if ($message->sender_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Forbidden'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }
}
