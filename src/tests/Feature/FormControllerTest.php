<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;


class FormControllerTest extends TestCase
{
    protected $container = [];
    
    public function test_submit_form_200_OK()
    {
        $postData = json_encode(['key' => 'value']);
        // Create a mock and queue a response.
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'OK',
                'error' =>false,
                'message' =>'Redirect ready',
                'urlcompleted' => 'http://urlcompleted.tld',
                'urlfailed' =>'http://urlfailed.tld',
                'data' => 'https://www.gesturl.pay.tld/redirect/with?querystring'
            ])),
        ]);

        // Middleware to store the requests
        $history = Middleware::history($this->container);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);

        // Bind the mocked client instance to the service container
        $this->app->instance(Client::class, $client);

        // Send a POST request to the form submission URL.
        $response = $this->post('/submit-form', ['url' => 'http://example.com/api/1.0/pre-order']);

        // Assert the response
        $response->assertStatus(302);

        // Assert the session has 'success' key and 'Form submitted successfully!' as its value
        $response->assertSessionHas('success', 'Form submitted successfully!');

        // Assert that a request was made
        $this->assertCount(1, $this->container);

        // Get the request
        $request = $this->container[0]['request'];

        // Assert request data
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/api/1.0/pre-order', $request->getUri()->getPath());

        // Assert the sent data
        $sentData = json_decode($request->getBody(), true);
        $this->assertEquals($postData, $sentData);
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
