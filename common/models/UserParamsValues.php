<?php

namespace common\models;

use Yii;
use common\base\ActiveRecord;

class UserParamsValues extends ActiveRecord {

    public static function tableName() {
        return '{{%user_params_values}}';
    }

}
