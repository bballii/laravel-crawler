<?php

namespace App\Http\Controllers;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    public function startCrawler(Request $request)
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $site = $request->get('site');
        $keywords = explode(',', $request->get('keywords'));

        $response = file_get_contents("https://www.$site", false, stream_context_create($arrContextOptions));
        if ($response) {
            $keywords_exist = [];
            foreach ($keywords as $word) {
                if (str_contains($response, str_replace(' ', '', $word))) {
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
                        if (!in_array($html, $results)) {
                            // Get the tag name and attributes
                            $tagName = $parentNode->nodeName;
                            $attributes = [];
                            if ($parentNode->hasAttributes()) {
                                foreach ($parentNode->attributes as $attr) {
                                    $attributes[$attr->nodeName] = $attr->nodeValue;
                                }
                            }

                            $results[] = [
                                'html' => $html,
                                'tag' => $tagName,
                                'attributes' => $attributes
                            ];
                        }
                    }
                }

                return response()->json(['matched' => $results], 200);
            } else {
                return response()->json(['message' => "keyword not found on the page $site"], 404);
            }
        }

        dd("NULL");
    }
}
