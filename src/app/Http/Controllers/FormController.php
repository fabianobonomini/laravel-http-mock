<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class FormController extends Controller
{
    protected $client;

    public function __construct(Client $client) 
    {
        $this->client = $client;
    }

    public function showForm()
    {
        return view('form');
    }

    public function submitForm(Request $request)
{
    $url = $request->input('url');
    $payload = json_encode(['key' => 'value']); // Example payload
    try {
        $response = $this->client->post($url, [
            'json' => $payload,
            'headers' => ['Content-Type' => 'application/json']
        ]);
        Log::info('This is an info message'. $response->getBody());
        return redirect('/form')->with([
            'success' => 'Form submitted successfully!',
            // 'response' => $response->getBody()
        ]);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle error
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();

            // Handle 4xx client errors
            if ($statusCode >= 400 && $statusCode < 500) {
                Log::error('This is an 4XX message');
                return redirect('/form')->withErrors([
                    'error' => 'Client error occurred',
                    // 'message' => $e->getMessage(),
                    'status_code' => $statusCode
                ]);
            }

            // Handle 5xx server errors
            if ($statusCode >= 500) {
                Log::error('This is an 5XX message');
                return redirect('/form')->withErrors([
                    'error' => 'Server error occurred',
                    //'message' => $e->getMessage(),
                    'status_code' => $statusCode
                ]);
            }
        } else {
            // If no response is available, handle as a network error or unknown error
            Log::error('This is an Unknown error occurred message');
            return redirect('/form')->withErrors([
                'error' => 'Unknown error occurred',
                //'message' => $e->getMessage(),
                'status_code' => null
            ]);
        }
    }
}

}
