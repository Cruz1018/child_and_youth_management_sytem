<?php

namespace Gemini;

class Client
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function generateContent($prompt)
    {
        $data = json_encode([
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ]
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "?key=" . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception("cURL Error: " . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}
