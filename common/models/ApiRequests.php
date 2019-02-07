<?php

namespace common\models;

use common\base\ActiveRecord;

class ApiRequests extends ActiveRecord {

    public static function tableName() {
        return '{{%api_requests}}';
    }

    public function beforeSave($insert) {
        $this->data = json_encode($this->data);
        return parent::beforeSave($insert);
    }

    public function afterFind() {
        parent::afterFind();
        $this->data = json_decode($this->data, 1);
        return $this;
    }

}
