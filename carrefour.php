<?php
require 'Model/Init.php';
require 'Model/Scraper.php';
require 'simple_html_dom.php';

$scraper = new Scraper();

$inputs = $scraper->getInputsById(1);

$url = 'https://myshop.carrefour.it/api/search/getproductbyids';
foreach ($inputs as $row) {
    $store = 'carrefour';
    $id = $row['id'];
    $ean = $row['ean'];
    $zipCode = $row['zip_code'];
    $address = $row['address'];
    $city = $row['city'];
    $link = $row['link'];
    $parseUrl = parse_url($row['link']);
    parse_str($parseUrl['query'], $arr);
    $itemId = $arr['id'];
    $postField = json_encode(array('ProductIds' => array($itemId)));

    $response = $scraper->carrefourCurlTo($url, $postField);
    if ($response['html']) {
        $contents = json_decode($response['html'], true);
        if (count($contents['Documents']) > 0) {
            $doc = $contents['Documents'][0];
            //var_dump($doc);
            $price = $doc['PropertyBag']['C4_PricesDiscount'];
            $originalPrice = $doc['PropertyBag']['C4_PricesDefault'];
            $pricePerKg = $doc['PropertyBag']['C4_UnitPrice'];
            $active = $doc['PropertyBag']['Active'];
            $title = $doc['PropertyBag']['C4_Description'];
            $measurement = $doc['PropertyBag']['C4_UOM'];
            $weight = $doc['PropertyBag']['C4_Weigth'];
            $promo = $doc['PropertyBag']['C4_FlagPromo'];

            $data = array(
                'id' => $id,
                'title' => addslashes($title),
                'price' => $price,
                'originalPrice' => $originalPrice,
                'pricePerKg' => $pricePerKg,
                'available' => $active,
                'unitMeasure' => $measurement,
                'size' => $weight,
                'promo' => ($promo == true ? 'yes' : 'no'),
                'ean' => $ean,
                'zipCode' => $zipCode,
                'address' => $address,
                'city' => $city,
                'link' => $link,
                'store' => $store
            );

            $scraper->insertOutput($data);
            $scraper->setItemFound($id);

        } else {
            echo "item not found...\n";
            $scraper->setItemNotFound($id);
        }
    }


}