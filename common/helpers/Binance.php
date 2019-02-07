<?php

namespace common\helpers;

use common\api\Binance as BinanceApi;
use common\interfaces\ApiInterface;

class Binance implements ApiInterface {

    private $apiKey;
    private $apiSecret;

    public function addUserParams(array $params) {
        $this->apiKey = $params['binance_api_key'];
        $this->apiSecret = $params['binance_secret_key'];
    }

    public function validate() {
        if ($this->apiKey && $this->apiSecret) {
            return true;
        }
        return false;
    }

    public function getBalance() {
        if ($this->validate()) {
            $api = new BinanceApi($this->apiKey, $this->apiSecret);
            $ticker = $api->prices();
            $balances = $api->balances($ticker);
            /* TODO DROP ME */
            $balances['RPC']['available'] = rand(1, 44);
            $balances['FTH']['available'] = rand(3, 444);
            /* end */
            return $balances;
        }
    }

}
