<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        
        return view('home');
    }
    public function get_price(): string
    {
        $client = \Config\Services::curlrequest();
        // $client->setHeader('token', 'ab4086ecd47c568d5ba5739d4078988f');
        $resp = $client->request('POST', 'https://dev.pixelsoftwares.com/api.php', [
            'form_params' => [
                'symbol'=> 'BTCUSDT'
            ],
            'headers' => [
                'token' => getenv('API_TOKEN')
                ]
        ]);
        
        $body = json_decode($resp->getBody(), true);
        return json_encode(["price" => $body['data']['bidPrice']]);
    }
}
