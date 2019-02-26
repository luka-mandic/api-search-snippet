<?php


namespace Mandic\Compare\Tests\Functional;

use Mandic\Compare\Controllers\HomeAutomation;
use Mandic\Compare\Tests\Fixtures\RequestInputs;
use Mandic\Compare\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

class TestHomeAutomationController extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    private $controller;
    private $inputs;

    public function setUp()
    {
        parent::setUp();

        $this->inputs = (new RequestInputs())->returnInputs();
        $this->controller = new MockHomeAutomationController($this);
        $this->controller->getLocale($this->inputs['locale']);
    }


    /**
     * We need to send a product ID to the POST endpoint so that the IDs in our DB and their DB match.
     * That is why we use DB transactions and only store products if we get a 201 response.
     */
    public function testProductGetsCreatedOnValidResponse()
    {
        $this->controller->createHttpClientMock(201);
        $this->controller->createProduct($this->inputs);

        $models = \Mandic\Compare\Models\HomeAutomation::all();

        $this->assertFalse($models->isEmpty());

        $model = $models->first();

        $this->assertEquals('Amazon Echo', $model->name);
    }

    /**
     * We should rollback our DB transaction if we get a response different than 201
     */
    public function testProductDoesNotGetCreatedOnInvalidResponse()
    {
        $this->controller->createHttpClientMock(400);
        $this->controller->createProduct($this->inputs);

        $models = \Mandic\Compare\Models\HomeAutomation::all();

        $this->assertTrue($models->isEmpty());
    }

    /**
     * Products should only be updated and stored in DB if we get a 200 response from the PUT endpoint
     */
    public function testProductGetsUpdatedOnValidResponse()
    {
        $this->controller->createHttpClientMock(201);
        $this->controller->createProduct($this->inputs);

        $model = \Mandic\Compare\Models\HomeAutomation::where('name', 'Amazon Echo')->first();
        $this->inputs['product']['name'] = 'Updated name';
        $this->inputs['product']['id'] = $model->id;

        $this->controller->createHttpClientMock(200);
        $this->controller->saveProduct($this->inputs);


        $updatedModel = \Mandic\Compare\Models\HomeAutomation::find($model->id);

        $this->assertEquals('Updated name', $updatedModel->name);
    }

    /**
     * We should rollback our DB transaction if we get a response different than 200
     */
    public function testProductDoesNotGetUpdatedOnInvalidResponse()
    {
        $this->controller->createHttpClientMock(201);
        $this->controller->createProduct($this->inputs);

        $model = \Mandic\Compare\Models\HomeAutomation::where('name', 'Amazon Echo')->first();
        $this->inputs['product']['name'] = 'Updated name';
        $this->inputs['product']['id'] = $model->id;

        $this->controller->createHttpClientMock(400);
        $this->controller->saveProduct($this->inputs);


        $updatedModel = \Mandic\Compare\Models\HomeAutomation::find($model->id);

        $this->assertEquals('Amazon Echo', $updatedModel->name);
    }

}


class MockHomeAutomationController extends HomeAutomation
{
    private $testCase;
    public $client;


    public function __construct(TestHomeAutomationController $testCase)
    {
        $this->testCase = $testCase;
    }

    public function createHttpClientMock($statusCode)
    {
        $httpResponse = $this->testCase->createMock(Response::class);
        $httpResponse->method('getStatusCode')->willReturn($statusCode);
        $httpClient = $this->testCase->createMock(\GuzzleHttp\Client::class);
        $httpClient->method('request')->willReturn($httpResponse);

        $this->client = $httpClient;
    }

}