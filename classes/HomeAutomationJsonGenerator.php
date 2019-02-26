<?php
namespace MAndic\Compare\Classes;


class HomeAutomationJsonGenerator
{
    private $offers;
    private $locale;
    private $product;
    private $element;

    public function __construct($product, $locale, $offers = [])
    {
        $this->offers = $offers;
        $this->locale = $locale;
        $this->product = $product;
    }


    private function addElement()
    {
        $this->element = [
            "id" => $this->product->id,
            "name" => $this->product->name,
            "price" => (float) $this->product->price,
            "currency" => $this->locale->currency,
            "category" => $this->product->category,
            "link" => $this->product->link,
            "code" => $this->product->code ?? "",
            "upc_code" => $this->product->upc_code ?? "",
            "product" => $this->product->product,
            "offers" => $this->offers

        ];
    }


    public function getJson()
    {
        $this->addElement();

        return response()->json($this->element);
    }

}