<?php

namespace App\Controllers;
use CodeIgniter\Database\RawSql;
use DateTime;

class Home extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        $db->query('CREATE TABLE IF NOT EXISTS live_price(price VARCHAR(50), last_updated DATETIME)');

        return view('home');
    }


    public function get_price(): string
    {
        $price = 0;
        // current time in ms since unix epoch
        $time = (int)(microtime(true)*1000);

        $db = \Config\Database::connect();
        $resp = $db->query('SELECT * FROM live_price')->getResult();

        // if no rows exist, fetch price, insert and return it
        if(!$resp){
            $price = $this->fetch_api_price();
            $data = [
                'price' => $price,
                'last_updated' => $time
            ];
            $db->table('live_price')->insert($data);
            log_message('error', 'first price');
            return json_encode(['price' => $price]);
        }
        $res = $resp[0];
        $diff = abs($time - $res->last_updated);
        
        // if difference is greater than 1 minute fetch new price otherwise return old one
        if($diff > 60000){
            
            $price = $this->fetch_api_price();
            $data = [
                'price' => $price,
                'last_updated' => $time
            ];
            $db->table('live_price')->update($data);
            log_message('error', 'stale price');

        } else {
            log_message('error', 'not stale price');

            $price = $res->price;
        }
        return json_encode(["price" => $price]);
    }

    function fetch_api_price(): string
    {
        $client = \Config\Services::curlrequest();
        $resp = $client->request('POST', 'https://dev.pixelsoftwares.com/api.php', [
            'form_params' => [
                'symbol'=> 'BTCUSDT'
            ],
            'headers' => [
                'token' => getenv('API_TOKEN')
                ]
        ]);
        
        $body = json_decode($resp->getBody(), true);
        return $body['data']['bidPrice'];
            
    }
}
