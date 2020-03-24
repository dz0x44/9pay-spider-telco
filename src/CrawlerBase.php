<?php


namespace Dz0x44\Spider\Telco;


use Goutte\Client;
use Symfony\Component\BrowserKit\CookieJar;

class CrawlerBase{

	protected $client = null;

	protected function saveCookie(){
//		$cookieJar = $this->client->getCookieJar();
//
//		file_put_contents(self::COOKIE_FILE, serialize($cookieJar->all()));
	}

	public function __construct(){
		$cookieJar = new CookieJar();
		$this->client = new Client(null, null, $cookieJar);
	}
}