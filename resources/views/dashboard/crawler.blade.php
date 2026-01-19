@extends('layouts.app')

@section('title', 'Crawler')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Web Crawler</h1>
        <p class="mt-2 text-gray-600">Crawl websites and extract HTML elements containing specified keywords</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form id="crawler-form" method="POST" action="{{ route('crawler.start') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="site" class="block text-sm font-medium text-gray-700 mb-2">
                        Website URL
                    </label>
                    <input
                        type="url"
                        id="site"
                        name="site"
                        required
                        value="{{ old('site') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="https://example.com"
                    >
                    @error('site')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                        Keywords (comma-separated)
                    </label>
                    <input
                        type="text"
                        id="keywords"
                        name="keywords"
                        required
                        value="{{ old('keywords') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="keyword1, keyword2, keyword3"
                    >
                    <p class="mt-1 text-sm text-gray-500">Separate multiple keywords with commas</p>
                    @error('keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150"
                    >
                        Start Crawling
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    <div id="results-section" class="hidden bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Results</h2>
        <div id="results-content" class="space-y-4">
            <!-- Results will be displayed here -->
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-lg font-medium text-gray-900">Crawling website...</span>
        </div>
    </div>
</div>

<script>
document.getElementById('crawler-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = document.getElementById('submit-btn');
    const loading = document.getElementById('loading');
    const resultsSection = document.getElementById('results-section');
    const resultsContent = document.getElementById('results-content');
    
    // Show loading
    loading.classList.remove('hidden');
    submitBtn.disabled = true;
    resultsSection.classList.add('hidden');
    
    // Get form data
    const formData = new FormData(form);
    
    // Submit via fetch
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.add('hidden');
        submitBtn.disabled = false;
        
        if (data.matched && data.matched.length > 0) {
            resultsContent.innerHTML = '';
            
            data.matched.forEach((item, index) => {
                const resultCard = document.createElement('div');
                resultCard.className = 'border border-gray-200 rounded-lg p-4';
                resultCard.innerHTML = `
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Result ${index + 1}</span>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">${item.tag}</span>
                    </div>
                    <div class="mt-2 p-3 bg-gray-50 rounded border border-gray-200">
                        <div class="text-sm text-gray-600 mb-2">HTML Content:</div>
                        <div class="text-xs font-mono text-gray-800 overflow-x-auto">${item.html}</div>
                    </div>
                    ${Object.keys(item.attributes).length > 0 ? `
                        <div class="mt-2">
                            <div class="text-sm text-gray-600 mb-1">Attributes:</div>
                            <div class="text-xs font-mono text-gray-800">
                                ${JSON.stringify(item.attributes, null, 2)}
                            </div>
                        </div>
                    ` : ''}
                `;
                resultsContent.appendChild(resultCard);
            });
            
            resultsSection.classList.remove('hidden');
        } else if (data.message) {
            resultsContent.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                    ${data.message}
                </div>
            `;
            resultsSection.classList.remove('hidden');
        } else {
            resultsContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    An error occurred while crawling the website.
                </div>
            `;
            resultsSection.classList.remove('hidden');
        }
    })
    .catch(error => {
        loading.classList.add('hidden');
        submitBtn.disabled = false;
        
        resultsContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                Error: ${error.message}
            </div>
        `;
        resultsSection.classList.remove('hidden');
    });
});
</script>
@endsection


