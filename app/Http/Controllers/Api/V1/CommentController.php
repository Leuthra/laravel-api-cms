<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $query = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest();

        if (!auth()->user()?->can('manage-posts')) {
            $query->where('status', 'approved');
        }

        $comments = $query->paginate(20);

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $status = auth()->user()?->can('manage-posts') ? 'approved' : 'pending';

        $comment = $post->comments()->create([
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => auth()->id(),
            'status' => $status,
        ]);

        $message = $status === 'pending' 
            ? 'Comment submitted for moderation.' 
            : 'Comment posted successfully.';

        return response()->json([
            'message' => $message,
            'data' => new CommentResource($comment->load('user'))
        ], 201);
    }

    public function update(Request $request, \App\Models\Comment $comment)
    {
        if (!auth()->user()->can('manage-posts')) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,pending,spam',
            'body'   => 'nullable|string'
        ]);

        $comment->update($validated);

        return response()->json(['message' => 'Comment updated', 'data' => new CommentResource($comment)]);
    }

    public function destroy(Comment $comment)
    {
        if (!auth()->user()->can('manage-posts')) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted'], 204);
    }
}