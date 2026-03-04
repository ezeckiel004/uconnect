<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCommentController extends Controller
{
    /**
     * Store a newly created comment in a forum post.
     */
    public function store(Request $request, string $postId)
    {
        try {
            $post = ForumPost::find($postId);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forum post not found',
                ], 404);
            }

            // Validate request
            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $comment = new ForumComment();
            $comment->forum_post_id = $postId;
            $comment->user_id = Auth::id();
            $comment->content = $validated['content'];
            $comment->likes = 0;
            $comment->save();

            // Create notification for the post author
            $currentUser = Auth::user();
            if ($post->user_id && $post->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $post->user_id,
                    'title' => 'Nouveau commentaire sur votre sujet',
                    'description' => $currentUser->name . ' a commenté votre sujet "' . $post->title . '"',
                    'subtitle' => '"' . substr($validated['content'], 0, 50) . '..."',
                    'icon' => 'forum',
                    'action_url' => '/forum/' . $postId,
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully',
                'data' => $this->formatComment($comment),
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
                'message' => 'Failed to create comment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, string $id)
    {
        try {
            $comment = ForumComment::find($id);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            // Check authorization
            $user = Auth::user();
            if ($comment->user_id !== $user->id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this comment',
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $comment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'data' => $this->formatComment($comment),
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
                'message' => 'Failed to update comment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(string $id)
    {
        try {
            $comment = ForumComment::find($id);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            // Check authorization
            $user = Auth::user();
            if ($comment->user_id !== $user->id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this comment',
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like a comment.
     */
    public function toggleLike(string $id)
    {
        try {
            $comment = ForumComment::find($id);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            $comment->increment('likes');

            return response()->json([
                'success' => true,
                'message' => 'Comment liked successfully',
                'data' => ['likes' => $comment->likes],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like comment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format comment data
     */
    private function formatComment(ForumComment $comment)
    {
        $comment->load(['user', 'replies.user']);

        return [
            'id' => $comment->id,
            'author' => $comment->user ? [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'avatar' => $comment->user->avatar ?? null,
                'logo_path' => $comment->user->logo_path ?? null,
            ] : null,
            'content' => $comment->content,
            'likes' => $comment->likes,
            'createdAt' => $comment->created_at->toIso8601String(),
            'replies' => $comment->replies ? $comment->replies->map(function ($reply) {
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
            })->values()->toArray() : [],
        ];
    }
}

