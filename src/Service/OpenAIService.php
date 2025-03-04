<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenAIService
{
    private Client $client;
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://openrouter.ai/api/v1/',
            'timeout'  => 30.0,
        ]);
        $this->apiKey = $apiKey; // API Key récupérée de .env
        $this->apiKey = $_ENV['OPENAI_API_KEY'];
        var_dump($this->apiKey); // Pour vérifier si la clé est correctement récupérée

    }
    public function askAI(string $question): ?string
    {
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'mistralai/mistral-7b-instruct', // ✅ Modèle valide sur OpenRouter
                    'messages' => [
                        ['role' => 'system', 'content' => "Tu es un expert en agriculture et tu donnes des conseils précis aux agriculteurs."],
                        ['role' => 'user', 'content' => $question]
                    ],
                ],
            ]);
    
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['choices'][0]['message']['content'] ?? null;
        } catch (RequestException $e) {
            return 'Erreur : ' . $e->getMessage();
        }
    }
    
}
