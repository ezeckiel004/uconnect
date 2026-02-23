<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ForumPostController extends Controller
{
    /**
     * Display a listing of the forum posts with comments and replies.
     */
    public function index(Request $request)
    {
        try {
            $category = $request->query('category');
            $query = ForumPost::with(['user', 'comments.user', 'comments.replies.user']);

            if ($category && $category !== 'Tous') {
                $query->where('category', $category);
            }

            $posts = $query->latest()->get()->map(function ($post) {
                return $this->formatPost($post);
            });

            return response()->json([
                'success' => true,
                'data' => $posts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch forum posts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created forum post (only by associations).
     */
    public function store(Request $request)
    {
        try {
            // Check if user is association
            $user = Auth::user();
            if (!$user || $user->type !== 'association') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only associations can create forum posts',
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|in:Idées,Ressources,Logistique',
                'file' => 'nullable|file|max:10240', // 10MB max
            ]);

            $post = new ForumPost();
            $post->user_id = $user->id;
            $post->title = $validated['title'];
            $post->description = $validated['description'];
            $post->category = $validated['category'];
            $post->likes = 0;
            $post->views = 0;

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('forum_files', $fileName, 'public');
                
                $post->file_name = $file->getClientOriginalName();
                $post->file_path = Storage::url($filePath);
                $post->file_size = $file->getSize();
            }

            $post->save();

            return response()->json([
                'success' => true,
                'message' => 'Forum post created successfully',
                'data' => $this->formatPost($post),
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
                'message' => 'Failed to create forum post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified forum post with its comments and replies.
     */
    public function show(string $id)
    {
        try {
            $post = ForumPost::with(['user', 'comments' => function ($query) {
                $query->with('user', 'replies.user');
            }])->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forum post not found',
                ], 404);
            }

            // Increment views
            $post->increment('views');

            return response()->json([
                'success' => true,
                'data' => $this->formatPost($post),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch forum post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified forum post (only by creator or admin).
     */
    public function update(Request $request, string $id)
    {
        try {
            $post = ForumPost::find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forum post not found',
                ], 404);
            }

            // Check authorization
            $user = Auth::user();
            if ($post->user_id !== $user->id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this post',
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'category' => 'sometimes|required|in:Idées,Ressources,Logistique',
            ]);

            $post->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Forum post updated successfully',
                'data' => $this->formatPost($post),
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
                'message' => 'Failed to update forum post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified forum post (only by creator or admin).
     */
    public function destroy(string $id)
    {
        try {
            $post = ForumPost::find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forum post not found',
                ], 404);
            }

            // Check authorization
            $user = Auth::user();
            if ($post->user_id !== $user->id && $user->type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this post',
                ], 403);
            }

            // Delete file if exists
            if ($post->file_path) {
                Storage::disk('public')->delete($post->file_path);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Forum post deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete forum post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like or unlike a forum post.
     */
    public function toggleLike(string $id)
    {
        try {
            $post = ForumPost::find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forum post not found',
                ], 404);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Check if user already liked this post
            $liked = $post->likedByUsers()->where('user_id', $user->id)->exists();

            if ($liked) {
                // Unlike: remove the like
                $post->likedByUsers()->detach($user->id);
            } else {
                // Like: add the like
                $post->likedByUsers()->attach($user->id);
            }

            // Update likes count from the database
            $post->likes = $post->likedByUsers()->count();
            $post->save();

            return response()->json([
                'success' => true,
                'message' => $liked ? 'Post unliked successfully' : 'Post liked successfully',
                'data' => $this->formatPost($post),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle like on post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download file attached to a forum post.
     */
    public function downloadFile(string $id)
    {
        try {
            $post = ForumPost::find($id);

            if (!$post || !$post->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                ], 404);
            }

            // Extract filename from path
            $filePath = str_replace('/storage/', '', $post->file_path);
            
            return response()->download(storage_path('app/public/' . $filePath), $post->file_name);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format post data for response
     */
    private function formatPost(ForumPost $post)
    {
        $user = Auth::user();
        $isLiked = $user ? $post->likedByUsers()->where('user_id', $user->id)->exists() : false;

        return [
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            'category' => $post->category,
            'author' => $post->user ? [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'avatar' => $post->user->avatar ?? null,
            ] : null,
            'likes' => $post->likes,
            'views' => $post->views,
            'isLiked' => $isLiked,
            'fileName' => $post->file_name,
            'filePath' => $post->file_path,
            'fileSize' => $post->file_size,
            'createdAt' => $post->created_at->toIso8601String(),
            'updatedAt' => $post->updated_at->toIso8601String(),
            'comments' => $post->comments ? $post->comments->map(function ($comment) {
                return $this->formatComment($comment);
            })->values()->toArray() : [],
        ];
    }

    /**
     * Format comment data
     */
    private function formatComment(ForumComment $comment)
    {
        return [
            'id' => $comment->id,
            'author' => $comment->user ? [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'avatar' => $comment->user->avatar ?? null,
            ] : null,
            'content' => $comment->content,
            'likes' => $comment->likes,
            'createdAt' => $comment->created_at->toIso8601String(),
            'replies' => $comment->replies ? $comment->replies->map(function ($reply) {
                return $this->formatReply($reply);
            })->values()->toArray() : [],
        ];
    }

    /**
     * Format reply data
     */
    private function formatReply(ForumReply $reply)
    {
        return [
            'id' => $reply->id,
            'author' => $reply->user ? [
                'id' => $reply->user->id,
                'name' => $reply->user->name,
                'avatar' => $reply->user->avatar ?? null,
            ] : null,
            'content' => $reply->content,
            'likes' => $reply->likes,
            'createdAt' => $reply->created_at->toIso8601String(),
        ];
    }
}

