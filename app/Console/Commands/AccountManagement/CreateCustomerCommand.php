<?php

namespace App\Console\Commands\AccountManagement;

use Illuminate\Console\Command;
use GetOpt\GetOpt;
use App\Services\GoogleAds\Utils\ArgumentNames;
use App\Services\GoogleAds\Utils\ArgumentParser;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V14\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V14\Resources\Customer;
use Google\ApiCore\ApiException;

class CreateCustomerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:googleads:create-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private const MANAGER_CUSTOMER_ID = '2827348768';

    public function main()
    {
        //$this->info(ArgumentNames::MANAGER_CUSTOMER_ID);
        //exit(1);
        // Either pass the required parameters for this example on the command line, or insert them
        // into the constants above.
        $options = (new ArgumentParser())->parseCommandArguments([
            ArgumentNames::MANAGER_CUSTOMER_ID => GetOpt::REQUIRED_ARGUMENT
        ]);

        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $googleAdsClient = (new GoogleAdsClientBuilder())
            ->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        try {
            self::runExample(
                $googleAdsClient,
                $options[ArgumentNames::MANAGER_CUSTOMER_ID] ?: self::MANAGER_CUSTOMER_ID
            );
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
     * @param int $managerCustomerId the manager customer ID
     */
    // [START create_customer]
    public static function runExample(GoogleAdsClient $googleAdsClient, int $managerCustomerId)
    {
        $customer = new Customer([
            'descriptive_name' => 'Account created with CustomerService on ' . date('Ymd h:i:s'),
            // For a list of valid currency codes and time zones see this documentation:
            // https://developers.google.com/google-ads/api/reference/data/codes-formats.
            'currency_code' => 'USD',
            'time_zone' => 'America/New_York',
            // The below values are optional. For more information about URL
            // options see: https://support.google.com/google-ads/answer/6305348.
            'tracking_url_template' => '{lpurl}?device={device}',
            'final_url_suffix' => 'keyword={keyword}&matchtype={matchtype}&adgroupid={adgroupid}'
        ]);

        // Issues a mutate request to create an account
        $customerServiceClient = $googleAdsClient->getCustomerServiceClient();
        $response = $customerServiceClient->createCustomerClient($managerCustomerId, $customer);

        printf(
            'Created a customer with resource name "%s" under the manager account with '
            . 'customer ID %d.%s',
            $response->getResourceName(),
            $managerCustomerId,
            PHP_EOL
        );
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->main();
    }
}
