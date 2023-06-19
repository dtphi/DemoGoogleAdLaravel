<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use App\Services\GoogleAds\GoogleAdsClientService as GadClient;

class GoogleAdsApiController extends Controller
{
    private const CUSTOMER_ID = '1986165192';

    private static $REPORT_TYPE_TO_DEFAULT_SELECTED_FIELDS = [
        'campaign' => ['campaign.id', 'campaign.name', 'campaign.status'],
        'customer' => ['customer.id']
    ];

    private const RESULTS_LIMIT = 100;

    /**
     * Controls a GET request call api
     *
     * @param Request $request the HTTP request
     * @param GoogleAdsClientService $gadsClient the Google Ads API client
     * @param int $customerId
     * @return JSON the json to redirect to after processing
     */
    public function getCampaignAction(
        Request $request,
        GadClient $gadsClient,
        int $customerId
    ) {
        // Creates a query that retrieves all campaigns.
        $query = 'SELECT campaign.id, campaign.name FROM campaign ORDER BY campaign.id';

        $response = $gadsClient->getCampaign($customerId, $query);

        $json['campaigns'] = $response;
        $json['campaign_count'] = count($json['campaigns']);

        return response()->JSON([
            'result' => $json
        ]);
    }

    /**
     * Controls a POST request call api create a default campaign
     *
     * @param Request $request the HTTP request
     * @param GoogleAdsClientService $gadsClient the Google Ads API client
     * @param int $customerId
     * @return JSON the json to redirect to after processing
     */
    public function createCampaignAction(
        Request $request,
        GadClient $gadsClient,
        int $customerId
    ) {
        $json['customerId'] = $customerId;

        if ($request->method() === 'POST') {
            $response = $gadsClient->createCampaign($customerId);

            $json['addedCampaignCount'] = $response['added_campaign_result']->count();
            $json['budgetRourceName'] = $response['budget_rource_name'];
        }

        return response()->JSON([
            'result' => $json
        ]);
    }

    /**
     * Controls a POST or GET request call api get report.
     *
     * @param Request $request the HTTP request
     * @param GoogleAdsClientService $gadsClient the Google Ads API client
     * @param int $customerId
     * @return JSON the json to redirect to after processing
     */
    public function showReportAction(
        Request $request,
        GadClient $gadsClient,
        int $customerId
    ) {
        if ($request->method() === 'POST') {
            // Retrieves the form inputs.
            $reportType = $request->input('reportType');
            $reportRange = $request->input('reportRange');
            $entriesPerPage = $request->input('entriesPerPage');

            // Retrieves the list of metric fields to select filtering out the static ones.
            $selectedFields = array_values(
                $request->except(
                    [
                        '_token',
                        'customerId',
                        'reportType',
                        'reportRange',
                        'entriesPerPage'
                    ]
                )
            );

            // Merges the list of metric fields to the resource ones that are selected by default.
            $selectedFields = array_merge(
                self::$REPORT_TYPE_TO_DEFAULT_SELECTED_FIELDS[$reportType],
                $selectedFields
            );

            // Builds the GAQL query.
            $query = sprintf(
                "SELECT %s FROM %s WHERE metrics.impressions > 0 AND segments.date " .
                "DURING %s LIMIT %d",
                join(", ", $selectedFields),
                $reportType,
                $reportRange,
                self::RESULTS_LIMIT
            );

            // Initializes the list of page tokens. Page tokens are used to request specific pages
            // of results from the API. They are especially useful to optimize navigation between
            // pages as there is no need to cache all the results before displaying.
            // More details can be found here:
            // https://developers.google.com/google-ads/api/docs/reporting/paging.
            //
            // The first page's token is always an empty string.
            $pageTokens = [''];

            // Updates the session with all the information that is necessary to process any
            // future requests (report result pages).
            $request->session()->put('customerId', $customerId);
            $request->session()->put('selectedFields', $selectedFields);
            $request->session()->put('entriesPerPage', $entriesPerPage);
            $request->session()->put('query', $query);
            $request->session()->put('pageTokens', $pageTokens);
        } else {
            // Loads from the session all the information that is necessary to process any
            // requests (report result page).
            $customerId = $request->session()->get('customerId');
            $selectedFields = $request->session()->get('selectedFields');
            $entriesPerPage = $request->session()->get('entriesPerPage');
            $query = $request->session()->get('query');
            $pageTokens = $request->session()->get('pageTokens');
        }

        // Determines the number of the page to load (the first one by default).
        $pageNo = $request->input('page') ?: 1;

        // Fetches next pages in sequence and stores their page tokens until the page token of the
        // requested page is retrieved.
        while (count($pageTokens) < $pageNo) {
            // Fetches the next unknown page.
            $response = $gadsClient->searchReport($customerId, $query, $entriesPerPage, end($pageTokens));
            
            if ($response->getPage()->getNextPageToken()) {
                // Stores the page token of the page that comes after the one we just fetched if
                // any so that it can be reused later if necessary.
                $pageTokens[] = $response->getPage()->getNextPageToken();
            } else {
                // Otherwise changes the requested page number for the latest page that we have
                // fetched until now, the requested page number was invalid.
                $pageNo = count($pageTokens);
            }
        }

        // Fetches the actual page that we want to display the results of.
        $response = $gadsClient->searchReport($customerId, $query, $entriesPerPage, $pageTokens[$pageNo - 1]);
        
        if ($response->getPage()->getNextPageToken()) {
            // Stores the page token of the page that comes after the one we just fetched if any so
            // that it can be reused later if necessary.
            $pageTokens[] = $response->getPage()->getNextPageToken();
        }

        // Determines the total number of results to display.
        // The total results count does not take into consideration the LIMIT clause of the query
        // so we need to find the minimal value between the limit and the total results count.
        $totalNumberOfResults = min(
            self::RESULTS_LIMIT,
            $response->getPage()->getResponseObject()->getTotalResultsCount()
        );

        // Extracts the results for the requested page.
        $results = [];
        foreach ($response->getPage()->getIterator() as $googleAdsRow) {
            /** @var Google\Ads\GoogleAds\V14\Services\GoogleAdsRow $googleAdsRow */
            // Converts each result as a Plain Old PHP Object (POPO) using JSON.
            $results[] = json_decode($googleAdsRow->serializeToJsonString(), true);
        }

        // Creates a length aware paginator to supply a given page of results for the view.
        $paginatedResults = new LengthAwarePaginator(
            $results,
            $totalNumberOfResults,
            $entriesPerPage,
            $pageNo,
            ['path' => url('show-report')]
        );

        // Updates the session with the known page tokens to avoid unnecessary requests during
        // future page navigation.
        $request->session()->put('pageTokens', $pageTokens);

        $json['paginatedResults'] = $paginatedResults;
        $json['selectedFields'] = $selectedFields;

        return response()->JSON([
            'result' => $json
        ]);
    }

