<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;


class FormControllerTest extends TestCase
{
    public function test_submit_form_200_OK()
    {
        // Create a mock and queue a response.
        $mock = new MockHandler([
            new Response(200, [], json_encode(['foo' => 'response'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Bind the mocked client instance to the service container
        $this->app->instance(Client::class, $client);

        // Send a POST request to the form submission URL.
        $response = $this->post('/submit-form', ['url' => 'http://example.com']);

        // Assert the session has 'success' key and 'Form submitted successfully!' as its value
        $response->assertSessionHas('success', 'Form submitted successfully!');

        // Assert the session has 'response' key and 'OK' as its value
        // $response->assertSessionHas('response', 'OK');
    }


    public function test_submit_form_400_KO()
    {
        // Create a mock and queue a response.
        $mock = new MockHandler([
            new Response(400, [], json_encode(['foo' => 'response-400'])),
        ]);
        
        $handler = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handler]);
    
        // Bind the mock client to the service container
        $this->app->instance(Client::class, $mockClient);

        // Send a POST request to the form submission URL.
        $response = $this->post('/submit-form', ['url' => 'https://tantosvago.it/not-found']);
        
        $response->assertRedirect('/form');

        // Assert the session has 'success' key and 'Form submitted successfully!' as its value
        $response->assertSessionHasErrors([
            'error' => 'Client error occurred',
            // 'message' => json_encode(['foo' => 'response-400']),
            // 'status_code' => 400,// same here
        ]);

        // Assert the session has 'response' key and 'OK' as its value
        // $response->assertSessionHas('response', 'OK');
    }

    public function test_submit_form_403_KO()
    {
        // Create a mock and queue a response.
        $mock = new MockHandler([
            new Response(403, [], json_encode(['foo' => 'response-403'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Bind the mocked client instance to the service container
        $this->app->instance(Client::class, $client);

        // Send a POST request to the form submission URL.
        $response = $this->post('/submit-form', ['url' => 'https://middleware.tantosvago.com/form']);

        // Assert the redirect to the home URL with session data.
        $response->assertRedirect('/form');
        // Assert the redirect to a named route.
        
    }
}
