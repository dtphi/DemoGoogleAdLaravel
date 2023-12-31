<?php

namespace App\Services\GoogleAds;

use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\V14\ResourceNames;
use Google\Ads\GoogleAds\V14\Resources\Campaign;
use Google\Ads\GoogleAds\V14\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V14\Services\CampaignOperation;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\V14\Resources\CampaignBudget;
use App\Services\GoogleAds\Utils\Helper;
use Google\Ads\GoogleAds\V14\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V14\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V14\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V14\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V14\Common\ManualCpc;
use Google\Ads\GoogleAds\Lib\V14\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\Lib\Configuration;

class GoogleAdsClientService
{
    private $gadsClient = null;

    private const NUMBER_OF_CAMPAIGNS_TO_ADD = 1;

    private static $valueToName = [
        CampaignStatus::UNSPECIFIED => 'UNSPECIFIED',
        CampaignStatus::UNKNOWN => 'UNKNOWN',
        CampaignStatus::ENABLED => 'ENABLED',
        CampaignStatus::PAUSED => 'PAUSED',
        CampaignStatus::REMOVED => 'REMOVED',
    ];

    public function getInstance()
    {
        if ($this->gadsClient == null) {
            $this->gadsClient = $this->getLoginClient();
        }

        return $this->gadsClient;
    }

    public function configuration()
    {
        $config = config('googleads');
        return new Configuration($config);
    }

    /**
     * Return the config info of gooogle ads.
     */
    public function getGoogleAdsConfig()
    {
        return config('googleads');
    }

