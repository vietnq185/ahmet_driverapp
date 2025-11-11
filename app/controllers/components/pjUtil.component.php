<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	static public function getClientIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		} else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'UNKNOWN';
	}
	
	static public function textToHtml($content)
	{
		$content = preg_replace('/\r\n|\n/', '<br />', $content);
		return '<html><head><title></title></head><body>'.$content.'</body></html>';
	}
	
	static public function formatPhone($value)
	{
		$value = trim($value);
		$value = preg_replace('/^\+/', '00', $value);
		$value = preg_replace('/\D+/', '', $value);
		
		return $value;
	}
	
	public static function formatCurrencySign($price, $currency, $separator = " ")
	{
		switch ($currency)
		{
			case 'USD':
				$format = "$" . $separator . $price;
				break;
			case 'GBP':
				$format = "&pound;" . $separator . $price;
				break;
			case 'EUR':
				$format = "&euro;" . $separator . $price;
				break;
			case 'JPY':
				$format = "&yen;" . $separator . $price;
				break;
			case 'AUD':
			case 'CAD':
			case 'NZD':
			case 'CHF':
			case 'HKD':
			case 'SGD':
			case 'SEK':
			case 'DKK':
			case 'PLN':
				$format = $price . $separator . $currency;
				break;
			case 'NOK':
			case 'HUF':
			case 'CZK':
			case 'ILS':
			case 'MXN':
				$format = $currency . $separator . $price;
				break;
			default:
				$format = $price . $separator . $currency;
				break;
		}
		return $format;
	}
	
	public static function getField($key, $return=false, $escape=false)
	{
		if (pjObject::getPlugin('pjWebsiteContent') !== NULL)
		{
			return pjWebsiteContentUtil::getField($key, $return, $escape);
		} else {
			return pjToolkit::getField($key, $return, $escape);
		}
	
	}
	
	public static function toMomemtJS($format)
	{
		$f = str_replace(
			array('Y', 'm', 'n', 'd', 'j'), 
			array('YYYY', 'MM', 'M', 'DD', 'D'), 
			$format
		);
		
		return $f;
	}
	
	static public function convertDateTime($date_time, $date_format, $time_format)
	{
		if(count(explode(" ", $date_time)) == 3)
		{
			list($_date, $_time, $_period) = explode(" ", $date_time);
			$iso_time = pjDateTime::formatTime($_time . ' ' . $_period, $time_format);
		}else{
			list($_date, $_time) = explode(" ", $date_time);
			$iso_time = pjDateTime::formatTime($_time, $time_format);
		}
		$iso_date = pjDateTime::formatDate($_date, $date_format);
		$iso_date_time = $iso_date . ' ' . $iso_time;
		$ts = strtotime($iso_date_time);
		
		return compact('iso_date', 'iso_time', 'iso_date_time', 'ts');
	}
	
	static public function getPostMaxSize()
	{
		$post_max_size = ini_get('post_max_size');
		switch (substr($post_max_size, -1))
		{
			case 'G':
				$post_max_size = (int) $post_max_size * 1024 * 1024 * 1024;
				break;
			case 'M':
				$post_max_size = (int) $post_max_size * 1024 * 1024;
				break;
			case 'K':
				$post_max_size = (int) $post_max_size * 1024;
				break;
		}
		return $post_max_size;
	}
	
	static public function getWeekRange($date, $week_start)
	{
		$week_arr = array(
				0=>'sunday',
				1=>'monday',
				2=>'tuesday',
				3=>'wednesday',
				4=>'thursday',
				5=>'friday',
				6=>'saturday');
			
		$ts = strtotime($date);
		$start = (date('w', $ts) == $week_start) ? $ts : strtotime('last ' . $week_arr[$week_start], $ts);
		$week_start = ($week_start == 0 ? 6 : $week_start -1);
		return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next ' . $week_arr[$week_start], $start)));
	}
	
	static public function sortArrayByArray(Array $array, Array $orderArray) {
		$ordered = array();
		foreach($orderArray as $key) 
		{
			if(array_key_exists($key,$array)) 
			{
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}

    public static function sortWeekDays($week_start, $days)
    {
        if($week_start > 0){
            $arr = array();
            for($i = $week_start; $i <= 6; $i++)
            {
                $arr[] = $i;
            }
            for($i = 0; $i < $week_start; $i++)
            {
                $arr[] = $i;
            }
            return pjUtil::sortArrayByArray($days, $arr);
        }else{
            return $days;
        }
    }
    
    public static function toBootstrapDate($format)
    {
    	return str_replace(
    			array('Y', 'm', 'n', 'd', 'j'),
    			array('yyyy', 'mm', 'm', 'dd', 'd'),
    			$format);
    }
    
    public static function getHourMinFromSeconds($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = round(($seconds - $hours * 3600) / 60);
        return compact('hours', 'mins');
    }
    
    public static function getSixMonths()
    {
    	$result = array();
    	$i = 1;
    	$month = time();
    	while($i <= 6)
    	{
    		$result[$i]['year'] = date('Y', $month);
    		$result[$i]['month'] = date('m', $month);
    		$month = strtotime('-1 month', $month);
    		$i++;
    	}
    	krsort($result);
    	return $result;
    }
    
	public static function getTitles(){
		$arr = array();
		$arr[] = 'mr';
		$arr[] = 'mrs';
		$arr[] = 'ms';
		$arr[] = 'dr';
		$arr[] = 'prof';
		$arr[] = 'rev';
		$arr[] = 'other';
		return $arr;
	}
}
?>