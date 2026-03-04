<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumComment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumReplyController extends Controller
{
    /**
     * Store a newly created reply to a comment.
     */
    public function store(Request $request, string $commentId)
    {
        try {
            $comment = ForumComment::find($commentId);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            // Validate request
            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $reply = new ForumReply();
            $reply->forum_comment_id = $commentId;
            $reply->user_id = Auth::id();
            $reply->content = $validated['content'];
            $reply->likes = 0;
            $reply->save();

            // Create notification for the comment author
            $currentUser = Auth::user();
            if ($comment->user_id && $comment->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $comment->user_id,
                    'title' => 'Nouvelle réponse à votre commentaire',
                    'description' => $currentUser->name . ' a répondu à votre commentaire',
                    'subtitle' => '"' . substr($validated['content'], 0, 50) . '..."',
                    'icon' => 'forum',
                    'action_url' => '/forum/' . $comment->forum_post_id,
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reply created successfully',
                'data' => $this->formatReply($reply),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified reply.
     */
    public function update(Request $request, string $id)
    {
        try {
            $reply = ForumReply::find($id);

            if (!$reply) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reply not found',
                ], 404);
            }

            // Check authorization
            $user = Auth::user();
            if ($reply->user_id !== $user->id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this reply',
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $reply->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Reply updated successfully',
                'data' => $this->formatReply($reply),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified reply.
     */
    public function destroy(string $id)
    {
        try {
            $reply = ForumReply::find($id);

            if (!$reply) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reply not found',
                ], 404);
            }

            // Check authorization
            $user = Auth::user();
            if ($reply->user_id !== $user->id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this reply',
                ], 403);
            }

            $reply->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reply deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like a reply.
     */
    public function toggleLike(string $id)
    {
        try {
            $reply = ForumReply::find($id);

            if (!$reply) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reply not found',
                ], 404);
            }

            $reply->increment('likes');

            return response()->json([
                'success' => true,
                'message' => 'Reply liked successfully',
                'data' => ['likes' => $reply->likes],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format reply data
     */
    private function formatReply(ForumReply $reply)
    {
        $reply->load('user');

        return [
            'id' => $reply->id,
            'author' => $reply->user ? [
                'id' => $reply->user->id,
                'name' => $reply->user->name,
                'avatar' => $reply->user->avatar ?? null,
                'logo_path' => $reply->user->logo_path ?? null,
            ] : null,
            'content' => $reply->content,
            'likes' => $reply->likes,
            'createdAt' => $reply->created_at->toIso8601String(),
        ];
    }
}

