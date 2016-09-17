<?php

/* @var $this yii\web\View */

$this->title = 'CurrencyFair Task';
?>
<div class="site-index">
    <h3>CurrencyFair - Task!</h3>
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12" style="margin-top: 30px;">
                <canvas id="myChart" width="400" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(document).ready(function() {
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: <?=json_encode($data)?>,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    })
</script>
