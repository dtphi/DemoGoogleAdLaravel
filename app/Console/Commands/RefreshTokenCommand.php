<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use UnexpectedValueException;

class RefreshTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:googleads:refresh-token-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var string the OAuth2 scope for the Google Ads API
     * @see https://developers.google.com/google-ads/api/docs/oauth/internals#scope
     */
    private const SCOPE = 'https://www.googleapis.com/auth/adwords';

    /**
     * @var string the Google OAuth2 authorization URI for OAuth2 requests
     * @see https://developers.google.com/identity/protocols/OAuth2InstalledApp#step-2-send-a-request-to-googles-oauth-20-server
     */
    private const AUTHORIZATION_URI = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * @var string the OAuth2 call back IP address.
     */
    private const OAUTH2_CALLBACK_IP_ADDRESS = 'http://127.0.0.1';

    public static function main()
    {
        if (!class_exists(HttpServer::class)) {
            echo 'Please install "react/http" package to be able to run this example';
            exit(1);
        }

        // To fill in the values below, generate a client ID and client secret from the Google Cloud
        // Console (https://console.cloud.google.com) by creating credentials for either a web or
        // desktop app OAuth client ID.
        // If using a web application, add the following to its "Authorized redirect URIs":
        //   http://127.0.0.1
        print 'Enter your OAuth2 client ID here: ';
        $clientId = trim(fgets(STDIN));

        print 'Enter your OAuth2 client secret here: ';
        $clientSecret = trim(fgets(STDIN));

        // Redirect URI must be absolute
        $redirectUrl = self::OAUTH2_CALLBACK_IP_ADDRESS . ':80/getCode';
        $oauth2 = new OAuth2(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'authorizationUri' => self::AUTHORIZATION_URI,
                'redirectUri' => $redirectUrl,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'scope' => self::SCOPE,
                // Create a 'state' token to prevent request forgery. See
                // https://developers.google.com/identity/protocols/OpenIDConnect#createxsrftoken
                // for details.
                'state' => sha1(openssl_random_pseudo_bytes(1024))
            ]
        );

        $authToken = null;

        printf(
            'Log into the Google account you use for Google Ads and visit the following URL '
            . 'in your web browser: %1$s%2$s%1$s%1$s',
            PHP_EOL,
            $oauth2->buildFullAuthorizationUri(['access_type' => 'offline'])
        );

        print 'Enter your OAuth2 code here: ';
        $code = trim(fgets(STDIN));
        $oauth2->setCode($code);
        $authToken = $oauth2->fetchAuthToken();

        $refreshToken = $authToken['refresh_token'];
        print 'Your refresh token is: ' . $refreshToken . PHP_EOL;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->main();
    }
}
