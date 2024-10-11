<?php


$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');

$proxies = array();
$start_count = 1;
$end_count = 10;

for ($i = $start_count; $i <= $end_count; $i++) {
    // Create URL
    curl_setopt($curl, CURLOPT_URL, "https://www.my-proxy.com/free-proxy-list-$i.html");

    // Execute cURL URL
    $result = curl_exec($curl);

    // Match proxies
    preg_match_all("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{2,4}/", $result, $matches);
    $proxies = array_merge($proxies,$matches[0]);
}


echo $proxies[array_rand($proxies)];