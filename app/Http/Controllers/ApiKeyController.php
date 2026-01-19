<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of the API keys.
     */
    public function index()
    {
        $apiKeys = auth()->user()->apiKeys()->latest()->get();
        
        return view('api-keys.index', compact('apiKeys'));
    }

    /**
     * Show the form for creating a new API key.
     */
    public function create()
    {
        return view('api-keys.create');
    }

    /**
     * Store a newly created API key in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $key = ApiKey::generate();
        $hashedKey = hash('sha256', $key);

        $apiKey = auth()->user()->apiKeys()->create([
            'name' => $request->name,
            'key' => $hashedKey,
            'expires_at' => $request->expires_at,
        ]);

        // Store the plain key in session to show once
        session()->flash('api_key_plain', $key);
        session()->flash('api_key_id', $apiKey->id);

        return redirect()->route('api-keys.show', $apiKey)->with('success', 'API key created successfully. Please copy it now as it will not be shown again.');
    }

    /**
     * Display the specified API key.
     */
    public function show(ApiKey $apiKey)
    {
        // Ensure the API key belongs to the authenticated user
        if ($apiKey->user_id !== auth()->id()) {
            abort(403);
        }

        $plainKey = session('api_key_plain');
        $keyId = session('api_key_id');

        // Clear the session after showing
        if ($keyId === $apiKey->id) {
            session()->forget(['api_key_plain', 'api_key_id']);
        }

        return view('api-keys.show', compact('apiKey', 'plainKey'));
    }

    /**
     * Remove the specified API key from storage.
     */
    public function destroy(ApiKey $apiKey)
    {
        // Ensure the API key belongs to the authenticated user
        if ($apiKey->user_id !== auth()->id()) {
            abort(403);
        }

        $apiKey->delete();

        return redirect()->route('api-keys.index')->with('success', 'API key deleted successfully.');
    }
}
