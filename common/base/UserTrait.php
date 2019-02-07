<?php

namespace common\base;

use yii;
use common\models\User;

/**
 *
 * @property User $user
 */
trait UserTrait
{
    public function getUser()
    {
        return Yii::$app->user->identity;
    }
}