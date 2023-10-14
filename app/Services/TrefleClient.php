<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TrefleClient
{
    private const URI = 'https://trefle.io/api/v1';
    private const KINGDOMS_ENDPOINT = '/kingdoms';
    private const SUBKINGDOMS_ENDPOINT = '/subkingdoms';
    private const PHYLUMS_ENDPOINT = '/divisions';
    private const CLASSES_ENDPOINT = '/division_classes';
    private const ORDERS_ENDPOINT = '/division_orders';
    private const FAMILIES_ENDPOINT = '/families';
    private const GENERA_ENDPOINT = '/genus';
    // private const PLANTS_ENDPOINT = '/plants';

    private const SEARCH_ENDPOINT = '/plants/search';

    private string $token;
    
    public function __construct()
    {
        $this->token = config('services.trefle.token');
    }

    public function getKingdoms(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::KINGDOMS_ENDPOINT);

        return $response->json();
    }

    public function getSubkingdoms(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::SUBKINGDOMS_ENDPOINT);

        return $response->json();
    }

    public function getPhylums(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::PHYLUMS_ENDPOINT);

        return $response->json();
    }

    public function getClasses(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::CLASSES_ENDPOINT);

        return $response->json();
    }

    public function getOrders(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::ORDERS_ENDPOINT);

        return $response->json();
    }

    public function getFamilies(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::FAMILIES_ENDPOINT);

        return $response->json();
    }

    public function getGenera(int $page = 1)
    {
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
        ];

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::GENERA_ENDPOINT);

        return $response->json();
    }

    // TODO: easier way for serializing queryparams for request
    // TODO: convert datatypes to string correctly when serializing queryparams
    public function getFlowers(string $query = '', bool $edible = FALSE, bool $vegetable = FALSE, string $scientificName = '', int $growthMonths = 0, int $bloomMonths = 0, string $color = '', int $page = 1)
    {

        // TODO: validate that we're passing the correct number/type of params (i.e. non-empty q, non-empty other params? depends on api spec)
        $queryParams = [
            'token' => $this->token,
            'page' => $page,
            'filter[edible]' => $edible ? 'true' : 'false',
            'filter[vegetable]' => $vegetable ? 'true' : 'false',
        ];

        // Set the rest of the params
        if ($query) {
            $queryParams['q'] = $query;
        }

        if ($scientificName) {
            $queryParams['filter[scientific_name]'] = $scientificName;
        }

        if ($growthMonths) {
            $queryParams['filter[growth_months]'] = $growthMonths;
        }

        if ($bloomMonths) {
            $queryParams['filter[bloom_months]'] = $bloomMonths;
        }
        
        if ($color) {
            $queryParams['filter[flower_color]'] = $color;
        }

        $response = Http::withQueryParameters($queryParams)->get(TrefleClient::URI.TrefleClient::SEARCH_ENDPOINT);

        return $response->json();
    }
}
