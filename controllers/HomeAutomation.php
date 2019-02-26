<?php namespace Mandic\Compare\Controllers;

use Backend\Classes\Controller;
use Mandic\Locale\Models\Locale;
use Mandic\Compare\Classes\HeroCampaignSearch;
use Mandic\Compare\Classes\HomeAutomationDataProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;


class HomeAutomation extends Controller
{
    protected $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    protected $listConfig = 'config_list.yaml';
    protected $formConfig = 'config_form.yaml';
    protected $searcher;
    CONST HERO_CAMPAIGN = 'smarthome';
    CONST TYPE = 'home_automation';


    protected $requiredPermissions = [
        'manage_compare'
    ];
    protected $client;
    protected $locale;

    protected function __construct()
    {
        parent::__construct();
        $this->searcher = new HeroCampaignSearch();
        $this->client = new \GuzzleHttp\Client();

    }


    /**
     * Necessary OctoberCMS function, used to update existing products with existing offers
     * @param $recordId
     * @param null $context
     */
    protected function update($recordId, $context = null)
    {
        $product = \Mandic\Compare\Models\HomeAutomation::with('locale')->find($recordId);
        $this->locale = $product->locale;

        $this->initForm($product, 'update');

        $endpoint = $this->setupEndpoint($this->locale->locale) . $recordId;
        try{
            $httpResponse = $this->sendRequest("GET", $endpoint);

            if ($httpResponse->getStatusCode() === 200) {
                $responseBody = $httpResponse->getBody();
                $data = \GuzzleHttp\json_decode($responseBody->getContents());

                $this->vars['existingOffers'] = \GuzzleHttp\json_encode($data->product->offers);
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * Validates incoming request and then either creates or updates a product
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
     */
    protected function createOrUpdateProduct(Request $request)
    {
        $validator = $this->validateRequest($request->input());

        if ($validator->fails()) {
            return response($validator->messages(), 406);
        }

        $product = $request->input('product');
        $inputs = $request->input();
        $this->getLocale($request->input('locale'));

        try {
            if (isset($product['id'])) {
                $data = $this->updateProduct($inputs);
            } else {
                $data = $this->createProduct($inputs);
            }
            return $data;
        } catch (\Exception $e) {
            \Log::error($e);
            return response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * We need to send a product ID to the POST endpoint so that the IDs in our DB and their DB match.
     * That is why we use DB transactions and only store products if we get a 201 response.
     * @param array $inputs
     * @return \Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    protected function createProduct(array $inputs)
    {
        $product = $inputs['product'];
        $product['locale_id'] = $this->locale->id;
        $product['type'] = self::TYPE;

        DB::beginTransaction();

        $model = \Mandic\Compare\Models\HomeAutomation::create($product);
        $data = $this->processData($model, $this->locale);
        $endpoint = $this->setupEndpoint($this->locale->locale);

        try{
            $httpResponse = $this->sendRequest("POST", $endpoint, $data->getContent());
            if ($httpResponse->getStatusCode() === 201) {
                DB::commit();
                return '/backend/mandic/compare/homeautomation/update/' . $model->id . '?success=true';
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            DB::rollBack();
            \Log::error($e);
            return response($e->getMessage(), $e->getCode());
        }
        DB::rollBack();
        return '/backend/mandic/compare/homeautomation/update/' . $model->id;
    }

    /**
     * @param array $inputs
     * @return \Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    protected function updateProduct(array $inputs)
    {
        $product = $inputs['product'];
        $product['locale_id'] = $this->locale->id;

        DB::beginTransaction();

        $model = \Mandic\Compare\Models\HomeAutomation::find($product['id']);
        $model->update($product);

        $data = $this->processData($model, $this->locale, $inputs['newOffers'] ?? [], $inputs['existingOffers']);

        $endpoint = $this->setupEndpoint($this->locale->locale) . $model->id;

        try{
            $httpResponse = $this->sendRequest("PUT", $endpoint, $data->getContent());
            if ($httpResponse->getStatusCode() === 200) {
                DB::commit();
                return '/backend/mandic/compare/homeautomation/update/' . $model->id . '?success=true';
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return response($e->getMessage(), $e->getCode());
        }

        DB::rollBack();
        return '/backend/mandic/compare/homeautomation/update/' . $model->id;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function deleteProduct($id)
    {
        $product = \Mandic\Compare\Models\HomeAutomation::with('locale')->find($id);
        $endpoint = $this->setupEndpoint($product->locale->locale) . $product->id;

        try{
            $httpResponse = $this->sendRequest("DELETE", $endpoint);
            if ($httpResponse->getStatusCode() === 200) {
                $product->delete();
                return Redirect::to('/backend/mandic/compare/homeautomation/')->with('message',
                    [
                        'message' => 'Product successfully deleted!',
                        'type' => 'success'
                    ]);
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return Redirect::to('/backend/mandic/Compare/homeautomation/')->with('message',
            [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Searches for new offers
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function onOfferSearch(Request $request)
    {
        $this->getLocale($request->input('locale'));
        $params = \Mandic\Compare\Models\HomeAutomation::prepareSearchData($request, $this->locale);

        $response = $this->searcher->search($params, $this->locale->locale);

        return $response;
    }

    /**
     * @param $locale
     * @return string
     */
    protected function setupEndpoint($locale)
    {
        return env('SMART_HOME_ENDPOINT') . $locale . '/products/';
    }

    /**
     * @param $model
     * @param $locale
     * @param array $newOffers
     * @param array $existingOffers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function processData($model, $locale, $newOffers = [], $existingOffers = [])
    {
        return (new HomeAutomationDataProcessor(
            $model,
            $newOffers,
            $existingOffers,
            $locale))->makeJson();
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(env('SMART_HOME_USERNAME') . ':' . env('SMART_HOME_PASSWORD'))
        ];
        return $headers;
    }

    /**
     * @param $locale
     */
    protected function getLocale($locale)
    {
        $this->locale = Locale::where('locale', $locale)->first();
    }

    /**
     * @param $inputs
     * @return \Illuminate\Validation\Validator
     */
    protected function validateRequest($inputs)
    {
        $rules = [
            'product.name' => 'required',
            'product.price' => 'required',
            'product.product' => 'required',
            'product.category' => 'required',
            'product.link' => 'required',
            'product.code' => 'required_without:product.upc_code',
            'product.upc_code' => 'required_without:product.code',
            'locale' => 'required',
        ];

        return $validator = Validator::make($inputs, $rules);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest($method, $endpoint, $body = null)
    {
        $httpResponse = $this->client->request($method, $endpoint,
            [
                'headers' => $this->getHeaders(),
                'body' => $body
            ]);

        return $httpResponse;
    }
}
