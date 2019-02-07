<?php

namespace common\models;

use common\base\ActiveRecord;

class Markets extends ActiveRecord {

    public static function tableName() {
        return '{{%markets}}';
    }

}
