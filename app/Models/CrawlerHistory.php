<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlerHistory extends Model
{
    use HasFactory;

    protected $table = 'crawler_history';

    protected $fillable = [
        'user_id',
        'api_key_id',
        'site',
        'keywords',
        'matches_count',
        'status',
        'execution_time',
        'response_message',
    ];

    protected $casts = [
        'keywords' => 'array',
        'execution_time' => 'integer',
        'matches_count' => 'integer',
    ];

    /**
     * Get the user that made the crawler request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the API key used for the request (if any).
     */
    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Scope to filter by API key name.
     */
    public function scopeByApiKeyName($query, $name)
    {
        return $query->whereHas('apiKey', function ($q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
    }

    /**
     * Scope to filter by user name.
     */
    public function scopeByUserName($query, $name)
    {
        return $query->whereHas('user', function ($q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
    }

    /**
     * Scope to filter by user role.
     */
    public function scopeByUserRole($query, $role)
    {
        return $query->whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    /**
     * Scope to filter by specific date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ]);
    }

    /**
     * Scope to filter by keywords.
     */
    public function scopeByKeywords($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            // Search in JSON array - convert to text and search
            // This works by searching the JSON string representation
            $q->whereRaw('keywords LIKE ?', ["%{$keyword}%"]);
        });
    }

    /**
     * Scope to filter by website/domain.
     */
    public function scopeByWebsite($query, $website)
    {
        return $query->where('site', 'like', "%{$website}%");
    }
}


