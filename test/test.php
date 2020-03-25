<?php

require "../vendor/autoload.php";

$spider = new \Dz0x44\Spider\Telco\VtcCrawler();

$dateFrom = $_REQUEST["date_from"] ?? false;

$links = $spider->list($dateFrom);
dump($links);
foreach ($links as $link){
	$data = $spider->detail($link);
	dd($data); exit();
}
