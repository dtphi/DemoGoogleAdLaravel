<?php

namespace Tests;

final class Constants
{
    public static function config()
    {
        return [
            'GOOGLE_ADS' => [
                'developerToken' => 'hXsKZcPJ5ELBnh5aZTnOCg',
                'loginCustomerId' => '2827348768',
            ],
        
            'OAUTH2' => [
                'clientId' => '936612272825-r0rs459pad6q2li5cndfts6ub0016o3t.apps.googleusercontent.com',
                'clientSecret' => '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',
                'refreshToken' => '',
                // Redirect URI must be absolute
                'redirectUri' => 'http://127.0..0.1:80/getCode',
                'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'tokenCredentialUri' => '',
                'scope' => 'https://www.googleapis.com/auth/adwords',
                'state' => sha1(openssl_random_pseudo_bytes(1024))
            ],
        ];
    }

    public static function userApiCredential()
    {
        return [
            'email' => 'kameron91@example.com',
            'password' => 'password'
        ];
    }
}
