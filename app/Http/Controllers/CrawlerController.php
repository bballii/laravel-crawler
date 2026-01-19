<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\CrawlerHistory;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CrawlerController extends Controller
{
    public function startCrawler(Request $request)
    {
        $startTime = microtime(true);
        
        $request->validate([
            'site' => 'required|url',
            'keywords' => 'required|string',
        ]);

        $user = auth()->user();
        $apiKey = null;
        $apiKeyId = null;

        // Check if request came via API key
        $apiKeyHeader = $request->header('X-API-Key') ?? $request->query('api_key');
        if ($apiKeyHeader) {
            $hashedKey = hash('sha256', $apiKeyHeader);
            $apiKey = ApiKey::where('key', $hashedKey)->first();
            if ($apiKey) {
                $apiKeyId = $apiKey->id;
                $user = $apiKey->user;
            }
        }

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $site = $request->get('site');
        $keywordsInput = $request->get('keywords');
        $keywords = array_map('trim', explode(',', $keywordsInput));
        $keywords = array_filter($keywords); // Remove empty values

        $status = 'success';
        $matchesCount = 0;
        $responseMessage = null;
        $responseData = null;

        if (empty($keywords)) {
            $executionTime = (int)((microtime(true) - $startTime) * 1000);
            $this->logHistory($user, $apiKeyId, $site, $keywords, 0, 'error', $executionTime, 'Please provide at least one keyword');
            return response()->json(['message' => 'Please provide at least one keyword'], 400);
        }

        $response = @file_get_contents($site, false, stream_context_create($arrContextOptions));
        if ($response === false) {
            $executionTime = (int)((microtime(true) - $startTime) * 1000);
            $this->logHistory($user, $apiKeyId, $site, $keywords, 0, 'error', $executionTime, 'Failed to fetch the website. Please check the URL.');
            return response()->json(['message' => 'Failed to fetch the website. Please check the URL.'], 400);
        }

        if ($response) {
            $keywords_exist = [];

            foreach ($keywords as $word) {
                $pattern = '/\b' . preg_quote($word, '/') . '\b/i';

                if (preg_match($pattern, $response)) {
                    $keywords_exist[] = $word;
                }
            }

            if (count($keywords_exist) > 0) {
                $dom = new DOMDocument;

                libxml_use_internal_errors(true);
                $dom->loadHTML($response);
                libxml_clear_errors();

                $xpath = new DOMXPath($dom);

                $results = [];
                foreach ($keywords_exist as $keyword) {
                    $query = "//text()[contains(., '" . $keyword . "')]";
                    $textNodes = $xpath->query($query);
                    foreach ($textNodes as $textNode) {
                        $parentNode = $textNode->parentNode;

                        // Remove any image tags from the parent node
                        $images = $parentNode->getElementsByTagName('img');
                        while ($images->length > 0) {
                            $img = $images->item(0);
                            $img->parentNode->removeChild($img);
                        }

                        $html = $dom->saveHTML($parentNode);

                        // Use the HTML content as key to avoid duplicates
                        if (!isset($results[$html])) {
                            // Get the tag name and attributes
                            $tagName = $parentNode->nodeName;
                            $attributes = [];
                            if ($parentNode->hasAttributes()) {
                                foreach ($parentNode->attributes as $attr) {
                                    $attributes[$attr->nodeName] = $attr->nodeValue;
                                }
                            }

                            $results[$html] = [
                                'html' => $html,
                                'tag' => $tagName,
                                'attributes' => $attributes
                            ];
                        }
                    }
                }

                // Convert associative array to sequential array for JSON response
                $results = array_values($results);
                $matchesCount = count($results);
                $executionTime = (int)((microtime(true) - $startTime) * 1000);
                
                $this->logHistory($user, $apiKeyId, $site, $keywords, $matchesCount, 'success', $executionTime, null);

                return response()->json(['matched' => $results], 200);
            } else {
                $executionTime = (int)((microtime(true) - $startTime) * 1000);
                $message = "No keywords found on the page $site";
                $this->logHistory($user, $apiKeyId, $site, $keywords, 0, 'error', $executionTime, $message);
                return response()->json(['message' => $message], 404);
            }
        }

        $executionTime = (int)((microtime(true) - $startTime) * 1000);
        $this->logHistory($user, $apiKeyId, $site, $keywords, 0, 'error', $executionTime, 'Failed to process the website');
        return response()->json(['message' => 'Failed to process the website'], 500);
    }

    /**
     * Log crawler history.
     */
    private function logHistory($user, $apiKeyId, $site, $keywords, $matchesCount, $status, $executionTime, $responseMessage)
    {
        if (!$user) {
            return; // Don't log if no user
        }

        CrawlerHistory::create([
            'user_id' => $user->id,
            'api_key_id' => $apiKeyId,
            'site' => $site,
            'keywords' => $keywords,
            'matches_count' => $matchesCount,
            'status' => $status,
            'execution_time' => $executionTime,
            'response_message' => $responseMessage,
        ]);
    }
}
