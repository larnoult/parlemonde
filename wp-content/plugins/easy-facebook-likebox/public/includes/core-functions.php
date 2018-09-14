<?php
if(!function_exists('efbl_time_ago')){ 
	function efbl_time_ago($date,$granularity=2) {
		//Preparing strings to translate
		$date_time_strings = array("second" => __('second', 'easy-facebook-likebox'), 
								   "seconds" =>  __('seconds', 'easy-facebook-likebox'), 
								   "minute" => __('minute', 'easy-facebook-likebox'), 
								   "minutes" => __('minutes', 'easy-facebook-likebox'), 
								   "hour" => __('hour', 'easy-facebook-likebox'), 
								   "hours" => __('hours', 'easy-facebook-likebox'), 
								   "day" => __('day', 'easy-facebook-likebox'), 
								   "days" => __('days', 'easy-facebook-likebox'),
								   "week" => __('week', 'easy-facebook-likebox'),
								   "weeks" => __('weeks', 'easy-facebook-likebox'), 
								   "month"  => __('month', 'easy-facebook-likebox'), 
								   "months"  => __('months', 'easy-facebook-likebox'), 
								   "year" => __('year', 'easy-facebook-likebox'),  
								   "years" => __('years', 'easy-facebook-likebox'),
								   "decade" => __('decade', 'easy-facebook-likebox'),
								   );
		
		$ago_text = __('ago', 'easy-facebook-likebox');
		$date = strtotime($date);
		$difference = time() - $date;
		$periods = array('decade' => 315360000,
			'year' => 31536000,
			'month' => 2628000,
			'week' => 604800, 
			'day' => 86400,
			'hour' => 3600,
			'minute' => 60,
			'second' => 1);
	
		foreach ($periods as $key => $value) {
			if ($difference >= $value) {
				$time = floor($difference/$value);
				$difference %= $value;
				$retval .= ($retval ? ' ' : '').$time.' ';
				$retval .= (($time > 1) ? $date_time_strings[$key.'s'] : $date_time_strings[$key] );
				$granularity--;
			}
			if ($granularity == '0') { break; }
		}
		 
		return ''.$retval.' '.$ago_text;      
	}
}

if(!function_exists('jws_fetchUrl')){
//Get JSON object of feed data
	function jws_fetchUrl($url){
		//Can we use cURL?
		if(is_callable('curl_init')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$feedData = curl_exec($ch);
			curl_close($ch);
		//If not then use file_get_contents
		} elseif ( ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') === TRUE ) {
			$feedData = @file_get_contents($url);
		//Or else use the WP HTTP API
		} else {
			if( !class_exists( 'WP_Http' ) ) include_once( ABSPATH . WPINC. '/class-http.php' );
			$request = new WP_Http;
			$result = $request->request($url);
			$feedData = $result['body'];
		}
	/*    echo $feedData;
		exit;*/
		return $feedData;
		
	}
}

if(!function_exists('ecff_stripos_arr')){
	function ecff_stripos_arr($haystack, $needle) {
		 
		if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $what) {
			if(($pos = stripos($haystack, ltrim($what) ))!==false) return $pos;
		}
		return false;
	}
}

if(!function_exists('ecff_makeClickableLinks')){
	function ecff_makeClickableLinks($text)
	{
		return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $text);
		
	}
}

if(!function_exists('ecff_hastags_to_link')){
	function ecff_hastags_to_link($text){
		
		return preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1#<a href="https://www.facebook.com/hashtag/\2" target="_blank">\2</a>', $text);
	}
}

if(!function_exists('efbl_parse_url')){
	function efbl_parse_url($url){
		$fb_url = parse_url( $url );
		$fanpage_url = str_replace('/', '', $fb_url['path']);

		return $fanpage_url;

	}
}

