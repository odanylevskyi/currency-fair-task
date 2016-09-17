<?php
/**
 * Created by PhpStorm.
 * Author: Oleksii Danylevskyi <aleksey@danilevsky.com>
 * Date: 17/09/2016
 * Time: 17:42
 */

namespace app\models;

use app\models\interfaces\Processor;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use app\models\TradeMessage;

class TradeRateProcessor implements Processor
{
    private $channel;
    private $message;
    private $key;

    public function __construct(TradeMessage $message, $channel = 'rate', $prefix = '')
    {
        $this->message = $message;
        $this->channel = $channel;
        $this->setKey($prefix);
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($prefix = '') {
        $this->key = $prefix.'currency:pair:' . $this->message->currencyFrom . ':' . $this->message->currencyTo;
    }

    public function run()
    {
        //Count avarage rate for currency pairs, eg. EUR/GBR
        try {
            $redis = Yii::$app->redis;
            $isExists = 0 + $redis->exists($this->key);
            if (!$isExists) {
                $redis->hset($this->key, 'count', 0);
                $redis->hset($this->key, 'from', $this->message->currencyFrom);
                $redis->hset($this->key, 'to', $this->message->currencyTo);
            }
            $count = 0 + $redis->hget($this->key, 'count');
            $avg   = 0 + $redis->hget($this->key, 'avg');
            $avg   = ($avg * $count + $this->message->rate) / ($count + 1);
            $redis->hset($this->key, 'count', $count + 1);
            $redis->hset($this->key, 'avg', $avg);
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => $this->channel,
                'message' => Json::encode([
                    'currencyFrom' => $this->message->currencyFrom,
                    'currencyTo' => $this->message->currencyTo,
                    'avg' => $avg,
                ]),
            ]);
            var_dump($this->key);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
    }
}