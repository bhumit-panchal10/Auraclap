<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenAIImageService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ],
        ]);
    }
    
    public function generateImage($prompt, $size = '1024x1024', $n = 1)
    {
        try {
            $response = $this->client->post('images/generations', [
                'json' => [
                    'prompt' => $prompt,
                    'n' => $n,
                    'size' => $size,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Return the generated image URLs
            return array_column($data['data'], 'url');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error generating image: ' . $e->getMessage());
            \Log::error('Error generating image details: ' . $e->getTraceAsString());

            // Handle the billing limit error gracefully for unpaid users
            if (strpos($e->getMessage(), 'billing_hard_limit_reached') !== false) {
                // Return a response indicating the billing limit has been reached
                return response()->json(['error' => 'Billing limit reached. Please check your OpenAI account.'], 402); // 402 Payment Required
            }

            return response()->json(['error' => 'Failed to generate images'], 500);
        }
    }

  



}
