<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_correct_view_is_returned_and_contains_url_input()
    {
        // Send a GET request to the /form URL.
        $response = $this->get('/form');

        // Assert that the response status is 200 (OK).
        $response->assertStatus(200);

        // Assert that the returned view is the expected one.
        $response->assertViewIs('form');

        // Assert that the returned view contains an input field with the name 'url'.
        $response->assertSee('<input type="text" name="url" required>', false);
    }
}
