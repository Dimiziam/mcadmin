<?php
/**
 * MTCharts
 * 
 * Class to parse charts sites
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTCharts
{
	/**
	 * Method to parse charts site and return items list
	 * 
	 * @param int $sourceId
	 * @return mixed
	 */
	public static function getItems($sourceId)
	{
		// Get chart source by id
		echo $sourceId;
		$source = ChartSource::model()->findByPk($sourceId);



		if (empty($source)) {
			return false;
		}

		// Check URL
		if (empty($source->url)) {
			return false;
		}

		// Parse url
		$parts = parse_url($source->url);


		// Prepare host
		$parts['host'] = str_replace('www.', '', $parts['host']);

		// Handle by host
		switch ($parts['host']) {
			case 'allcharts.org':
				$items = self::allCharts($source->url);
				break;

			case 'acharts.us':
				$items = self::aCharts($source->url);
				break;

			case 'hotcharts.ru':
				$items = self::hotCharts($source->url);
				break;

			case 'top40-charts.com':
				$items = self::top40Charts($source->url);
				break;

			case 'officialcharts.com':
				$items = self::officialCharts($source->url);
				break;

			case 'billboard.com':
				$items = self::billboard($source->url);
				break;

			case 'swisscharts.com':
				$items = self::swissCharts($source->url);
				break;

			case 'artists.letssingit.com':
				$items = self::artistsLetssingIt($source->url);
				break;

			case 'snkhan.co.uk':
				$items = self::snkhan($source->url);
				break;

			default:
				return false;
		}

		return $items;
	}

	/**
	 * Method to get charts info from snkhan.co.uk
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function snkhan($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}

		// Parse
		preg_match_all('$<!-- ([^\-]+) - ([^\-]+) -->$is', $content, $matches);
		if (empty($matches[2])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[2] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => htmlspecialchars_decode(trim(strip_tags($singer))),
				'album' => htmlspecialchars_decode(trim($matches[1][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from artists.letssingit.com
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function artistsLetssingIt($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}

		// Parse
		preg_match_all('$<A href="[^\"]+" class=[^\<\>]+><B>([^\<\>]+)</B></A><BR><A href="[^\"]+" class=[^\<\>]+>([^\<\>]+)</A>$is', $content, $matches);
		if (empty($matches[2])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[1] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => htmlspecialchars_decode(trim(strip_tags($singer))),
				'album' => htmlspecialchars_decode(trim($matches[2][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from swisscharts.com
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function swissCharts($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}

		// Parse
		preg_match_all('$<a href="/album/[^\<\>]+" class="navb" style="text-decoration:none;">(.*?)<br>([^\<\>]+)</a>$is', $content, $matches);
		if (empty($matches[2])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[1] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => iconv('iso-8859-1', 'utf-8', htmlspecialchars_decode(trim(strip_tags($singer)))),
				'album' => iconv('iso-8859-1', 'utf-8', htmlspecialchars_decode(trim($matches[2][$index]))),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from billboard.com
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function billboard($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}

		// Parse
		preg_match_all('$<h1>([^\<\>]+)</h1>[^\<\>]+<p class="chart_info">(.*?)</p>$is', $content, $matches);
		if (empty($matches[2])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[2] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => htmlspecialchars_decode(trim(strip_tags($singer))),
				'album' => htmlspecialchars_decode(trim($matches[1][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from officialcharts.com
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function officialCharts($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}

		// Parse
		preg_match_all('/<span>([^\<\>]+)</span></a><br><a href="\S*" class="chart-artist text-lg inline-block"><span>([^\<\>]+)</span>/', $content, $matches);
		// preg_match_all('$<span>([^\<\>]+)</span></a><br><a href="\S*" class="chart-artist text-lg inline-block"><span>([^\<\>]+)</span>$is', $content, $matches);
		// preg_match_all('$<h3>([^\<\>]+)</h3>[^\<\>]+<h4>([^\<\>]+)</h4>$is', $content, $matches);
		if (empty($matches[2])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[2] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => htmlspecialchars_decode(ucwords(strtolower(trim($singer)))),
				'album' => htmlspecialchars_decode(ucwords(strtolower(trim($matches[1][$index])))),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from top40-charts.com
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function top40Charts($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}

		// Parse
		preg_match_all('$href="/song\.php\?sid=[0-9]+">([^\<\>]+)</a></b></div><a style="text-decoration: none;\s*"\s*href="/artist\.php\?aid=[0-9]+">([^\<\>]+)</a></td>$is', $content, $matches);
		if (empty($matches[2])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[2] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => htmlspecialchars_decode(trim($singer)),
				'album' => htmlspecialchars_decode(trim($matches[1][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from hotcharts.ru
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function hotCharts($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}
		$content = str_replace("\n", '', $content);
		$content = str_replace("’", "'", $content);

		// Parse
		preg_match_all('$<span class="artist_name">([^\<\>]+)</span></a><a href="/mp3/\?song=[0-9]+" title="[^\"]*"><span class="song_name">([^\<\>]+)</span></a>$is', $content, $matches);
		if (empty($matches[1])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[1] as $index => $singer) {
			$items[] = array(
				'position' => $index + 1,
				'singer' => htmlspecialchars_decode(trim($singer)),
				'album' => htmlspecialchars_decode(trim($matches[2][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from acharts.us
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function aCharts($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}
		$content = str_replace("\n", '', $content);

		// Get first
		preg_match_all('$<acronym title="artist\(s\) / band\(s\)"><a href="[^\<\>]+">([^\<\>]+)</a></acronym> <br> <acronym title="album title"><a href="[^\<\>]+" class="Title">([^\<\>]+)</a></acronym>$is', $content, $first);
		if (empty($first[1])) {
			return false;
		}

		// Get positions
		preg_match_all('$<td class="No">\s*([0-9]+)$is', $content, $positions);
		if (empty($positions[1])) {
			return false;
		}

		// Get names
		preg_match_all('$<a href="[^\"]+">([^\<\>]+)</a> <br> <a href="[^\"]+" class="Title">([^\<\>]+)</a>$is', $content, $names);
		if (empty($names[1])) {
			return false;
		}

		// Prepare items
		$items = array(
			array(
				'position' => 1,
				'singer' => htmlspecialchars_decode(trim($first[1][0])),
				'album' => htmlspecialchars_decode(trim($first[2][0])),
			)
		);
		foreach ($positions[1] as $index => $position) {
			$items[] = array(
				'position' => $position,
				'singer' => htmlspecialchars_decode(trim($names[1][$index])),
				'album' => htmlspecialchars_decode(trim($names[2][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to get charts info from allcharts.org
	 * 
	 * @param string $url
	 * @return mixed
	 */
	public static function allCharts($url)
	{
		// Get page content
		$content = self::sendRequest($url);
		if (empty($content)) {
			return false;
		}
		$content = str_replace('</font>', '', $content);

		// Parse
		preg_match_all('$<tr align=center bgcolor=[^\<\>]+><td rowspan=2><b>([0-9]+)</b></td><td rowspan=2>[^\<\>]+</td><td rowspan=2>[^\<\>]+</td><td rowspan=2><b>"([^\<\>]+)"</b>([^\<\>]+)</td><td width=80 bgcolor=#f7efe7>$is', $content, $matches);
		if (empty($matches[1])) {
			return false;
		}

		// Prepare items
		$items = array();
		foreach ($matches[1] as $index => $position) {
			$items[] = array(
				'position' => $position,
				'singer' => htmlspecialchars_decode(trim($matches[3][$index])),
				'album' => htmlspecialchars_decode(trim($matches[2][$index])),
			);
		}

		return $items;
	}

	/**
	 * Method to send request to charts site
	 * 
	 * @param string $url
	 * @return string
	 */
	public static function sendRequest($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;

	}
}