<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnsplashService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function getRandomImage($query = 'machine de recyclage')
    {
        $response = $this->client->request('GET', 'https://api.unsplash.com/photos/random', [
            'query' => [
                'query' => $query,
                'client_id' => $this->apiKey,
            ],
        ]);

        $data = $response->toArray();
        return $data['urls']['regular'] ?? 'https://via.placeholder.com/300';
    }
}
