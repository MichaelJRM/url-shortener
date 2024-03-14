<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SafeBrowsingLookupService
{
    public function isUrlSafe(string $url): array
    {
        $apiKey = $this->_getApiKey();
        $response = Http::post("{$this->_getBaseUrl()}:find?key=$apiKey", [
            'client' => [
                'clientId' => 'Not a company',
                'clientVersion' => '1.0'
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
                'platformTypes' => ['ALL_PLATFORMS'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [
                    [
                        'url' => $url,
                    ],
                ]
            ]
        ]);

        if ($response->successful()) {
            return [!isset($response['matches']), false];
        } else {
            return [false, true];
        }
    }

    private function _getApiKey(): string
    {
        return env('GOOGLE_SAFE_BROWSING_API_KEY');
    }

    private function _getBaseUrl(): string
    {
        return "https://safebrowsing.googleapis.com/v4/threatMatches";
    }
}
