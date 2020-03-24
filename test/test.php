<?php

require "../vendor/autoload.php";

$spider = new \Dz0x44\Spider\Telco\VtcCrawler();

$links = $spider->crawl();
dd($links);
foreach ($links as $link){
	$data = $spider->crawlDetail($link);
	dd($data); exit();
}