if(!function_exists('efbl_get_locales')){
	/**
	 * Compile and filter the list of locales.
	 *
	 *
	 * @return return the list of locales.
	 */
	function efbl_get_locales(){

				$locales = array(  'af_ZA' => 'Afrikaans', 
						   'ar_AR' => 'Arabic', 
						   'az_AZ' => 'Azeri', 
						   'be_BY' => 'Belarusian', 
						   'bg_BG' => 'Bulgarian', 
						   'bn_IN' => 'Bengali', 
						   'bs_BA' => 'Bosnian', 
						   'ca_ES' => 'Catalan', 
						   'cs_CZ' => 'Czech', 
						   'cy_GB' => 'Welsh', 
						   'da_DK' => 'Danish', 
						   'de_DE' => 'German', 
						   'el_GR' => 'Greek', 
						   'en_US' => 'English (US)', 
						   'en_GB' => 'English (UK)', 
						   'eo_EO' => 'Esperanto', 
						   'es_ES' => 'Spanish (Spain)', 
						   'es_LA' => 'Spanish', 
						   'et_EE' => 'Estonian', 
						   'eu_ES' => 'Basque', 
						   'fa_IR' => 'Persian', 
						   'fb_LT' => 'Leet Speak', 
						   'fi_FI' => 'Finnish', 
						   'fo_FO' => 'Faroese', 
						   'fr_FR' => 'French (France)', 
						   'fr_CA' => 'French (Canada)', 
						   'fy_NL' => 'NETHERLANDS (NL)', 
						   'ga_IE' => 'Irish', 
						   'gl_ES' => 'Galician', 
 						   'hi_IN' => 'Hindi', 
						   'hr_HR' => 'Croatian', 
						   'hu_HU' => 'Hungarian', 
						   'hy_AM' => 'Armenian', 
						   'id_ID' => 'Indonesian', 
						   'is_IS' => 'Icelandic', 
						   'it_IT' => 'Italian', 
						   'ja_JP' => 'Japanese', 
						   'ka_GE' => 'Georgian', 
						   'km_KH' => 'Khmer', 
						   'ko_KR' => 'Korean', 
						   'ku_TR' => 'Kurdish', 
						   'la_VA' => 'Latin', 
						   'lt_LT' => 'Lithuanian', 
						   'lv_LV' => 'Latvian', 
						   'mk_MK' => 'Macedonian', 
						   'ml_IN' => 'Malayalam', 
						   'ms_MY' => 'Malay', 
						   'nb_NO' => 'Norwegian (bokmal)', 
						   'ne_NP' => 'Nepali', 
						   'nl_NL' => 'Dutch', 
						   'nn_NO' => 'Norwegian (nynorsk)', 
						   'pa_IN' => 'Punjabi', 
						   'pl_PL' => 'Polish', 
						   'ps_AF' => 'Pashto', 
						   'pt_PT' => 'Portuguese (Portugal)', 
						   'pt_BR' => 'Portuguese (Brazil)', 
						   'ro_RO' => 'Romanian', 
						   'ru_RU' => 'Russian', 
						   'sk_SK' => 'Slovak', 
						   'sl_SI' => 'Slovenian', 
						   'sq_AL' => 'Albanian', 
						   'sr_RS' => 'Serbian', 
						   'sv_SE' => 'Swedish', 
						   'sw_KE' => 'Swahili', 
						   'ta_IN' => 'Tamil', 
						   'te_IN' => 'Telugu', 
						   'th_TH' => 'Thai', 
						   'tl_PH' => 'Filipino', 
						   'tr_TR' => 'Turkish', 
						   'uk_UA' => 'Ukrainian',
						   'ur_PK' => 'Urdu',
 						   'vi_VN' => 'Vietnamese', 
						   'zh_CN' => 'Simplified Chinese (China)', 
						   'zh_HK' => 'Traditional Chinese (Hong Kong)', 
						   'zh_TW' => 'Traditional Chinese (Taiwan)',
						   );
			
			return apply_filters( 
				'efbl_locale_names',
				$locales
			);	
	}
}
if( !function_exists( 'get_css3_animations' ) ){	
	function get_css3_animations(){

		$css3_effects = array(
							'Static' => array(
									'No Effect',
							),
							'Attention Seekers' => array(
									'bounce',
									'flash',
									'pulse',
									'rubberBand',
									'shake',
									'swing',
							),

							'Bouncing Entrances' => array(
									'bounceIn',
									'bounceInDown',
									'bounceInLeft',
									'bounceInRight',
									'bounceInUp',
							),

							'Fading Entrances' => array(
									'fadeIn',
									'fadeInDown',
									'fadeInDownBig',
									'fadeInLeft',
									'fadeInLeftBig',
									'fadeInRight',
									'fadeInRightBig',
									'fadeInUp',
									'fadeInUpBig',
							),

							'Flippers' => array(
									'flip',
									'flipInX',
									'flipInY',
									'flipOutX',
									'flipOutY',						
							),

							'Rotating Entrances' => array(
									'rotateIn',
									'rotateInDownLeft',
									'rotateInDownRight',
									'rotateInUpLeft',
									'rotateInUpRight',
							),

							'Sliding Entrances' => array(
									'slideInUp',
									'slideInDown',
									'slideInLeft',
									'rotateInUpLeft',
									'slideInRight',
							),

							'Zoom Entrances' => array(
									'zoomIn',
									'zoomInDown',
									'zoomInLeft',
									'zoomInRight',
									'zoomInUp',
							),

							'Specials' => array(
									'hinge',
									'rollIn',
									'rollOut',
							),
				);

		return apply_filters( 
				'efbl_css3_effects',
				$css3_effects
			);

	}
}