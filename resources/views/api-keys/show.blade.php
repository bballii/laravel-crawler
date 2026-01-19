@extends('layouts.app')

@section('title', 'API Key Created')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">API Key Created</h1>
        <p class="mt-2 text-gray-600">Your API key has been generated. Copy it now as it won't be shown again.</p>
    </div>

    @if($plainKey)
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Important: Save Your API Key</h3>
                    <p class="text-yellow-700 mb-4">This is the only time you'll be able to see this API key. Make sure to copy it and store it securely.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                <div class="flex items-center space-x-2">
                    <input
                        type="text"
                        id="api-key-input"
                        value="{{ $plainKey }}"
                        readonly
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 font-mono text-sm"
                    >
                    <button
                        type="button"
                        onclick="copyApiKey()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition"
                    >
                        Copy
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-2">Usage Example:</h4>
                <div class="bg-gray-900 rounded p-3 overflow-x-auto">
                    <code class="text-green-400 text-sm">
                        curl -X POST {{ url('/api/crawler') }} \<br>
                        &nbsp;&nbsp;-H "X-API-Key: {{ $plainKey }}" \<br>
                        &nbsp;&nbsp;-H "Content-Type: application/json" \<br>
                        &nbsp;&nbsp;-d '{"site":"https://example.com","keywords":"keyword1, keyword2"}'
                    </code>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">API Key Details</h3>
                <p class="mt-1 text-sm text-gray-500">Key Name: {{ $apiKey->name }}</p>
                <p class="mt-1 text-sm text-gray-500">Created: {{ $apiKey->created_at->format('M d, Y H:i') }}</p>
                <p class="mt-4 text-sm text-red-600">The API key value is no longer available. If you need it, please create a new key.</p>
            </div>
        </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('api-keys.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
            Back to API Keys
        </a>
    </div>
</div>

<script>
function copyApiKey() {
    const input = document.getElementById('api-key-input');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        alert('API key copied to clipboard!');
    } catch (err) {
        alert('Failed to copy. Please select and copy manually.');
    }
}
</script>
@endsection
