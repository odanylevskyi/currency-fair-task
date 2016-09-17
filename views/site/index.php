<?php

/* @var $this yii\web\View */

$this->title = 'CurrencyFair Task';
?>
<div class="site-index">
    <h3>CurrencyFair - Task!</h3>
    <div class="body-content">
        <div class="row">
            <div class="col-lg-6">
                <div id="vmap" style="width: 550px; height: 422px;"></div>
            </div>
            <div class="col-lg-6">
                <div class="messages">
                    <table class="table-bordered table-responsive" style="width: 100%; text-align: center; max-height: 400px; overflow-y: auto;">
                        <thead>
                            <th>UserID</th>
                            <th>Country</th>
                            <th>Currency From/To</th>
                            <th>Amount Sell/Buy</th>
                            <th>Rate</th>
                            <th>Date/Time</th>
                        </thead>
                        <tbody>
                            <?php if(empty($data)):?>
                                <tr><td colspan="6">No activity</td></tr>
                            <?php else:?>
                                <?php for($i = count($data)-1; $i >= count($data)-18-1; $i--):?>
                                    <tr>
                                        <td><?=$data[$i]['userId']?></td>
                                        <td><?=$data[$i]['originatingCountry']?></td>
                                        <td><?=$data[$i]['currencyFrom'].'/'.$data[$i]['currencyTo']?></td>
                                        <td><?=$data[$i]['amountSell'].'/'.$data[$i]['amountBuy']?></td>
                                        <td><?=number_format($data[$i]['rate'], 2)?></td>
                                        <td><?=$data[$i]['timePlaced']?></td>
                                    </tr>
                                <?php endfor;?>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>