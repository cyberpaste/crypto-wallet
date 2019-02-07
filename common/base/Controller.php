<?php

namespace common\base;

use common\behaviors\QueryParamAuth;
use common\models\User;
use Yii;

abstract class Controller extends \yii\web\Controller {

    use UserTrait;

    protected $servicePermitions = []; // = ['index', 'update'];

    public function behaviors() {
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }

    public function beforeAction($action) {

        if (parent::beforeAction($action)) {

            if (Yii::$app->user->identity->role == User::ROLE_SUPER_ADMIN) {
                return true;
            }

            $route = $action->controller->module->id . '-' . $action->controller->id . '-' . $action->id;

            if (!empty($this->servicePermitions)) {
                foreach ($this->servicePermitions as $servicePermition) {
                    if ($servicePermition === $action->id) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}
