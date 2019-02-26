<?php

namespace Mandic\Compare\Classes;


class HeroCampaignSearch
{

    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * @param $params
     * @param $locale
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($params, $locale)
    {
        $data = $this->getOffers($params, $locale);

        if (!isset($data->offers->offerList)) {
            return response()->json(['error' => 'No offers available'], 500);
        }

        foreach ($data->offers->offerList as $offer) {
            $offer->checked = false;
        }

        return response()->json($data, 200);
    }

    /**
     * @param $data
     * @param $locale
     * @return mixed
     */
    public function getOffers($data, $locale)
    {
        \Log::useDailyFiles(storage_path('logs/herocampaign/javaData.log'));
        $jsonRequest = json_encode($data);
        \Log::info("Request: \n" . $jsonRequest);
        $response = $this->client->post(
            config('app.java_search_endpoint') . '/' . $locale,
            [
                'headers' => $this->getHeaders(),
                'body' => $jsonRequest
            ]
        );

        \Log::info("Response: \n" . $response->getBody());


        return \GuzzleHttp\json_decode($response->getBody());
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        return $headers;
    }
}
