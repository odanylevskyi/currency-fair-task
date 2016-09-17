<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\TradeMessage;
use app\models\TradeObserver;
use app\models\TradeRateProcessor;

class ConsumerController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Message consumer action.
     *
     * @return string
     */
    public function actionIndex($token = null)
    {
        if (!$token || !in_array($token, Yii::$app->params['tokens'])) {
            throw new ErrorException('You have no access to this page.');
        }
        $jsonString = file_get_contents('php://input');
        if (strlen($jsonString) <= 0) {
            return false;
        }
        $data = json_decode($jsonString, 1);
        try {
            //Save message in database
            $message = new TradeMessage($data);
            //Validate accepted data
            if(!$message->validate()) {
                $errors = array_values($message->getErrors());
                if (isset($errors[0]) && isset($errors[0][0])) {
                    throw new ErrorException($errors[0][0]);
                } else {
                    throw new ErrorException('Some of TradeMessage attributes are not valid.');
                }
            }
            //Save message
            if (!$message->save()) {
                throw new ErrorException('An error occurs when saving trade message.');
            }

            //Run observer to trigger socket.io and update frontend global map
            $observer = new TradeObserver($message);
            $observer->notify();

            //Run processor to trigger socket.io and update bar charts
            $processor = new TradeRateProcessor($message);
            $processor->run();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
