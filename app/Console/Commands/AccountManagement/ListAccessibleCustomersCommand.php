<?php

namespace App\Console\Commands\AccountManagement;

use Illuminate\Console\Command;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V14\Errors\GoogleAdsError;
use Google\ApiCore\ApiException;

class ListAccessibleCustomersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:googleads:list-accessible-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function main()
    {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $googleAdsClient = (new GoogleAdsClientBuilder())->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        try {
            self::runExample($googleAdsClient);
        } catch (GoogleAdsException $googleAdsException) {
            printf(
                "Request with ID '%s' has failed.%sGoogle Ads failure details:%s",
                $googleAdsException->getRequestId(),
                PHP_EOL,
                PHP_EOL
            );
            foreach ($googleAdsException->getGoogleAdsFailure()->getErrors() as $error) {
                /** @var GoogleAdsError $error */
                printf(
                    "\t%s: %s%s",
                    $error->getErrorCode()->getErrorCode(),
                    $error->getMessage(),
                    PHP_EOL
                );
            }
            exit(1);
        } catch (ApiException $apiException) {
            printf(
                "ApiException was thrown with message '%s'.%s",
                $apiException->getMessage(),
                PHP_EOL
            );
            exit(1);
        }
    }

    /**
     * Runs the example.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     */
    // [START list_accessible_customers]
    public static function runExample(GoogleAdsClient $googleAdsClient)
    {
        $customerServiceClient = $googleAdsClient->getCustomerServiceClient();

        // Issues a request for listing all accessible customers.
        $accessibleCustomers = $customerServiceClient->listAccessibleCustomers();
        print 'Total results: ' . count($accessibleCustomers->getResourceNames()) . PHP_EOL;

        // Iterates over all accessible customers' resource names and prints them.
        foreach ($accessibleCustomers->getResourceNames() as $resourceName) {
            /** @var string $resourceName */
            printf("Customer resource name: '%s'%s", $resourceName, PHP_EOL);
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->main();
    }
}
