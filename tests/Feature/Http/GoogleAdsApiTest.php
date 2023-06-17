<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class GoogleAdsApiTest extends TestCase
{
    public function test_get_campaign(): void 
    {
        $customerId = 1986165192;
        $campaignCount = 1;
        $campaignId = 20290897135;
        $template = 'list-result';
        $dataViewKey = 'data';

        /** @var Illuminate\Testing\TestResponse */
        $response = $this->get('/get-campaign?customerId=' . 1986165192);

        $response->assertStatus(200);
        $response->assertSeeText($campaignId);
        $response->assertViewIs($template);
        $response->assertViewHas($dataViewKey);
    }
}