    /**
     * Check OAuth2
     */
    public function getLoginClient()
    {
        $gadsConfig = $this->getGoogleAdsConfig();
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

    /**
     * Get google ads client login.
     */
    private function loginFromConfig()
    {
        $config = $this->configuration();

        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->from($config)
            ->build();
        $googleAdsClient = (new GoogleAdsClientBuilder())
            ->from($config)
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        return $googleAdsClient;
    }

    /**
     * Search report in the specified client account.
     *
     * @param int $customerId
     * @param string $query
     * @param int $entriesPerPage
     * @param string $pageToken
     * @return object $response
     */
    public function searchReport(int $customerId, string $query, ?int $entriesPerPage = 0, ?string $pageToken = ''):object
    {
        if (!empty($pageToken)) {
            return $this->getInstance()->getGoogleAdsServiceClient()->search(
                $customerId,
                $query,
                [
                    'pageSize' => $entriesPerPage,
                    // Requests to return the total results count. This is necessary to
                    // determine how many pages of results exist.
                    'returnTotalResultsCount' => true,
                    // There is no need to go over the pages we already know the page tokens for.
                    // Fetches the last page we know the page token for so that we can retrieve the
                    // token of the page that comes after it.
                    'pageToken' => $pageToken
                ]
            );
        }

        return $this->getInstance()->getGoogleAdsServiceClient()->search(
            $customerId,
            $query
        );
    }

    /**
     * @param int $customerId
     * @param int $campaignId
     */
    public function searchCampaign(int $customerId, int $campaignId):object
    {
        //$resourceName = ResourceNames::forCampaign($customerId, $campaignId);
        $campaignResourceName = sprintf(
            "customers/%d/campaigns/%d",
            $customerId,
            $campaignId
        );

        $query = sprintf(
            "SELECT campaign.id, campaign.name, campaign.status FROM campaign " .
            "WHERE campaign.resource_name = '%s' LIMIT 1",
            $campaignResourceName
        );

        return $this->getInstance()->getGoogleAdsServiceClient()->search(
            $customerId,
            $query
        );
    }

    /**
     * Update status for the campaign in the specified client account.
     *
     * @param int $customerId
     * @param int $campaignId
     * @param string $campaignName
     * @param string $campaignStatus
     * @return string $resourceName
     */
    public function updateCampaign(int $customerId, int $campaignId, string $campaignName, string $campaignStatus):string
    {
        // Deducts the campaign resource name from the given IDs.
        $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

        // Creates a campaign object and sets its status to PAUSED.
        $campaign = new Campaign();
        $campaign->setResourceName($campaignResourceName);
        $campaign->setName($campaignName);
        if (in_array($campaignStatus, self::$valueToName)) {
            $campaign->setStatus(CampaignStatus::value($campaignStatus));
        }

        // Constructs an operation that will pause the campaign with the specified resource
        // name, using the FieldMasks utility to derive the update mask. This mask tells the
        // Google Ads API which attributes of the campaign need to change.
        $campaignOperation = new CampaignOperation();
        $campaignOperation->setUpdate($campaign);
        $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

        // Issues a mutate request to pause the campaign.
        $this->getInstance()->getCampaignServiceClient()->mutateCampaigns(
            $customerId,
            [$campaignOperation]
        );

        return $campaignResourceName;
    }

    /**
     * Update status for the campaign in the specified client account.
     *
     * @param int $customerId
     * @param int $campaignId
     * @return string $resourceName
     */
    public function pauseCampaign(int $customerId, int $campaignId):string
    {
        // Deducts the campaign resource name from the given IDs.
        $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

        // Creates a campaign object and sets its status to PAUSED.
        $campaign = new Campaign();
        $campaign->setResourceName($campaignResourceName);
        $campaign->setStatus(CampaignStatus::PAUSED);

        // Constructs an operation that will pause the campaign with the specified resource
        // name, using the FieldMasks utility to derive the update mask. This mask tells the
        // Google Ads API which attributes of the campaign need to change.
        $campaignOperation = new CampaignOperation();
        $campaignOperation->setUpdate($campaign);
        $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

        // Issues a mutate request to pause the campaign.
        $this->getInstance()->getCampaignServiceClient()->mutateCampaigns(
            $customerId,
            [$campaignOperation]
        );

        return $campaignResourceName;
    }

    /**
     * Create a new campaign in the specified client account.
     *
     * @param int $customerId
     * @param int $numberAdd
     * @return array $data
     */
    public function createCampaign(int $customerId, ?int $numberAdd = 1):array
    {
        // Creates a single shared budget to be used by the campaigns added below.
        $budgetResourceName = $this->addCampaignBudget($customerId);

        // Configures the campaign network options.
        $networkSettings = new NetworkSettings([
            'target_google_search' => true,
            'target_search_network' => true,
            'target_content_network' => true,
            'target_partner_search_network' => false
        ]);

        $loopCampaignAdd = $numberAdd ?? self::NUMBER_OF_CAMPAIGNS_TO_ADD;

        $campaignOperations = [];
        for ($i = 0; $i < $loopCampaignAdd; $i++) {
            // [START add_campaigns_1]
            $campaign = new Campaign([
                'name' => 'Interplanetary Cruise #' . Helper::getPrintableDatetime(),
                'advertising_channel_type' => AdvertisingChannelType::SEARCH,
                'status' => CampaignStatus::PAUSED,
                'manual_cpc' => new ManualCpc(),
                'campaign_budget' => $budgetResourceName,
                'network_settings' => $networkSettings,
                'start_date' => date('Ymd', strtotime('+1 day')),
                'end_date' => date('Ymd', strtotime('+1 month'))
            ]);
            // [END add_campaigns_1]

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setCreate($campaign);
            $campaignOperations[] = $campaignOperation;
        }

        // Issues a mutate request to add campaigns.
        $campaignServiceClient = $this->getInstance()->getCampaignServiceClient();
        $response = $campaignServiceClient->mutateCampaigns($customerId, $campaignOperations);

        $data['budget_rource_name'] = $budgetResourceName;
        $data['added_campaign_result'] = $response->getResults();

        return $data;
    }

    /**
     * Creates a new campaign budget in the specified client account.
     *
     * @param int $customerId the customer ID
     * @return string the resource name of the newly created budget
     */
    // [START add_campaigns]
    private function addCampaignBudget(int $customerId):string
    {
        // Creates a campaign budget.
        $budget = new CampaignBudget([
            'name' => 'Interplanetary Cruise Budget #' . Helper::getPrintableDatetime(),
            'delivery_method' => BudgetDeliveryMethod::STANDARD,
            'amount_micros' => 500000
        ]);

        // Creates a campaign budget operation.
        $campaignBudgetOperation = new CampaignBudgetOperation();
        $campaignBudgetOperation->setCreate($budget);

        // Issues a mutate request.
        $campaignBudgetServiceClient = $this->getInstance()->getCampaignBudgetServiceClient();
        $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
            $customerId,
            [$campaignBudgetOperation]
        );

        /** @var CampaignBudget $addedBudget */
        $addedBudget = $response->getResults()[0];

        return $addedBudget->getResourceName();
    }

    /**
     * Get the campaigns in the specified client account.
     *
     * @param int $customerId
     * @param string $query
     * @return array $data
     */
    public function getCampaign(int $customerId, string $query):array
    {
        // Issues a search stream request.
        /** @var GoogleAdsServerStreamDecorator $stream */
        $stream = $this->getInstance()->getGoogleAdsServiceClient()->searchStream($customerId, $query);

        $data = [];
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $status = $googleAdsRow->getCampaign()->getStatus();
            if ($status == CampaignStatus::REMOVED) {
            } else {
                $data[] = [
                    'id' => $googleAdsRow->getCampaign()->getId(),
                    'name' => $googleAdsRow->getCampaign()->getName(),
                    'status' => $status
                ];
            }
        }

        return $data;
    }

    /**
     * Delete the campaigns in the specified client account.
     *
     * @param int $customerId
     * @param int $campaignId
     * @return object $removedCampaign
     */
    public function deleteCampaign(int $customerId, int $campaignId):object
    {
        // Creates the resource name of a campaign to remove.
        $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

        // Creates a campaign operation.
        $campaignOperation = new CampaignOperation();
        $campaignOperation->setRemove($campaignResourceName);

        // Issues a mutate request to remove the campaign.
        $campaignServiceClient = $this->getInstance()->getCampaignServiceClient();
        $response = $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);

        /** @var Campaign $removedCampaign */
        $removedCampaign = $response->getResults()[0];

        return $removedCampaign;
    }
}
