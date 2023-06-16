<?php

namespace App\Services\GoogleAds;

use Google\Ads\GoogleAds\Lib\V14\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\V14\ResourceNames;
use Google\Ads\GoogleAds\V14\Resources\Campaign;
use Google\Ads\GoogleAds\V14\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V14\Services\CampaignOperation;
use Google\Ads\GoogleAds\Util\FieldMasks;

class GoogleAdsClientService
{
    private $gadsClient = null;

    public function getInstance()
    {
        if ($this->gadsClient == null) {
            $this->gadsClient = $this->getLoginClient();
        }

        return $this->gadsClient;
    }

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

    /**
     * @customerId int
     * @query string
     * @entriesPerPage int
     * @pageToken string
     * @return response object.
     */
    public function searchReport(int $customerId, string $query, ?int $entriesPerPage, ?string $pageToken):object
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
     * @customerId int
     * @campaignId int
     * @return resourceName string
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
}