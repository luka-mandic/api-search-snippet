<?php

namespace Mandic\Compare\Tests\Fixtures;

class RequestInputs
{
    public function returnInputs()
    {
        return $inputs = [
            "newOffers" => [
                [
                    "sourceType" => "hero_smarthome",
                    "sourceObjectId" => 1,
                    "productId" => 1,
                    "network" => "billigerde",
                    "networkProductId" => "1243675632",
                    "networkOfferId" => "932464692",
                    "merchantId" => "3667",
                    "merchantName" => "OTTO",
                    "merchantImage" => "www.url.com",
                    "offerName" => "Amazon Echo (2. Generation) - Smart Lautsprecher grau",
                    "offerImage" => "www.url.com",
                    "currency" => "EUR",
                    "price" => 99.99,
                    "priceDisplay" => "99,99 €",
                    "shippingCosts" => 5.95,
                    "shippingCostsDisplay" => "5,95",
                    "url" => "www.url.com",
                    "trackingUrl" => "www.url.com",
                    "manufacturer" => "ECHO",
                    "availabilityStatus" => "GREEN",
                    "availabilityTextLocalised" => "lieferbar - in einem Werktag bei Ihnen",
                    "checked" => true
                ],
                [
                    "sourceType" => "hero_smarthome",
                    "sourceObjectId" => 1,
                    "productId" => 1,
                    "network" => "billigerde",
                    "networkProductId" => "1243675632",
                    "networkOfferId" => "811348663",
                    "merchantId" => "5440",
                    "merchantName" => "JACOB",
                    "merchantImage" => "www.url.com",
                    "offerName" => "Amazon Echo (2nd Generation) - Smart-Lautsprecher - Bluetooth, Wi-Fi - zweiweg - Anthrazit",
                    "offerImage" => "www.url.com",
                    "currency" => "EUR",
                    "price" => 113.2,
                    "priceDisplay" => "113,20 €",
                    "shippingCosts" => 0,
                    "shippingCostsDisplay" => "0,00 €",
                    "url" => "www.url.com",
                    "trackingUrl" => "www.url.com",
                    "manufacturer" => "Amazon",
                    "availabilityStatus" => "GREEN",
                    "availabilityTextLocalised" => "Sofort lieferbar",
                    "checked" => true,
                ]
            ],
            "existingOffers" => [
                [
                    "sourceType" => "hero_smarthome",
                    "sourceObjectId" => 22,
                    "productId" => 1,
                    "network" => "billigerde",
                    "networkProductId" => "1401490151",
                    "networkOfferId" => "921754866",
                    "merchantId" => "21097",
                    "merchantName" => "fonfonfon.de",
                    "merchantImage" => "www.url.com",
                    "offerName" => "Samsung Galaxy Note 9 N960F Duos 128GB schwarz",
                    "offerImage" => "www.url.com",
                    "currency" => "EUR",
                    "price" => 745.77,
                    "priceDisplay" => "745,77 €",
                    "savingsTotal" => 154.23,
                    "savingsTotalDisplay" => "154,23 €",
                    "savingsTotalPercentage" => 17.1,
                    "savingsTotalPercentageDisplay" => "18",
                    "shippingCosts" => 0,
                    "shippingCostsDisplay" => "0,00 €",
                    "url" => "www.url.com",
                    "trackingUrl" => "www.url.com",
                    "manufacturer" => "Samsung",
                ]
            ],
            "product" => [
                "id" => 1,
                "locale_id" => 1,
                "type" => "home_automation",
                "name" => "Amazon Echo",
                "category" => "entertaiment ",
                "link" => "www.url.com",
                "product" => "Amazon Echo",
                "code" => "B06ZXQV6P8",
                "price" => 99.99,
                "upc_code" => "dfgdgd",
                "created_at" => "2018-10-30 09:42:33",
                "updated_at" => "2018-11-12 13:30:26",
                "locale" => [
                    "id" => 1,
                    "locale" => "de",
                    "language" => "de_DE",
                    "php_date_format" => "d.m.Y",
                    "linux_locale" => "de_DE.utf8",
                    "currency" => "EUR",
                    "seo_lang" => "de-de",
                    "seo_url" => "www.url.com",
                    "utc_offset_hours" => 1,
                    "utc_offset_minutes" => 0,
                    "currency_symbol" => "€",
                    "currency_position" => "after"
                ]
            ],
            "locale" => "de"
        ];
    }
}