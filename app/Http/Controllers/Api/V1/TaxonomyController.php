<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyResource;
use App\Models\Taxonomy;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $taxonomies = Taxonomy::when($request->type, function ($query, $type) {
            return $query->where('type', $type);
        })->get();

        return TaxonomyResource::collection($taxonomies);
    }

    public function tree()
    {
        $taxonomies = Taxonomy::get()->toTree();
        return TaxonomyResource::collection($taxonomies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:category,tag',
            'parent_id' => 'nullable|exists:taxonomies,id'
        ]);

        $taxonomy = Taxonomy::create($validated);
        return $this->okResponse(new TaxonomyResource($taxonomy), 'Taxonomy created', 201);
    }

    public function show(Taxonomy $taxonomy)
    {
        return $this->okResponse(new TaxonomyResource($taxonomy->load('children')));
    }

    public function update(Request $request, Taxonomy $taxonomy)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'parent_id' => 'nullable|exists:taxonomies,id'
        ]);

        $taxonomy->update($validated);
        return $this->okResponse(new TaxonomyResource($taxonomy), 'Taxonomy updated');
    }

    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->delete();
        return $this->okResponse(null, 'Taxonomy deleted');
    }
}