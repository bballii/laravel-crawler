@extends('layouts.app')

@section('title', 'Crawler History')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Crawler History</h1>
        <p class="mt-2 text-gray-600">View and filter crawler usage history</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('crawler-history.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- API Key Name -->
                <div>
                    <label for="api_key_name" class="block text-sm font-medium text-gray-700 mb-2">
                        API Key Name
                    </label>
                    <input
                        type="text"
                        id="api_key_name"
                        name="api_key_name"
                        value="{{ request('api_key_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by API key name"
                    >
                </div>

                <!-- User Name -->
                <div>
                    <label for="user_name" class="block text-sm font-medium text-gray-700 mb-2">
                        User Name
                    </label>
                    <input
                        type="text"
                        id="user_name"
                        name="user_name"
                        value="{{ request('user_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by user name"
                    >
                </div>

                <!-- User Role -->
                <div>
                    <label for="user_role" class="block text-sm font-medium text-gray-700 mb-2">
                        User Role
                    </label>
                    <select
                        id="user_role"
                        name="user_role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Roles</option>
                        <option value="user" {{ request('user_role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="super_admin" {{ request('user_role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                <!-- Keywords -->
                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                        Keywords
                    </label>
                    <input
                        type="text"
                        id="keywords"
                        name="keywords"
                        value="{{ request('keywords') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by keyword used"
                    >
                    <p class="mt-1 text-xs text-gray-500">Search for keywords that were used in scraping</p>
                </div>

                <!-- Website -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website
                    </label>
                    <input
                        type="text"
                        id="website"
                        name="website"
                        value="{{ request('website') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., amazon, google"
                    >
                    <p class="mt-1 text-xs text-gray-500">Search by website domain or name</p>
                </div>

                <!-- Specific Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Specific Date
                    </label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <p class="mt-1 text-xs text-gray-500">Leave empty if using date range</p>
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date
                    </label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date
                    </label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
            </div>

            <div class="flex items-center space-x-4 pt-4">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition"
                >
                    Apply Filters
                </button>
                <a
                    href="{{ route('crawler-history.index') }}"
                    class="text-gray-600 hover:text-gray-900 font-medium"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($history->isEmpty())
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No history found</h3>
                <p class="mt-1 text-sm text-gray-500">No crawler history matches your filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API Key</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keywords</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matches</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time (ms)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($history as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $entry->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $entry->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entry->user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $entry->user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $entry->apiKey ? $entry->apiKey->name : 'Web Dashboard' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <a href="{{ $entry->site }}" target="_blank" class="text-blue-600 hover:text-blue-800 truncate block max-w-xs" title="{{ $entry->site }}">
                                        {{ \Illuminate\Support\Str::limit($entry->site, 40) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="max-w-xs">
                                        @foreach($entry->keywords as $keyword)
                                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                {{ $keyword }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $entry->matches_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $entry->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($entry->execution_time) }}ms
                                </td>
                            </tr>
                            @if($entry->response_message)
                                <tr>
                                    <td colspan="9" class="px-6 py-2 bg-red-50 text-sm text-red-700">
                                        <strong>Error:</strong> {{ $entry->response_message }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

