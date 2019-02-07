<?php

namespace common\models;

use common\base\ActiveRecord;

class UserParams extends ActiveRecord {

    public static function tableName() {
        return '{{%user_params}}';
    }

}
