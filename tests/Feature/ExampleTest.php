<?php

test('the landing page renders successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSeeText('Sistem Cerdas');
    $response->assertSeeText('Rekomendasi Draft Pick');
    $response->assertSeeText('Mulai Rekomendasi Draft');
    $response->assertSeeText('Login Admin');
});

test('the homepage dashboard renders successfully', function () {
    $response = $this->get('/home');

    $response->assertStatus(200);
    $response->assertSee('METASCOUT: LAND OF DAWN');
});

