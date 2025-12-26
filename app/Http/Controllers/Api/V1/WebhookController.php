<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index()
    {
        return response()->json(Webhook::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'event' => 'required|string',
            'secret' => 'nullable|string',
            'headers' => 'nullable|array',
        ]);

        $webhook = Webhook::create($validated);

        return response()->json($webhook, 201);
    }

    public function destroy(Webhook $webhook)
    {
        $webhook->delete();
        return response()->json(null, 204);
    }
}