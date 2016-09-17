<?php
/**
 * Created by PhpStorm.
 * Author: Oleksii Danylevskyi <aleksey@danilevsky.com>
 * Date: 17/09/2016
 * Time: 18:39
 */

namespace app\models\interfaces;


interface Observer 
{
    public function notify();
}