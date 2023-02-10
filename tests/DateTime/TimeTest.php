<?php
date_default_timezone_set("Asia/Jakarta");
$date = new DateTime('2023-02-12');
echo $date->format("Y-m-d H:i:s") . PHP_EOL;
echo $date->getTimestamp() . PHP_EOL;
echo time();
