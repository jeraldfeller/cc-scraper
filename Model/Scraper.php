<?php

/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 8/20/2018
 * Time: 5:30 AM
 */
class Scraper
{
    public $debug = TRUE;
    protected $db_pdo;

    public function insertInput($storeId, $link, $ean, $address, $addressType, $city){
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `inputs` SET `store_id` = '.$storeId.', `link` = "'.$link.'", `ean` = "'.$ean.'", `'.$addressType.'` = "'.$address.'", `city` = "'.$city.'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function insertOutput($data){
        $date = date('Y-m-d');
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `outputs` SET `input_id` = '.$data['id'].', 
                `store` = "'.$data['store'].'",
                `title` = "'.$data['title'].'",
                `size` = "'.$data['size'].'",
                `unit_measure` = "'.$data['unitMeasure'].'",
                `price` = '.$data['price'].',
                `price_per_kg` = '.$data['pricePerKg'].',
                `original_price` = '.$data['originalPrice'].',
                `is_promo` = "'.$data['promo'].'",
                `available` = '.$data['available'].',
                `ean` = "'.$data['ean'].'",
                `zip_code` = "'.$data['zipCode'].'",
                `address` = "'.$data['address']. '",
                `city` = "'.$data['city'].'",
                `link` = "'.$data['link'].'",
                `date` = "'.$date.'"
                ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function setItemNotFound($id){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `inputs` SET `is_found` = 0 WHERE `id` = '.$id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function setItemFound($id){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `inputs` SET `is_found` = 1 WHERE `id` = '.$id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function getInputs(){
        $pdo = $this->getPdo();
        $sql = 'SELECT i.*, s.name FROM `inputs` i, `store` s WHERE i.store_id = s.id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $content[] = $row;
        }
        $pdo = null;

        return $content;
    }

    public function getOutputs(){
        $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `outputs`
                ORDER BY `id` DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $content[] = $row;
        }
        $pdo = null;

        return $content;
    }

    public function getInputsById($storeId){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `inputs` WHERE `store_id` = '.$storeId.' AND `status` = 0 ORDER BY `id` LIMIT '.LIMIT_COUNT;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $content[] = $row;
            $sql = 'UPDATE `inputs` SET `status` = 0 WHERE `id` = '.$row['id'];
            $stmtU = $pdo->prepare($sql);
            $stmtU->execute();
        }
        $pdo = null;

        return $content;
    }

    public function reset(){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `inputs` SET `status` = 0';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function carrefourCurlTo($url, $post){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json, text/javascript, */*; q=0.01",
                "Host: myshop.carrefour.it",
                "Origin: https://myshop.carrefour.it",
                "Referer: https://myshop.carrefour.it/Ricerca?search=barilla",
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36",
                "Content-Type: application/json",
                "Cookie: c4_service_info=Regular; ASP.NET_SessionId=uvlsyjh35wlgqycjilaaruc2; SC_ANALYTICS_GLOBAL_COOKIE=fd538928d33045229386c88355d2d6af|False; _ga=GA1.3.1569134641.1535063895; _ga=GA1.2.1569134641.1535063895; cto_lwid=57df8a75-97a0-4afc-b4de-269c963a8da9; c4_store_info=%7B%22id%22%3A%220893%22%2C%22citta%22%3A%22Castelnuovo%20Scrivia%22%2C%22insegna%22%3A%22Carrefour%20Market%22%2C%22address%22%3A%22Castelnuovo%20Scrivia%20Via%20M.%20D'azeglio%20N.16%22%2C%22tipologiaConsegna%22%3A%22pickup%22%2C%22zipCode%22%3A%2215053%22%7D; c4_cust_info=ID=f52408a0-3fb5-4e00-af71-12111b7b8726&ISGUEST=True; BVBRANDID=9bd74032-a37d-4efe-8464-3ff246b9817d; _gid=GA1.3.2076815772.1535401747; BVBRANDSID=d6738527-d788-4187-9857-b56ac2b89770; _gid=GA1.2.2076815772.1535401747",
                "Cache-Control: no-cache",
                "Postman-Token: 85969a77-227f-4da2-ab22-81feaa26c0c4"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return array('html' => $err);
        } else {
            return array('html' => $response);
        }
    }

    public function curlTo($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 85969a77-227f-4da2-ab22-81feaa26c0c4"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return array('html' => $err);
        } else {
            return array('html' => $response);
        }
    }

    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
}