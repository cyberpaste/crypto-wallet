<?php

namespace frontend\modules\user\controllers;

use common\base\FrontController;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii;
use common\models\UserParams;
use common\models\UserParamsValues;
use common\models\Markets;
use yii\web\HttpException;
use common\models\ApiRequests;

class DefaultController extends FrontController {

    public function behaviors() {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'edit', 'update-balance'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'edit' => ['get', 'post'],
                ],
            ],
        ];
    }

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/site/user');
    }

    public function actionIndex() {
        $markets = Markets::find()->asArray()->indexBy('id')->all();
        $apiRequests = ApiRequests::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->indexBy('market_id')
                ->all();

        return $this->render('index.twig', ['markets' => $markets, 'apiRequests' => $apiRequests, 'title' => 'Balance']);
    }

    public function actionUpdateBalance($id) {
        $market = Markets::find()->where(['id' => $id])->one();
        if ($market) {
            $className = "\\common\\helpers\\" . ucfirst($market->slug);
            if (class_exists($className)) {
                $userParams = UserParams::find()
                        ->where(['market_id' => $market->id])
                        ->indexBy('id')
                        ->asArray()
                        ->all();
                $userParamsIds = [];
                foreach ($userParams as $item) {
                    $userParamsIds[] = $item['id'];
                }

                if ($userParamsIds) {
                    $userParamsValues = UserParamsValues::find()
                            ->where(['user_id' => Yii::$app->user->id])
                            ->andWhere(['in', 'param_id', $userParamsIds])
                            ->asArray()
                            ->all();
                    $convertedData = [];
                    foreach ($userParamsValues as $item) {
                        $convertedData[$userParams[$item['param_id']]['slug']] = $item['value'];
                    }
                    $marketUpdater = new $className;
                    $marketUpdater->addUserParams($convertedData);
                    if ($marketUpdater->validate()) {
                        $balance = $marketUpdater->getBalance();
                        if (!$balance['error']) {
                            $apiRequest = ApiRequests::find()->where(['market_id' => $market->id, 'user_id' => Yii::$app->user->id])->one();
                            if ($apiRequest) {
                                $apiRequest->data = $balance;
                                $apiRequest->save();
                            } else {
                                $apiRequests = new ApiRequests();
                                $apiRequests->data = $balance;
                                $apiRequests->market_id = $market->id;
                                $apiRequests->user_id = Yii::$app->user->id;
                                $apiRequests->save();
                            }
                        } else {
                            Yii::$app->getSession()->setFlash('market-' . $id . '-error', $balance['error']);
                            return $this->redirect(['/user/']);
                        }
                    } else {
                        //TODO make getErrors
                        Yii::$app->getSession()->setFlash('market-' . $id . '-error', 'Wrong api data please edit account');
                        return $this->redirect(['/user/']);
                    }
                }
            } else {
                Yii::$app->getSession()->setFlash('error', 'Unable to update this market');
                throw new HttpException(500);
            }
            Yii::$app->getSession()->setFlash('market-' . $id, 'Updated');
            return $this->redirect(['/user/']);
        } else {
            Yii::$app->getSession()->setFlash('error', 'Market not found');
            throw new HttpException(404);
        }
    }

    public function actionEdit() {
        $userParams = UserParams::find()->indexBy('id')->asArray()->all();
        $markets = Markets::find()->asArray()->indexBy('id')->all();
        $connection = Yii::$app->getDb();
        $conditionParams = [];
        $params = [];
        $conditionParams['user_id'] = Yii::$app->user->id;

        $sql = "
            SELECT  
             user_params_values.*,  
             user_params.name as param_name,
             user_params.market_id as market_id 
           FROM `user_params_values` 
           INNER JOIN user_params ON user_params.id = user_params_values.param_id
           WHERE user_id = :user_id;";

        $command = $connection->createCommand($sql, array_merge($conditionParams, $params));
        $userParamsValues = $command->queryAll();

        if ($userParamsValues) {
            foreach ($userParamsValues as $item) {
                if ($item['value']) {
                    $userParams[$item['param_id']]['value'] = $item['value'];
                }
            }
        }

        foreach ($userParams as $item) {
            $markets[$item['market_id']]['params'][$item['id']] = $item;
        }

        if (Yii::$app->request->post()) {
            $params = Yii::$app->request->post('params');
            foreach ($userParams as $i => $item) {
                $currentValue = UserParamsValues::find()
                        ->where(['user_id' => Yii::$app->user->id, 'param_id' => $item['id']])
                        ->one();
                if ($params[$i]) {
                    if ($currentValue) {
                        $currentValue->value = $params[$i];
                        $currentValue->save();
                    } else {
                        $userParamsValues = new UserParamsValues;
                        $userParamsValues->user_id = Yii::$app->user->id;
                        $userParamsValues->param_id = $i;
                        $userParamsValues->value = $params[$i];
                        $userParamsValues->save();
                        print_r($userParamsValues, 1);
                    }
                } else {
                    if ($currentValue) {
                        $currentValue->delete();
                    }
                }
            }
            Yii::$app->getSession()->setFlash('msg', 'Edited');
            return $this->redirect(['/user/edit']);
        }

        return $this->render('edit.twig', ['markets' => $markets, 'title' => 'Edit data']);
    }

}
