<?php

use yii\db\Migration;
use common\models\Markets;
use common\models\UserParams;

/**
 * Class m190207_153504_add_binance_params
 */
class m190207_153504_add_binance_params extends Migration {

    public function safeUp() {
        $market = new Markets;
        $market->name = 'Binance';
        $market->slug = 'binance';
        $market->save();

        $marketId = $market->id;


        $userParams = new UserParams;
        $userParams->name = 'API Key';
        $userParams->market_id = $marketId;
        $userParams->slug = 'binance_api_key';

        $userParams->save();

        $userParams = new UserParams;
        $userParams->name = 'Secret Key';
        $userParams->market_id = $marketId;
        $userParams->slug = 'binance_secret_key';
        $userParams->save();
    }

    public function safeDown() {
        $market = Markets::find()->where(['name' => 'Binance'])->one();
        $this->delete('markets', ['id' => $market->id]);
        $this->delete('user_params', ['market_id' => $market->id]);
    }

}
