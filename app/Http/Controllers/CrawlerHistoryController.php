<?php

namespace App\Http\Controllers;

use App\Models\CrawlerHistory;
use Illuminate\Http\Request;

class CrawlerHistoryController extends Controller
{
    /**
     * Display a listing of the crawler history.
     */
    public function index(Request $request)
    {
        $query = CrawlerHistory::with(['user', 'apiKey'])->latest();

        // Filter by API key name
        if ($request->filled('api_key_name')) {
            $query->byApiKeyName($request->api_key_name);
        }

        // Filter by user name
        if ($request->filled('user_name')) {
            $query->byUserName($request->user_name);
        }

        // Filter by user role
        if ($request->filled('user_role')) {
            $query->byUserRole($request->user_role);
        }

        // Filter by specific date
        if ($request->filled('date') && !$request->filled('start_date') && !$request->filled('end_date')) {
            $query->byDate($request->date);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by keywords
        if ($request->filled('keywords')) {
            $query->byKeywords($request->keywords);
        }

        // Filter by website
        if ($request->filled('website')) {
            $query->byWebsite($request->website);
        }

        $history = $query->paginate(20)->withQueryString();

        return view('crawler-history.index', compact('history'));
    }
}


