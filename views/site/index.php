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
                            <tr><td colspan="6">No activity</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>