<?php


namespace Dz0x44\Spider\Telco;

use Carbon\Carbon;

class VtcCrawler extends CrawlerBase {
	const URL = 'https://vtcpay.vn/News/GetPromotionData?l=vi&page=1';

	public function crawl(){
		$crawler = $this->client->request('GET', self::URL);

		$links = [];
		$crawler->filter('a.tt')->each(function ($node) use (&$links){
			if (($date = $this->_getDateFromText($node->text())) && $this->_isValidDate($date)) {
				$links[] = $node->attr('href');
			}
		});

		return $links;
	}

	private function _getDateFromText($text){
		$params = explode(' ', $text);
		$dateString = array_pop($params);
		$dateParams = explode('/', $dateString);

		if (sizeof($dateParams) < 3){
			return false;
		}

		return Carbon::create($dateParams[2], $dateParams[1], $dateParams[0]);
	}

	public function crawlDetail($link){
		$crawler = $this->client->request('GET', $link);

		$title = $crawler->filter('.tt-big-chitiet h1')->text();
		$content = [];

		$crawler->filter('#news_content div')->each(function ($node) use (&$content){
			$html = strip_tags($node->html(), '<div><strong>');
			$content[] = nl2br(trim($html));
		});

		$params = explode(" ", $title);

		if (sizeof($content)<3){
			return false;
		}

		return [
			'title' => $title,
			'telco' => $params[1] ?? '',
			'image' => $crawler->filter('#news_content img')->eq(0)->attr('src'),
			'date' => array_pop($params),
			'teaser' => trim(strip_tags($content[0])),
			'content' => $content[2] ?? '',
		];
	}

	private function _isValidDate($date){
		return $date >= Carbon::now();
	}
}