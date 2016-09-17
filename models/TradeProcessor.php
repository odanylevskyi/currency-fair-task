<?php
/**
 * Created by PhpStorm.
 * Author: Oleksii Danylevskyi <aleksey@danilevsky.com>
 * Date: 17/09/2016
 * Time: 11:42
 */

namespace app\models;

use app\models\interfaces\Processor;
use Yii;
use yii\helpers\Json;
use app\models\TradeMessage;

class TradeRateProcessor implements Processor
{
    private $channel;
    private $message;

    public function __construct(TradeMessage $message, $channel = 'rate')
    {
        $this->message = $message;
        $this->channel = $channel;
    }

    public function run()
    {
        //Count avarage rate for currency pairs, eg. EUR/GBR
        $redis = Yii::$app->redis;
        if (!$redis->exists('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo)) {
            $redis->hset('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'count', 0);
            $redis->hset('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'from', $this->message->currencyFrom);
            $redis->hset('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'to', $this->message->currencyTo);
        }
        $count = $redis->hget('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'count');
        $avg   = $redis->hget('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'avg');
        $avg = ($avg*$count+$this->message->rate)/($count+1);
        $redis->hset('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'count', $count+1);
        $redis->hset('currency:pair:'.$this->message->currencyFrom.':'.$this->message->currencyTo, 'avg', $avg);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => $this->channel,
            'message' => Json::encode([
                'currencyFrom' => $this->message->currencyFrom,
                'currencyTo' => $this->message->currencyTo,
                'avg' => $avg,
            ]),
        ]);
    }
}