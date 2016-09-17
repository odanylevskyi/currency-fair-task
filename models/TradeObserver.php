<?php
/**
 * Created by PhpStorm.
 * Author: Oleksii Danylevskyi <aleksey@danilevsky.com>
 * Date: 17/09/2016
 * Time: 17:42
 */

namespace app\models;

use app\models\interfaces\Observer;
use Yii;
use yii\helpers\Json;
use app\models\TradeMessage;

class TradeObserver implements Observer
{
    private $channel;
    private $message;

    public function __construct(TradeMessage $message, $channel = 'notification')
    {
        $this->message = $message;
        $this->channel = $channel;
    }

    public function notify()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => $this->channel,
            'message' => Json::encode($this->message->data),
        ]);
    }
}