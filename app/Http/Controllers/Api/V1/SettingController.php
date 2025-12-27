<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->query('group');

        $cacheKey = $group ? "settings_{$group}" : "settings_all";

        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $query = Setting::query();
            
            if ($group) {
                $query->where('group', $group);
            }

            return $query->pluck('value', 'key');
        });
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'group' => 'nullable|string'
        ]);

        foreach ($request->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $request->group ?? 'general'
                ]
            );
        }

        if ($request->has('group')) {
            Cache::forget("settings_{$request->group}");
        }
        Cache::forget("settings_all"); 

        return response()->json(['message' => 'Settings updated successfully']);
    }
}