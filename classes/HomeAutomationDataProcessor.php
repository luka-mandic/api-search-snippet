<?php


namespace Mandic\Compare\Classes;


class HomeAutomationDataProcessor
{
    private $product;
    private $newOffers;
    private $existingOffers;
    private $locale;

    public function __construct($product, $newOffers, $existingOffers, $locale)
    {
        $this->product = $product;
        $this->newOffers = $newOffers;
        $this->existingOffers = $existingOffers;
        $this->locale = $locale;
    }

    private function checkForDuplicateOffers()
    {
        /*
         * Use 'networkOfferId' field to identify duplicate offers and remove them from the existingOffers array
         * because we want to keep the newly selected offers
         */
        $offerKeys = [];
        foreach ($this->existingOffers as $key => $existingOffer) {
            // Remove offer from array if 'delete' == true
            if (isset($existingOffer['delete']) && $existingOffer['delete'] == true) {
                unset($this->existingOffers[$key]);
                continue;
            }
            // Remove 'delete' index as it will interfere with POST and PUT requests to java api
            unset($this->existingOffers[$key]['delete']);
            $offerKeys[$existingOffer['networkOfferId']] = $key;
        }

        foreach ($this->newOffers as $key => $newOffer) {
            if (isset($offerKeys[$newOffer['networkOfferId']])) {
                unset($this->existingOffers[$offerKeys[$newOffer['networkOfferId']]]);
            }
        }

        return array_merge($this->newOffers, $this->existingOffers);
    }

    public function makeJson()
    {
        $offers = $this->checkForDuplicateOffers();

        return ((new HomeAutomationJsonGenerator($this->product, $this->locale, $offers))->getJson());
    }

}