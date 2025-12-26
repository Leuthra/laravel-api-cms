<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies'])
            ->latest()
            ->get();

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $post->comments()->create([
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => auth()->id(),
            'status' => 'approved', 
        ]);

        return response()->json([
            'message' => 'Comment posted successfully',
            'data' => new CommentResource($comment->load('user'))
        ], 201);
    }
}