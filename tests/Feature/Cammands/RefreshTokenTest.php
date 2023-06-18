<?php

namespace Tests\Feature\Cammands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Google\Auth\CredentialsLoader;
use Tests\TestCase;
use Tests\Constants;
use Google\Auth\OAuth2;

class RefreshTokenTest extends TestCase
{
    /**
     * A basic feature test handle.
     */
    public function test_handle(): void
    {
        $config = Constants::config()['OAUTH2'];

        $expectedParam = [
            'clientId' => $config['clientId'],
            'clientSecret' => $config['clientSecret'],
            'authorizationUri' => $config['authorizationUri'],
            'redirectUri' => $config['redirectUri'],
            'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            'scope' => $config['scope'],
            'state' => $config['state']
        ];

        $expectedOauthUriPrefix = 'response_type=code&access_type=offline&client_id=' . $expectedParam['clientId'];

        $oauth2 = new OAuth2($expectedParam);

        $oauthUri = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $oauthUri);
        $this->assertIsString($oauthUri->getQuery());
        $this->assertStringStartsWith($expectedOauthUriPrefix, $oauthUri->getQuery());
    }
}
