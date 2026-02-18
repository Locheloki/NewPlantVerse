<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class GeminiService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->client = new Client();
    }

    /**
     * Identify a plant from a photo
     *
     * @param string $photoDataUri Base64 encoded photo data URI
     * @return array Plant identification results
     */
    public function identifyPlant(string $photoDataUri): array
    {
        $imageData = $this->extractBase64FromDataUri($photoDataUri);

        $prompt = "Identify the plant in this image and provide:
1. The species name
2. Common name
3. Care recommendations

Respond in JSON format with keys: species, commonName, careRecommendations";

        return $this->generateContent(
            'gemini-2.5-flash',
            [
                'parts' => [
                    [
                        'text' => $prompt,
                    ],
                    [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data' => $imageData,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Get personalized care advice for a plant
     *
     * @param string $plantSpecies The species of the plant
     * @param string $userQuestion The user's question about care
     * @param string $location The user's location
     * @param string $environmentalFactors Environmental conditions
     * @return array Care advice
     */
    public function getPersonalizedCareAdvice(
        string $plantSpecies,
        string $userQuestion,
        string $location,
        string $environmentalFactors
    ): array {
        $prompt = "You are a helpful plant care assistant. Use the following information to provide personalized care advice to the user.

Plant Species: {$plantSpecies}
User Question: {$userQuestion}
Location: {$location}
Environmental Factors: {$environmentalFactors}

Provide detailed and specific advice based on the user's question, plant species, location, and environmental factors. Respond in JSON format with key: advice";

        return $this->generateContent(
            'gemini-2.5-flash',
            [
                'parts' => [
                    [
                        'text' => $prompt,
                    ],
                ],
            ]
        );
    }

    /**
     * Generate content using Gemini API
     *
     * @param string $model The model to use
     * @param array $requestBody The request body
     * @return array The API response
     */
    protected function generateContent(string $model, array $requestBody): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'GEMINI_API_KEY is not configured. Please add your API key to the .env file.',
            ];
        }

        try {
            $response = $this->client->post(
                "{$this->baseUrl}/{$model}:generateContent",
                [
                    'query' => ['key' => $this->apiKey],
                    'json' => $requestBody,
                    'timeout' => 30,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $data['candidates'][0]['content']['parts'][0]['text'];
                // Try to extract JSON from the response
                return $this->parseJsonFromText($text);
            }

            return [
                'success' => false,
                'error' => 'No content in response',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract base64 data from a data URI
     *
     * @param string $dataUri The data URI string
     * @return string Base64 encoded data
     */
    protected function extractBase64FromDataUri(string $dataUri): string
    {
        if (strpos($dataUri, 'data:') !== 0) {
            return $dataUri;
        }

        $parts = explode(',', $dataUri, 2);
        return $parts[1] ?? '';
    }

    /**
     * Parse JSON from text response
     *
     * @param string $text The text to parse
     * @return array Parsed data
     */
    protected function parseJsonFromText(string $text): array
    {
        // Try to parse as JSON directly
        $json = json_decode($text, true);
        if ($json) {
            return ['success' => true, 'data' => $json];
        }

        // Try to extract JSON from the text
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $json = json_decode($matches[0], true);
            if ($json) {
                return ['success' => true, 'data' => $json];
            }
        }

        // Return the raw text
        return ['success' => true, 'data' => ['response' => $text]];
    }
}