    /**
     * Controls a POST request api.
     *
     * @param Request $request the HTTP request
     * @param GoogleAdsClientService $gadsClient the Google Ads API client
     * @param int $customerId
     * @param int $campaignId
     * @return JSON the json to redirect to after processing
     */
    public function pauseCampaignAction(
        Request $request,
        GadClient $gadsClient,
        int $customerId,
        int $campaignId
    ) {
        $campaignResourceName = $gadsClient->pauseCampaign($customerId, $campaignId);
        
        // Builds the GAQL query to retrieve more information about the now paused campaign.
        $query = sprintf(
            "SELECT campaign.id, campaign.name, campaign.status FROM campaign " .
            "WHERE campaign.resource_name = '%s' LIMIT 1",
            $campaignResourceName
        );
        // Searches the result.
        $response = $gadsClient->searchReport(
            $customerId,
            $query
        );

        // Fetches and converts the result as a POPO using JSON.
        $campaign = json_decode(
            $response->iterateAllElements()->current()->getCampaign()->serializeToJsonString(),
            true
        );

        $json['customerId'] = $customerId;
        $json['campaign'] = $campaign;

        return response()->JSON([
            'result' => $json
        ]);
    }

    /**
     * Controls a POST request api.
     *
     * @param Request $request the HTTP request
     * @param GoogleAdsClientService $gadsClient the Google Ads API client
     * @param int $customerId
     * @param int $campaignId
     * @return JSON the json to redirect to after processing
     */
    public function deleteCampaignAction(
        Request $request,
        GadClient $gadsClient,
        int $customerId,
        int $campaignId
    ) {
        // Retrieves the form inputs.
        $customerId = $request->input('customerId')??0;
        $campaignId = $request->input('campaignId')??0;

        if ($request->input('test') || ($customerId == 0)) {
            $json['customerId'] = 1112223344;
            $json['campaignName'] = 'Campaign name 1';
        } 
        
        if ($request->method() === 'POST') {
            $json['customerId'] = $customerId;
            $removedCampaign = $gadsClient->deleteCampaign($customerId, $campaignId);
            $json['campaignName'] = $removedCampaign->getResourceName();
        }

        return response()->JSON([
            'result' => $json
        ]);
    }
}
