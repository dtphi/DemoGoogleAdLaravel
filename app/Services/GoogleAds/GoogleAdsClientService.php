<?php

namespace App\Services\GoogleAds;

use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;

class GoogleAdsClientService
{
    public function getLoginClient()
    {
        $gadsConfig = config()->get('googleads');
        $oAuth2Builder = (new OAuth2TokenBuilder())
            ->withClientId($gadsConfig['OAUTH2']['clientId'])
            ->withClientSecret($gadsConfig['OAUTH2']['clientSecret'])
            ->withRefreshToken($gadsConfig['OAUTH2']['refreshToken']);
        
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = $oAuth2Builder->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $gadsClientBuilder = (new GoogleAdsClientBuilder())
            ->withDeveloperToken($gadsConfig['GOOGLE_ADS']['developerToken'])
            ->withLoginCustomerId($gadsConfig['GOOGLE_ADS']['loginCustomerId'])
            ->withOAuth2Credential($oAuth2Credential);

        $googleAdsClient = $gadsClientBuilder->build();

        return $googleAdsClient;
    }
}