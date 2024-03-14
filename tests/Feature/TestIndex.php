<?php

use App\Models\ShortUrl;
use Inertia\Testing\AssertableInertia as Assert;

test('index screen can be rendered', function () {
    $response = $this->get('/');

    $response->assertInertia(fn(Assert $page) => $page->component('Index'));
});

test('user can shorten link', function () {
    $response = $this->post('/short-urls', [
        'urlToShort' => 'https://www.google.com',
    ]);

    $response->assertInertia(fn(Assert $page) => $page->component('Index')->has('shortUrl'));
});

test('user is redirected when using short link', function () {
    $entity = ShortUrl::factory()->createOne();

    $response = $this->get(url("short-urls/$entity->short_url"));

    $response->assertRedirect($entity->url);
});
