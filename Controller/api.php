<?php
require '../Model/Init.php';
require '../Model/Scraper.php';
$scraper = new Scraper();

switch ($_GET['action']) {
    case 'get-inputs':
        echo json_encode($scraper->getInputs());
        break;
    case 'import':
        if (isset($_FILES['importFile']['tmp_name'])) {
            $store = $_POST['store'];
            if (pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION) == 'csv') {
                $file = $_FILES['importFile']['tmp_name'];
                $fileName = $_FILES['importFile']['name'];
                $flag = true;
                $fileHandle = fopen($_FILES['importFile']['tmp_name'], "r");
                while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
                    if ($flag) {
                        $flag = false;
                        continue;
                    }
                    $link = $data[0];
                    $ean = $data[2];
                    if($store == 1){
                        $address = htmlspecialchars($data[1], ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');
                        $addressType = 'zip_code';
                        $city = '';
                    }else{
                        $address = htmlspecialchars($data[1], ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');
                        $addressType = 'address';
                        $city = explode('.', parse_url($link)['host'])[0];
                    }

                    $scraper->insertInput($store, $link, $ean, $address, $addressType, $city);
                }

                fclose($fileHandle);
            }
            echo true;
        } else {
            echo false;
        }
        break;
}