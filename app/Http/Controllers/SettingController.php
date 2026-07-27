<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        abort_if(!auth()->user()->can('setting-list'), 403, 'Unauthorized action.');

        $settings = [
            'app_title' => Setting::get('app_title', 'InApp Inventory Dashboard'),
            'app_logo' => Setting::get('app_logo'),
            'app_favicon' => Setting::get('app_favicon'),
            'primary_color' => Setting::get('primary_color', '#ea580c'),
            'primary_text_color' => Setting::get('primary_text_color', '#ffffff'),
        ];

        return view('settings.index', compact('settings'));
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        abort_if(!auth()->user()->can('setting-edit'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'app_title' => ['required', 'string', 'max:255'],
            'app_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'app_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,ico,webp', 'max:1024'],
            'primary_color' => ['required', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'primary_text_color' => ['required', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
        ]);

        Setting::set('app_title', $validated['app_title']);
        Setting::set('primary_color', $validated['primary_color']);
        Setting::set('primary_text_color', $validated['primary_text_color']);

        // Handle App Logo Upload
        if ($request->hasFile('app_logo')) {
            $existingLogo = Setting::get('app_logo');
            if ($existingLogo && str_contains($existingLogo, 'storage/settings/')) {
                $oldPath = 'settings/' . basename($existingLogo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $logoPath = $request->file('app_logo')->store('settings', 'public');
            Setting::set('app_logo', asset('storage/' . $logoPath));
        }

        // Handle App Favicon Upload
        if ($request->hasFile('app_favicon')) {
            $existingFavicon = Setting::get('app_favicon');
            if ($existingFavicon && str_contains($existingFavicon, 'storage/settings/')) {
                $oldFavPath = 'settings/' . basename($existingFavicon);
                if (Storage::disk('public')->exists($oldFavPath)) {
                    Storage::disk('public')->delete($oldFavPath);
                }
            }
            $favPath = $request->file('app_favicon')->store('settings', 'public');
            Setting::set('app_favicon', asset('storage/' . $favPath));
        }

        ActivityLogger::log('updated', 'Settings', 'Updated application settings', null, [
            'app_title' => $validated['app_title'],
            'primary_color' => $validated['primary_color'],
        ]);

        return redirect()->route('settings.index')->with('success', 'Application settings updated successfully.');
    }
}
