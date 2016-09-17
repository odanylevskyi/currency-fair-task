<?php

namespace app\controllers;

use app\models\TradeMessage;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays currency pairs rates Chart Bar.
     *
     * @return string
     */
    public function actionRate()
    {
        $redis = Yii::$app->redis;
        $pairKeys = $redis->keys('currency:pair:*');
        $data = [];
        $dataArray = [];
        foreach($pairKeys as $key) {
            $data['labels'][] = $redis->hget($key, 'from').'/'.$redis->hget($key, 'to');
            $dataArray['label'] = 'Currency Pairs Rate';
            $dataArray['data'][] = 0 + $redis->hget($key, 'avg');
        }
        $data['datasets'][] = $dataArray;
        return $this->render('rate', [
            'data' => $data,
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
