<?php
/**
 * MTCaptcha
 * 
 * Class to work with captcha
 * 
 * @package MediaClub
 * @subpackage tools
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class MTCaptcha {
	/**
	 * Valiable to store captcha unique key
	 * 
	 * @var string
	 */
	private $_uniqueKey;
	
	/**
	 * Valiable to store Redis config alias
	 * 
	 * @var string
	 */
	private $_redisConfigAlias;
	
	/**
	 * Valiable to store captcha settings
	 * 
	 * @var array
	 */
	private $_setting = array();
	
	/**
	 * Captcha string
	 * 
	 * @var string
	 */
	private $_string = null;
	
	/**
	 * Field to store apthabet for captcha
	 * NOTE! do not change without changing font files
	 * 
	 * @var string
	 */
	private $_alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
	
	/**
	 * Symbol's vertical fluctuation amplitude divided by 2
	 * 
	 * @var int
	 */
	private $_fluctuationAmplitude = 5;
	
	/**
	 * Increase safety by prevention of spaces between symbols
	 * 
	 * @var bool
	 */
	private $_noSpaces = false;
	
	/**
	 * Show hide credits flag
	 * 
	 * @var bool
	 */
	private $_showCredits = true;
	
	/**
	 * Class constructor
	 * 
	 * @param string $redisConfigAlias
	 * @param int $userId
	 * @throws Exception
	 * @return void
	 */
	public function __construct($redisConfigAlias, $uniqueKey)
	{
		// Process redis config alias
		if (empty($redisConfigAlias)) {
			throw new Exception('Redis config alias is empty', 'no_redis_config');
		}
		$this->_redisConfigAlias = $redisConfigAlias;
		
		// Process unique key id
		if (empty($uniqueKey)) {
			throw new Exception('Unique key is empty', 'no_unique_key');
		}
		$this->_uniqueKey = $uniqueKey;
	}
	
	/**
	 * Method to configure captcha
	 * 
	 * @param array $settings
	 * @return TCaptcha
	 */
	public function configure($settings)
	{
		$this->_setting = $settings;
		
		return $this;
	}
	
		/**
	 * Method return apthabet for captcha
	 * NOTE! do not change without changing font files
	 * 
	 * @return string
	 */
	public function getAlphabet()
	{
		return '0123456789abcdefghijklmnopqrstuvwxyz';
	}
	
	/**
	 * Method to render captcha
	 *
	 * @return bool
	 */
	public function render()
	{
		if (empty($this->_setting)) {
			return false;
		}
		
		$width = $this->_setting['width'];
		$height = $this->_setting['height'];
		
		// Prepare font
		$fontFile = dirname(__FILE__) . '/../../data/captcha/' . $this->_setting['font'] . '.png';
		$font = imagecreatefrompng($fontFile);
		imagealphablending($font, true);
		$fontFileWidth = imagesx($font);
		$fontFileHeight = imagesy($font) - 1;
		$fontMetrics = array();
		$symbol = 0;
		$readingSymbol = false;
		
		// Loading font
		$alphabetLength = strlen($this->_alphabet);
		for ($i=0; $i<$fontFileWidth && $symbol<$alphabetLength; $i++){
			$transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

			if (!$readingSymbol && !$transparent){
				$fontMetrics[$this->_alphabet{$symbol}] = array('start' => $i);
				$readingSymbol = true;
				continue;
			}

			if($readingSymbol && $transparent){
				$fontMetrics[$this->_alphabet{$symbol}]['end'] = $i;
				$readingSymbol = false;
				$symbol++;
				continue;
			}
		}
		
		// Generate random string
		$randomString = $this->generateString();
		
		// Create image canvas
		$img = imagecreatetruecolor($width, $height);
		imagealphablending($img, true);
		$white = imagecolorallocate($img, 255, 255, 255);
		$black = imagecolorallocate($img, 0, 0, 0);
		imagefilledrectangle($img, 0, 0, $width-1, $height-1, $white);
		
		// draw text
		$x=1;
		for($i=0; $i<$this->_setting['length']; $i++){
			$m = $fontMetrics[$randomString{$i}];

			$y = mt_rand(-$this->_fluctuationAmplitude, $this->_fluctuationAmplitude) + ($height - $fontFileHeight) / 2 + 2;

			if ($this->_noSpaces){
				$shift = 0;
				if ($i > 0){
					$shift = 10000;
					for ($sy=7; $sy<$fontFileHeight-20; $sy+=1){
						for ($sx = $m['start']-1; $sx<$m['end']; $sx+=1){
			        		$rgb = imagecolorat($font, $sx, $sy);
			        		$opacity = $rgb >> 24;
							if ($opacity < 127){
								$left = $sx - $m['start'] + $x;
								$py = $sy + $y;
								if( $py > $height) { 
									break;
								}
								for ($px=min($left, $width-1); $px>$left-12 && $px>=0; $px-=1){
					        		$color = imagecolorat($img, $px, $py) & 0xff;
									if ($color + $opacity < 190){
										if ($shift > $left - $px){
											$shift = $left - $px;
										}
										break;
									}
								}
								break;
							}
						}
					}
					if ($shift == 10000){
						$shift = mt_rand(4,6);
					}
				}
			} else {
				$shift = 1;
			}
			imagecopy($img, $font, $x - $shift, $y, $m['start'], 1, $m['end'] - $m['start'], $fontFileHeight);
			$x += $m['end'] - $m['start'] - $shift;
		}
		
		$center=$x/2;
		
		// Add credits and background
		$img2 = imagecreatetruecolor($width, $height + ($this->_showCredits ? 12 : 0));
		$foregroundColor = array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
		$backgroundColor = array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
		$foreground = imagecolorallocate($img2, $foregroundColor[0], $foregroundColor[1], $foregroundColor[2]);
		$background = imagecolorallocate($img2, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		imagefilledrectangle($img2, 0, 0, $width-1, $height-1, $background);		
		imagefilledrectangle($img2, 0, $height, $width-1, $height+12, $foreground);
		$credits = $_SERVER['HTTP_HOST'];
		imagestring($img2, 2, $width / 2 - imagefontwidth(2) * strlen($credits) / 2, $height - 2, $credits, $background);

		// periods
		$rand1 = mt_rand(750000,1200000)/10000000;
		$rand2 = mt_rand(750000,1200000)/10000000;
		$rand3 = mt_rand(750000,1200000)/10000000;
		$rand4 = mt_rand(750000,1200000)/10000000;
		// phases
		$rand5 = mt_rand(0,31415926)/10000000;
		$rand6 = mt_rand(0,31415926)/10000000;
		$rand7 = mt_rand(0,31415926)/10000000;
		$rand8 = mt_rand(0,31415926)/10000000;
		// amplitudes
		$rand9 = mt_rand(330,420)/110;
		$rand10 = mt_rand(330,450)/110;

		// Wave distortion
		for ($x=0; $x<$width; $x++){
			for ($y=0; $y<$height; $y++){
				$sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - $width / 2 + $center + 1;
				$sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

				if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1){
					continue;
				} else {
					$color = imagecolorat($img, $sx, $sy) & 0xFF;
					$colorX = imagecolorat($img, $sx+1, $sy) & 0xFF;
					$colorY = imagecolorat($img, $sx, $sy+1) & 0xFF;
					$colorXy = imagecolorat($img, $sx+1, $sy+1) & 0xFF;
				}

				if ($color == 255 && $colorX == 255 && $colorY == 255 && $colorXy == 255){
					continue;
				} else if ($color == 0 && $colorX == 0 && $colorY == 0 && $colorXy == 0){
					$newRed = $foregroundColor[0];
					$newGreen = $foregroundColor[1];
					$newBlue = $foregroundColor[2];
				} else {
					$frsx = $sx - floor($sx);
					$frsy = $sy - floor($sy);
					$frsx1 = 1 - $frsx;
					$frsy1 = 1 - $frsy;

					$newcolor = (
						$color * $frsx1 * $frsy1+
						$colorX * $frsx * $frsy1+
						$colorY * $frsx1 * $frsy+
						$colorXy * $frsx * $frsy
					);

					if ($newcolor > 255) { 
						$newcolor = 255;
					}
					$newcolor = $newcolor / 255;
					$newcolor0 = 1 - $newcolor;

					$newRed = $newcolor0 * $foregroundColor[0] + $newcolor * $backgroundColor[0];
					$newGreen = $newcolor0 * $foregroundColor[1] + $newcolor * $backgroundColor[1];
					$newBlue = $newcolor0 * $foregroundColor[2] + $newcolor * $backgroundColor[2];
				}

				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newRed, $newGreen, $newBlue));
			}
		}

		// Send headers
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header('Cache-Control: post-check=0, pre-check=0', FALSE); 
		header('Pragma: no-cache');
		
		// Send image
		if(function_exists("imagejpeg")){
			header("Content-Type: image/jpeg");
			imagejpeg($img2, null, 90);
		}else if(function_exists("imagegif")){
			header("Content-Type: image/gif");
			imagegif($img2);
		}else if(function_exists("imagepng")){
			header("Content-Type: image/x-png");
			imagepng($img2);
		}
	}
	
	/**
	 * Method return foreground color
	 */
	public function getForegroundColor()
	{
		return array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
	}
	
	/**
	 * Method to compare string with stored string
	 * 
	 * @param string $string
	 * @return bool
	 */
	public function validate($string)
	{
		if (empty($string)) {
			return false;
		}
		
		// Get stored string from cache
		$storedString = TRedis::instance($this->_redisConfigAlias)->get(
			'captcha_' . $this->_setting['key'] . '_' . $this->_uniqueKey
		);
		if (empty($storedString)) {
			return false;
		}
		
		// Compare
		return $string == $storedString;
	}
	
	/**
	 * Method to clear stored string
	 * 
	 * @return bool
	 */
	public function clear()
	{
		return TRedis::instance($this->_redisConfigAlias)->delete(
			'captcha_' . $this->_setting['key'] . '_' . $this->_uniqueKey
		);
	}
	
	/**
	 * Method return random string
	 * 
	 * @param bool $regenerate
	 * @return string
	 */
	public function generateString($regenerate = false)
	{
		if (isset($this->_string) && !$regenerate) {
			return $this->_string;
		}
		
		$allowedSymbols = $this->getAllowedSymbols();
		$length = $this->_setting['length'];
		$this->_string = '';
		while (true) {
			$this->_string = '';
			for ($i=0; $i<$length; $i++){
				$this->_string .= $allowedSymbols{mt_rand(0, strlen($allowedSymbols) - 1)};
			}
			if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $allowedSymbols)) { 
				break;
			}
		}
		
		// Store into Redis
		TRedis::instance($this->_redisConfigAlias)->set(
			'captcha_' . $this->_setting['key'] . '_' . $this->_uniqueKey, $this->_string
		);
		
		return $this->_string;
	}
	
	/**
	 * Method return allowed symbols
	 * 
	 * @return string
	 */
	public function getAllowedSymbols()
	{
		switch ($this->_setting['allowedSymbols']) {
			case 'didgits':
				return '0123456789';
			break;
			
			case 'alphabet':
				return 'abcdefghijklmnopqrstuvwxyz';
			break;
			
			case 'both':
			default:
				return '23456789abcdeghkmnpqsuvxyz';
			break;
		}
	}
}