<?php
/**
 * Created by PhpStorm.
 * Author: Oleksii Danylevskyi <aleksey@danilevsky.com>
 * Date: 17/09/2016
 * Time: 17:29
 */

namespace app\models;


use yii\base\ErrorException;
use yii\base\Model;
use yii\helpers\Json;
use Yii;
use yii\db\Exception;

class TradeMessage extends Model
{
    private $id;
    public $userId;
    public $currencyFrom;
    public $currencyTo;
    public $amountSell;
    public $amountBuy;
    public $rate;
    public $timePlaced;
    public $originatingCountry;

    protected $data;

    public function rules()
    {
        return [
            [['userId', 'currencyFrom', 'currencyTo', 'amountSell', 'amountBuy', 'rate', 'timePlaced', 'originatingCountry'], 'required'],
            [['userId'], 'integer'],
            [['currencyFrom', 'currencyTo'], 'string', 'min' => 3, 'max' => 3],
            [['originatingCountry'], 'string', 'min' => 2, 'max' => 2],
            [['amountSell', 'amountBuy', 'rate'], 'number'],
            [['timePlaced'], 'checkDate'],
        ];
    }

    public function checkDate() {
        return strtotime($this->timePlaced) !== false;
    }

    public function __construct(array $data, array $config = [])
    {
        if (empty($data)) {
            throw new ErrorException('Variable $data should not be emtpy.');
        }
        parent::__construct($config);
        $this->setAttributes($data);
        $this->setData($data);
    }

    public function save()
    {
        $redis = Yii::$app->redis;
        $this->setId($redis->get('id'));
        try {
            $redis->hset('messages', $this->getId(), Json::encode($this->attributes));
            $redis->incr('id');
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    //Getters
    /*
     * Get all attributes
     * @return - array
     */
    public function getData()
    {
        return $this->data;
    }

    public function getId()
    {
        return $this->id;
    }

    // Setters
    public function setData($values)
    {
        $this->data = $values;
    }

    public function setId($id)
    {
        if (!$id) $id = 0;
        $this->id = 'message'.$id;
    }
}