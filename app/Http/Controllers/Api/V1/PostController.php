<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class PostController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters([
                'title',
                'status',
                'type',
                AllowedFilter::exact('user_id'),
            ])
            ->allowedIncludes(['author', 'taxonomies'])
            ->defaultSort('-created_at')
            ->paginate(request()->get('per_page', 10));

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $post = Post::create($data);

        if ($request->has('tags')) {
            $post->taxonomies()->sync($request->tags);
        }

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('featured_images');
        }

        return $this->okResponse(new PostResource($post), 'Post created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'taxonomies']);
        return $this->okResponse(new PostResource($post));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        if (!auth()->user()->can('edit-any-post') && auth()->id() !== $post->user_id) {
            return $this->errorResponse('You do not have permission to update this post', 403);
        }

        $post->update($request->validated());

        if ($request->has('tags')) {
            $post->taxonomies()->sync($request->tags);
        }

        if ($request->hasFile('image')) {
            $post->clearMediaCollection('featured_images');
            $post->addMediaFromRequest('image')->toMediaCollection('featured_images');
        }

        return $this->okResponse(new PostResource($post), 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $this->okResponse(null, 'Post moved to trash');
    }
}