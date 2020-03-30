<?php


namespace Dz0x44\Spider\Telco;

use Carbon\Carbon;

class VtcCrawler extends CrawlerBase {
	const URL = 'https://vtcpay.vn/News/GetPromotionData?l=vi&page=1';

	public function list($dateFrom = false){

		$dateFrom = empty($dateFrom) ? Carbon::now() : Carbon::parse($dateFrom);
		$links = [];

		$crawler = $this->client->request('GET', self::URL);

		$crawler->filter('a.tt')->each(function ($node) use (&$links, $dateFrom){
			if (($date = $this->_getDateFromText($node->text())) && ($date->gte($dateFrom))) {
				$links[$node->attr('href')] = $node->text();
			}
		});

		return $links;
	}

	private function _getDateFromText($text){
		$params = explode(' ', trim($text));
		$dateString = array_pop($params);
		$dateParams = explode('/', $dateString);

		if (sizeof($dateParams) < 3){
			return false;
		}

		return Carbon::create($dateParams[2], $dateParams[1], $dateParams[0]);
	}

	public function detail($link){
		$params = explode('-', $link);
		$id = array_pop($params);
		$crawler = $this->client->request('GET', $link);

		$title = $crawler->filter('.tt-big-chitiet h1')->text();
		$content = [];

		$crawler->filter('#news_content div')->each(function ($node) use (&$content){
			$content[] = strip_tags($node->html(), '<div><strong><br>');
		});

		$params = explode(" ", $title);

		if (sizeof($content)<3){
			return false;
		}

		return [
			'id' => $id,
			'link' => $link,
			'title' => $title,
			'telco' => $params[1] ?? '',
			'image' => $crawler->filter('#news_content img')->eq(0)->attr('src'),
			'date' => array_pop($params),
			'teaser' => trim(strip_tags($content[0])),
			'content' => $content[2] ?? '',
		];
	}
}