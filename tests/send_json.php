<?php
/**
 * Created by PhpStorm.
 * Author: Oleksii Danylevskyi <aleksey@danilevsky.com>
 * Date: 17/09/2016
 * Time: 18:31
 */

$curl = curl_init();

$from = ['EUR', 'USD', 'UAH', 'KRW', 'CAD'];
$to = ['GBR', 'RUB', 'JPY', 'HKD', 'CHF'];
$countries = ['IE', 'GB', 'FR', 'UA', 'AU', 'RU', 'US', 'IN', 'PK', 'CO', 'BR', 'CA', 'AR'];
$j = 0;
while($j < 3) {
    $j++;
    $i = 0;
    $sec = rand(1,3);
    $rand = rand(1, 100);
    sleep($sec);
    echo "Number of Requests: {$rand}\n";
    while($i < $rand) {
        $uid = rand(1, 10);
        $sell = rand(1, 10000);
        $buy = rand(2, 5000);
        $rate = $buy/$sell;
        $country = $countries[array_rand($countries)];
        $data = [
            "userId" => $uid,
            "currencyFrom" => $from[array_rand($from)],
            "currencyTo" => $to[array_rand($to)],
            "amountSell" => $sell,
            "amountBuy" => $buy,
            "rate" => $rate,
            "timePlaced" => date('d-m-Y H:i:s', time()),
            "originatingCountry" => $country
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://ec2-52-43-235-109.us-west-2.compute.amazonaws.com/index.php?r=consumer/index&token=5yKU4UIv9DQmO30LPW8ShMPkUXPAewrl",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
        ));

        $response = curl_exec($curl);
//        echo "Response: {$response}\n";
        $i++;
    }
}