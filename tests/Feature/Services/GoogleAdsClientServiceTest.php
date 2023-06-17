<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\GoogleAds\GoogleAdsClientService;
use Mockery\MockInterface;
use Tests\Constants;
use Illuminate\Testing\Assert as PHPUnit;

class GoogleAdsClientServiceTest extends TestCase
{
    protected $service = null;

    protected $svMock = null;

    protected function setUp(): void 
    {
        parent::setUp();
        $this->svMock = $this->mock(GoogleAdsClientService::class);
        $this->service = new GoogleAdsClientService();
    }

    /**
     * A basic feature test example.
     */
    public function test_getConfiguration(): void
    {
        $expectConfiguration = Constants::config();
        $result = $this->service->getGoogleAdsConfig();

        PHPUnit::assertArrayHasKey('GOOGLE_ADS', $result);
        PHPUnit::assertArrayHasKey('developerToken', $result['GOOGLE_ADS']);
        PHPUnit::assertArrayHasKey('loginCustomerId', $result['GOOGLE_ADS']);
        PHPUnit::assertArrayHasKey('OAUTH2', $result);
        PHPUnit::assertArrayHasKey('clientId', $result['OAUTH2']);
        PHPUnit::assertArrayHasKey('clientSecret', $result['OAUTH2']);
        PHPUnit::assertArrayHasKey('refreshToken', $result['OAUTH2']);
        PHPUnit::assertContains($expectConfiguration['GOOGLE_ADS']['developerToken'], $result['GOOGLE_ADS']);
        PHPUnit::assertContains($expectConfiguration['GOOGLE_ADS']['loginCustomerId'], $result['GOOGLE_ADS']);
        PHPUnit::assertContains($expectConfiguration['OAUTH2']['clientId'], $result['OAUTH2']);
        PHPUnit::assertContains($expectConfiguration['OAUTH2']['clientSecret'], $result['OAUTH2']);
    }
}
