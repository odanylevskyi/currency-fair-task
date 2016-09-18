<?php
namespace tests\codeception\unit\models;

use app\models\TradeRateProcessor;
use Codeception\Specify;
use Yii;
use yii\codeception\TestCase;

class TradeRateProcessorTest extends TestCase
{
    use Specify;

    public $dataset;

    protected function setUp()
    {
        parent::setUp();
        $this->dataset[] = "{\"userId\":8,\"currencyFrom\":\"EUR\",\"currencyTo\":\"CHF\",\"amountSell\":472,\"amountBuy\":596,\"rate\":1.2627118644068,\"timePlaced\":\"17-09-2016 20:20:13\",\"originatingCountry\":\"CA\"}";
        $this->dataset[] = "{\"userId\":6,\"currencyFrom\":\"KRW\",\"currencyTo\":\"RUB\",\"amountSell\":735,\"amountBuy\":375,\"rate\":0.51020408163265,\"timePlaced\":\"17-09-2016 20:20:13\",\"originatingCountry\":\"AR\"}";
        $this->dataset[] = "{\"userId\":3,\"currencyFrom\":\"KRW\",\"currencyTo\":\"GBR\",\"amountSell\":179,\"amountBuy\":645,\"rate\":3.6033519553073,\"timePlaced\":\"17-09-2016 20:20:13\",\"originatingCountry\":\"FR\"}";
    }

    public function testRun()
    {
        $redis = \Yii::$app->redis;
        $i = 0;
        foreach ($this->dataset as $data) {
            $array = json_decode($data, 1);
            $message = new \app\models\TradeMessage($array);
            $message->save();
            $processor = new TradeRateProcessor($message, 'rate', 'test');
            $processor->run();
            $this->assertEquals(1, 0+$redis->exists($processor->getKey()));
            $this->assertEquals($message->rate, 0+$redis->hget($processor->getKey(), 'avg'));
            $this->assertEquals(1, 0+$redis->hget($processor->getKey(), 'count'));
            $this->assertEquals($message->currencyFrom, $redis->hget($processor->getKey(), 'from'));
            $this->assertEquals($message->currencyTo, $redis->hget($processor->getKey(), 'to'));
            $count = $redis->del($processor->getKey());
            $this->assertEquals(1, $count);
        }
    }
}
