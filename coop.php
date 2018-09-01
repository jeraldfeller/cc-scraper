<?php
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
require 'Model/Init.php';
require 'Model/Scraper.php';
require 'simple_html_dom.php';

$scraper = new Scraper();

$inputs = $scraper->getInputsById(2);
foreach ($inputs as $row) {
    $store = 'coop';
    $id = $row['id'];
    $ean = $row['ean'];
    $zipCode = $row['zip_code'];
    $address = $row['address'];
    $city = $row['city'];
    $link = $row['link'];

    $htmlData = $scraper->curlTo($link);
    $html = file_get_html($link, false);
    if ($html) {
        $wrap = $html->find('.product-wrapper', 0);
        if($wrap){
            $title = trim($wrap->find('.name', 0)->plaintext);
            $pricePerKg = $wrap->find('.price_per_qty', 0)->plaintext;
            $isPromo = $wrap->find('.price-discount', 0);
            $qty = $wrap->find('#qty', 0);
            if($qty){
                $available = 1;
            }else{
                $available = 0;
            }
            if($isPromo){
                $promo = 'yes';
                $price = htmlspecialchars($wrap->find('.final-price', 0)->plaintext);
                $originalPrice = $wrap->find('.old-price', 0)->plaintext;
            }else{
                $promo = 'no';
                $price = $wrap->find('.price', 0)->plaintext;
                $originalPrice = $price;
            }

           // $explodeTitle = explode(' ', $title);
            $weight = 0;
            $unitMeasure = '';
            if(preg_match('/[0-9]/', $title, $matches, PREG_OFFSET_CAPTURE)) {
                $i1 = $matches[0][1];
                preg_match("#\d\D*$#", $title, $matches, PREG_OFFSET_CAPTURE);
                $i2 = $matches[0][1];
                $measurementText = substr($title, $i1, $i2);
                if(strpos($measurementText, 'X') > 0){
                    $measurementExplode = explode(' ', $measurementText);
                    $unitMeasure = $measurementExplode[count($measurementExplode) - 1];
                    unset($measurementExplode[count($measurementExplode) - 1]);
                    $weight = implode(' ', $measurementExplode);
                }else{
                    $titleExplode = explode(' ', $title);
                    $titleLastPart = $titleExplode[count($titleExplode)-1];
                    $weight = preg_replace("/[^0-9.]/", "", $titleLastPart);
                    $unitMeasure = preg_replace('/[0-9]+/', '', $titleLastPart);
                }
            }

            $data = array(
                'id' => $id,
                'title' => $title,
                'price' => preg_replace("/[^0-9.]/", "", str_replace(',', '.', $price)),
                'pricePerKg' => preg_replace("/[^0-9.]/", "", str_replace(',', '.', $pricePerKg)),
                'originalPrice' => preg_replace("/[^0-9.]/", "", str_replace(',', '.', $originalPrice)),
                'unitMeasure' => $unitMeasure,
                'size' => $weight,
                'promo' => $promo,
                'available' => $available,
                'ean' => $ean,
                'zipCode' => $zipCode,
                'address' => $address,
                'city' => $city,
                'link' => $link,
                'store' => $store

            );
            $scraper->insertOutput($data);
            $scraper->setItemFound($id);
        }else{
            echo "item not found...\n";
            $scraper->setItemNotFound($id);
        }
    }



}