<?php

use Mandic\Compare\Models\HomeAutomation;
use Mandic\Compare\Tests\TestCase;
use Mandic\Compare\Classes\HomeAutomationDataProcessor;
use Mandic\Locale\Models\Locale;
use Mandic\Compare\Tests\Fixtures\RequestInputs;


class JsonSchemaTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;
    private $data;
    private $locale;

    public function setUp()
    {
        parent::setUp();

        $this->data = (new RequestInputs())->returnInputs();
        $this->locale = Locale::where('locale', $this->data['locale'])->first();

    }
    public function testJsonSchema()
    {
        $model = HomeAutomation::create($this->data['product']);

        $data = (new HomeAutomationDataProcessor($model, $this->data['newOffers'], $this->data['existingOffers'], $this->locale))->makeJson();
        $json = json_decode($data->getContent());

        $validator = new JsonSchema\Validator;
        $validator->validate($json, (object)['$ref' => 'file://' . realpath('plugins/mandic/compare/tests/fixtures/schema.json')]);

        if (!$validator->isValid()) {
            echo "\nJSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                echo sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        $this->assertTrue($validator->isValid(), 'Json schema failed validation');
    }
}
