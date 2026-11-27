<?php

test('the landing page renders successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSeeText('Rekomendasi Draft Pick');
    $response->assertSeeText('Mulai Draft');
    $response->assertSeeText('Admin');
});

test('the homepage dashboard renders successfully', function () {
    $response = $this->get('/home');

    $response->assertStatus(200);
    $response->assertSee('METASCOUT: LAND OF DAWN');
});

