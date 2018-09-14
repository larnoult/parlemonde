<?php
/**
 * Central class for helper methods
 */
class MMP_Globals {
	/**
	 * Sanitizes popuptext
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function sanitize_popuptext($popuptext, $strict = false, $linebreaks = false) {
		global $allowedposttags;
		$lmm_options = get_option('leafletmapsmarker_options');
		$sanitize_from = array(
			'#<ul(.*?)>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
			'#</li>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
			'#</li>(\s)*(<br\s*/?>)*(\s)*</ul>#si',
			'#<ol(.*?)>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
			'#</li>(\s)*(<br\s*/?>)*(\s)*</ol>#si',
			'#(<br\s*/?>){1}\s*<ul(.*?)>#si',
			'#(<br\s*/?>){1}\s*<ol(.*?)>#si',
			'#</ul>\s*(<br\s*/?>){1}#si',
			'#</ol>\s*(<br\s*/?>){1}#si',
		);
		$sanitize_to = array(
			'<ul$1><li$5>',
			'</li><li$4>',
			'</li></ul>',
			'<ol$1><li$5>',
			'</li></ol>',
			'<ul$2>',
			'<ol$2>',
			'</ul>',
			'</ol>'
		);
		if ($strict) {
			if ($linebreaks) {
				$sanitized = preg_replace($sanitize_from, $sanitize_to, stripslashes(str_replace('\\\\', '/', str_replace('"', '\'', preg_replace('/[\x00-\x1F\x7F]/', '', preg_replace('/(\015\012)|(\015)|(\012)/', '<br />', $popuptext))))));
			} else {
				$sanitized = preg_replace($sanitize_from, $sanitize_to, stripslashes(str_replace('\\\\', '/', str_replace('"', '\'', preg_replace('/[\x00-\x1F\x7F]/', '', preg_replace('/(\015\012)|(\015)|(\012)/', '', $popuptext))))));
			}
		} else {
			$sanitized = preg_replace($sanitize_from, $sanitize_to, stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/', '<br />', $popuptext)));
		}
		if (isset($lmm_options['wp_kses_status']) && $lmm_options['wp_kses_status'] == 'enabled') {
			$additionaltags = array(
				'iframe' => array(
					'id' => true,
					'name' => true,
					'src' => true,
					'class' => true,
					'style' => true,
					'frameborder' => true,
					'scrolling' => true,
					'align' => true,
					'width' => true,
					'height' => true,
					'marginwidth' => true,
					'marginheight' => true
				),
				'style' => array(
					'media' => true,
					'scoped' => true,
					'type' => true
				)
			);
			$sanitized = wp_kses($sanitized, array_merge($allowedposttags, $additionaltags));
		}
		return $sanitized;
	}

	/**
	 * Sanitizes string of comma-sparated values
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function sanitize_csv($csv, $absint = true, $zero = true, $quotes = false) {
		$csv_sanitized = array();
		$csv = explode(',', $csv);
		foreach ($csv as $value) {
			if ($absint) {
				$value = abs(intval($value));
			} else {
				$value = intval($value);
			}
			if ($zero || $value) {
				if ($quotes === 'single') {
					array_push($csv_sanitized, "'" . $value . "'");
				} else if ($quotes === 'double') {
					array_push($csv_sanitized, '"' . $value . '"');
				} else {
					array_push($csv_sanitized, $value);
				}
			}
		}
		$csv_sanitized = array_unique($csv_sanitized);
		natsort($csv_sanitized);
		$csv_sanitized = implode(',', $csv_sanitized);
		return $csv_sanitized;
	}

	/**
	 * Sanitizes first character of string fields to prevent command injections
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function sanitize_excel($string) {
		$filter = array('=', '+', '-', '@');
		$first_char = substr($string, 0, 1);
		$string = substr($string, 1);
		if (in_array($first_char, $filter)) {
			$first_char = "'" . $first_char . "'";
		}
		return $first_char . $string;
	}

	/**
	 * Removes accents from characters for geocoding
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function accent_folding($address) {
		$accent_map = array('ẚ' => 'a', 'Á' => 'a', 'á' => 'a', 'À' => 'a', 'à' => 'a', 'Ă' => 'a', 'ă' => 'a', 'Ắ' => 'a', 'ắ' => 'a', 'Ằ' => 'a', 'ằ' => 'a', 'Ẵ' => 'a', 'ẵ' => 'a', 'Ẳ' => 'a', 'ẳ' => 'a', 'Â' => 'a', 'â' => 'a', 'Ấ' => 'a', 'ấ' => 'a', 'Ầ' => 'a', 'ầ' => 'a', 'Ẫ' => 'a', 'ẫ' => 'a', 'Ẩ' => 'a', 'ẩ' => 'a', 'Ǎ' => 'a', 'ǎ' => 'a', 'Å' => 'a', 'å' => 'a', 'Ǻ' => 'a', 'ǻ' => 'a', 'Ä' => 'a', 'ä' => 'a', 'Ǟ' => 'a', 'ǟ' => 'a', 'Ã' => 'a', 'ã' => 'a', 'Ȧ' => 'a', 'ȧ' => 'a', 'Ǡ' => 'a', 'ǡ' => 'a', 'Ą' => 'a', 'ą' => 'a', 'Ā' => 'a', 'ā' => 'a', 'Ả' => 'a', 'ả' => 'a', 'Ȁ' => 'a', 'ȁ' => 'a', 'Ȃ' => 'a', 'ȃ' => 'a', 'Ạ' => 'a', 'ạ' => 'a', 'Ặ' => 'a', 'ặ' => 'a', 'Ậ' => 'a', 'ậ' => 'a', 'Ḁ' => 'a', 'ḁ' => 'a', 'Ⱥ' => 'a', 'ⱥ' => 'a', 'Ǽ' => 'a', 'ǽ' => 'a', 'Ǣ' => 'a', 'ǣ' => 'a', 'Ḃ' => 'b', 'ḃ' => 'b', 'Ḅ' => 'b', 'ḅ' => 'b', 'Ḇ' => 'b', 'ḇ' => 'b', 'Ƀ' => 'b', 'ƀ' => 'b', 'ᵬ' => 'b', 'Ɓ' => 'b', 'ɓ' => 'b', 'Ƃ' => 'b', 'ƃ' => 'b', 'Ć' => 'c', 'ć' => 'c', 'Ĉ' => 'c', 'ĉ' => 'c', 'Č' => 'c', 'č' => 'c', 'Ċ' => 'c', 'ċ' => 'c', 'Ç' => 'c', 'ç' => 'c', 'Ḉ' => 'c', 'ḉ' => 'c', 'Ȼ' => 'c', 'ȼ' => 'c', 'Ƈ' => 'c', 'ƈ' => 'c', 'ɕ' => 'c', 'Ď' => 'd', 'ď' => 'd', 'Ḋ' => 'd', 'ḋ' => 'd', 'Ḑ' => 'd', 'ḑ' => 'd', 'Ḍ' => 'd', 'ḍ' => 'd', 'Ḓ' => 'd', 'ḓ' => 'd', 'Ḏ' => 'd', 'ḏ' => 'd', 'Đ' => 'd', 'đ' => 'd', 'ᵭ' => 'd', 'Ɖ' => 'd', 'ɖ' => 'd', 'Ɗ' => 'd', 'ɗ' => 'd', 'Ƌ' => 'd', 'ƌ' => 'd', 'ȡ' => 'd', 'ð' => 'd', 'É' => 'e', 'Ə' => 'e', 'Ǝ' => 'e', 'ǝ' => 'e', 'é' => 'e', 'È' => 'e', 'è' => 'e', 'Ĕ' => 'e', 'ĕ' => 'e', 'Ê' => 'e', 'ê' => 'e', 'Ế' => 'e', 'ế' => 'e', 'Ề' => 'e', 'ề' => 'e', 'Ễ' => 'e', 'ễ' => 'e', 'Ể' => 'e', 'ể' => 'e', 'Ě' => 'e', 'ě' => 'e', 'Ë' => 'e', 'ë' => 'e', 'Ẽ' => 'e', 'ẽ' => 'e', 'Ė' => 'e', 'ė' => 'e', 'Ȩ' => 'e', 'ȩ' => 'e', 'Ḝ' => 'e', 'ḝ' => 'e', 'Ę' => 'e', 'ę' => 'e', 'Ē' => 'e', 'ē' => 'e', 'Ḗ' => 'e', 'ḗ' => 'e', 'Ḕ' => 'e', 'ḕ' => 'e', 'Ẻ' => 'e', 'ẻ' => 'e', 'Ȅ' => 'e', 'ȅ' => 'e', 'Ȇ' => 'e', 'ȇ' => 'e', 'Ẹ' => 'e', 'ẹ' => 'e', 'Ệ' => 'e', 'ệ' => 'e', 'Ḙ' => 'e', 'ḙ' => 'e', 'Ḛ' => 'e', 'ḛ' => 'e', 'Ɇ' => 'e', 'ɇ' => 'e', 'ɚ' => 'e', 'ɝ' => 'e', 'Ḟ' => 'f', 'ḟ' => 'f', 'ᵮ' => 'f', 'Ƒ' => 'f', 'ƒ' => 'f', 'Ǵ' => 'g', 'ǵ' => 'g', 'Ğ' => 'g', 'ğ' => 'g', 'Ĝ' => 'g', 'ĝ' => 'g', 'Ǧ' => 'g', 'ǧ' => 'g', 'Ġ' => 'g', 'ġ' => 'g', 'Ģ' => 'g', 'ģ' => 'g', 'Ḡ' => 'g', 'ḡ' => 'g', 'Ǥ' => 'g', 'ǥ' => 'g', 'Ɠ' => 'g', 'ɠ' => 'g', 'Ĥ' => 'h', 'ĥ' => 'h', 'Ȟ' => 'h', 'ȟ' => 'h', 'Ḧ' => 'h', 'ḧ' => 'h', 'Ḣ' => 'h', 'ḣ' => 'h', 'Ḩ' => 'h', 'ḩ' => 'h', 'Ḥ' => 'h', 'ḥ' => 'h', 'Ḫ' => 'h', 'ḫ' => 'h', 'H' => 'h', '̱' => 'h', 'ẖ' => 'h', 'Ħ' => 'h', 'ħ' => 'h', 'Ⱨ' => 'h', 'ⱨ' => 'h', 'Í' => 'i', 'í' => 'i', 'Ì' => 'i', 'ì' => 'i', 'Ĭ' => 'i', 'ĭ' => 'i', 'Î' => 'i', 'î' => 'i', 'Ǐ' => 'i', 'ǐ' => 'i', 'Ï' => 'i', 'ï' => 'i', 'Ḯ' => 'i', 'ḯ' => 'i', 'Ĩ' => 'i', 'ĩ' => 'i', 'İ' => 'i', 'i' => 'i', 'Į' => 'i', 'į' => 'i', 'Ī' => 'i', 'ī' => 'i', 'Ỉ' => 'i', 'ỉ' => 'i', 'Ȉ' => 'i', 'ȉ' => 'i', 'Ȋ' => 'i', 'ȋ' => 'i', 'Ị' => 'i', 'ị' => 'i', 'Ḭ' => 'i', 'ḭ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ɨ' => 'i', 'ɨ' => 'i', 'Ĵ' => 'j', 'ĵ' => 'j', 'J' => 'j', '̌' => 'j', 'ǰ' => 'j', 'ȷ' => 'j', 'Ɉ' => 'j', 'ɉ' => 'j', 'ʝ' => 'j', 'ɟ' => 'j', 'ʄ' => 'j', 'Ḱ' => 'k', 'ḱ' => 'k', 'Ǩ' => 'k', 'ǩ' => 'k', 'Ķ' => 'k', 'ķ' => 'k', 'Ḳ' => 'k', 'ḳ' => 'k', 'Ḵ' => 'k', 'ḵ' => 'k', 'Ƙ' => 'k', 'ƙ' => 'k', 'Ⱪ' => 'k', 'ⱪ' => 'k', 'Ĺ' => 'a', 'ĺ' => 'l', 'Ľ' => 'l', 'ľ' => 'l', 'Ļ' => 'l', 'ļ' => 'l', 'Ḷ' => 'l', 'ḷ' => 'l', 'Ḹ' => 'l', 'ḹ' => 'l', 'Ḽ' => 'l', 'ḽ' => 'l', 'Ḻ' => 'l', 'ḻ' => 'l', 'Ł' => 'l', 'ł' => 'l', 'Ł' => 'l', '̣' => 'l', 'ł' => 'l', '̣' => 'l', 'Ŀ' => 'l', 'ŀ' => 'l', 'Ƚ' => 'l', 'ƚ' => 'l', 'Ⱡ' => 'l', 'ⱡ' => 'l', 'Ɫ' => 'l', 'ɫ' => 'l', 'ɬ' => 'l', 'ɭ' => 'l', 'ȴ' => 'l', 'Ḿ' => 'm', 'ḿ' => 'm', 'Ṁ' => 'm', 'ṁ' => 'm', 'Ṃ' => 'm', 'ṃ' => 'm', 'ɱ' => 'm', 'Ń' => 'n', 'ń' => 'n', 'Ǹ' => 'n', 'ǹ' => 'n', 'Ň' => 'n', 'ň' => 'n', 'Ñ' => 'n', 'ñ' => 'n', 'Ṅ' => 'n', 'ṅ' => 'n', 'Ņ' => 'n', 'ņ' => 'n', 'Ṇ' => 'n', 'ṇ' => 'n', 'Ṋ' => 'n', 'ṋ' => 'n', 'Ṉ' => 'n', 'ṉ' => 'n', 'Ɲ' => 'n', 'ɲ' => 'n', 'Ƞ' => 'n', 'ƞ' => 'n', 'ɳ' => 'n', 'ȵ' => 'n', 'N' => 'n', '̈' => 'n', 'n' => 'n', '̈' => 'n', 'Ó' => 'o', 'ó' => 'o', 'Ò' => 'o', 'ò' => 'o', 'Ŏ' => 'o', 'ŏ' => 'o', 'Ô' => 'o', 'ô' => 'o', 'Ố' => 'o', 'ố' => 'o', 'Ồ' => 'o', 'ồ' => 'o', 'Ỗ' => 'o', 'ỗ' => 'o', 'Ổ' => 'o', 'ổ' => 'o', 'Ǒ' => 'o', 'ǒ' => 'o', 'Ö' => 'o', 'ö' => 'o', 'Ȫ' => 'o', 'ȫ' => 'o', 'Ő' => 'o', 'ő' => 'o', 'Õ' => 'o', 'õ' => 'o', 'Ṍ' => 'o', 'ṍ' => 'o', 'Ṏ' => 'o', 'ṏ' => 'o', 'Ȭ' => 'o', 'ȭ' => 'o', 'Ȯ' => 'o', 'ȯ' => 'o', 'Ȱ' => 'o', 'ȱ' => 'o', 'Ø' => 'o', 'ø' => 'o', 'Ǿ' => 'o', 'ǿ' => 'o', 'Ǫ' => 'o', 'ǫ' => 'o', 'Ǭ' => 'o', 'ǭ' => 'o', 'Ō' => 'o', 'ō' => 'o', 'Ṓ' => 'o', 'ṓ' => 'o', 'Ṑ' => 'o', 'ṑ' => 'o', 'Ỏ' => 'o', 'ỏ' => 'o', 'Ȍ' => 'o', 'ȍ' => 'o', 'Ȏ' => 'o', 'ȏ' => 'o', 'Ơ' => 'o', 'ơ' => 'o', 'Ớ' => 'o', 'ớ' => 'o', 'Ờ' => 'o', 'ờ' => 'o', 'Ỡ' => 'o', 'ỡ' => 'o', 'Ở' => 'o', 'ở' => 'o', 'Ợ' => 'o', 'ợ' => 'o', 'Ọ' => 'o', 'ọ' => 'o', 'Ộ' => 'o', 'ộ' => 'o', 'Ɵ' => 'o', 'ɵ' => 'o', 'Ṕ' => 'p', 'ṕ' => 'p', 'Ṗ' => 'p', 'ṗ' => 'p', 'Ᵽ' => 'p', 'Ƥ' => 'p', 'ƥ' => 'p', 'P' => 'p', '̃' => 'p', 'p' => 'p', '̃' => 'p', 'ʠ' => 'q', 'Ɋ' => 'q', 'ɋ' => 'q', 'Ŕ' => 'r', 'ŕ' => 'r', 'Ř' => 'r', 'ř' => 'r', 'Ṙ' => 'r', 'ṙ' => 'r', 'Ŗ' => 'r', 'ŗ' => 'r', 'Ȑ' => 'r', 'ȑ' => 'r', 'Ȓ' => 'r', 'ȓ' => 'r', 'Ṛ' => 'r', 'ṛ' => 'r', 'Ṝ' => 'r', 'ṝ' => 'r', 'Ṟ' => 'r', 'ṟ' => 'r', 'Ɍ' => 'r', 'ɍ' => 'r', 'ᵲ' => 'r', 'ɼ' => 'r', 'Ɽ' => 'r', 'ɽ' => 'r', 'ɾ' => 'r', 'ᵳ' => 'r', 'ß' => 's', 'Ś' => 's', 'ś' => 's', 'Ṥ' => 's', 'ṥ' => 's', 'Ŝ' => 's', 'ŝ' => 's', 'Š' => 's', 'š' => 's', 'Ṧ' => 's', 'ṧ' => 's', 'Ṡ' => 's', 'ṡ' => 's', 'ẛ' => 's', 'Ş' => 's', 'ş' => 's', 'Ṣ' => 's', 'ṣ' => 's', 'Ṩ' => 's', 'ṩ' => 's', 'Ș' => 's', 'ș' => 's', 'ʂ' => 's', 'S' => 's', '̩' => 's', 's' => 's', '̩' => 's', 'Þ' => 't', 'þ' => 't', 'Ť' => 't', 'ť' => 't', 'T' => 't', '̈' => 't', 'ẗ' => 't', 'Ṫ' => 't', 'ṫ' => 't', 'Ţ' => 't', 'ţ' => 't', 'Ṭ' => 't', 'ṭ' => 't', 'Ț' => 't', 'ț' => 't', 'Ṱ' => 't', 'ṱ' => 't', 'Ṯ' => 't', 'ṯ' => 't', 'Ŧ' => 't', 'ŧ' => 't', 'Ⱦ' => 't', 'ⱦ' => 't', 'ᵵ' => 't', 'ƫ' => 't', 'Ƭ' => 't', 'ƭ' => 't', 'Ʈ' => 't', 'ʈ' => 't', 'ȶ' => 't', 'Ú' => 'u', 'ú' => 'u', 'Ù' => 'u', 'ù' => 'u', 'Ŭ' => 'u', 'ŭ' => 'u', 'Û' => 'u', 'û' => 'u', 'Ǔ' => 'u', 'ǔ' => 'u', 'Ů' => 'u', 'ů' => 'u', 'Ü' => 'u', 'ü' => 'u', 'Ǘ' => 'u', 'ǘ' => 'u', 'Ǜ' => 'u', 'ǜ' => 'u', 'Ǚ' => 'u', 'ǚ' => 'u', 'Ǖ' => 'u', 'ǖ' => 'u', 'Ű' => 'u', 'ű' => 'u', 'Ũ' => 'u', 'ũ' => 'u', 'Ṹ' => 'u', 'ṹ' => 'u', 'Ų' => 'u', 'ų' => 'u', 'Ū' => 'u', 'ū' => 'u', 'Ṻ' => 'u', 'ṻ' => 'u', 'Ủ' => 'u', 'ủ' => 'u', 'Ȕ' => 'u', 'ȕ' => 'u', 'Ȗ' => 'u', 'ȗ' => 'u', 'Ư' => 'u', 'ư' => 'u', 'Ứ' => 'u', 'ứ' => 'u', 'Ừ' => 'u', 'ừ' => 'u', 'Ữ' => 'u', 'ữ' => 'u', 'Ử' => 'u', 'ử' => 'u', 'Ự' => 'u', 'ự' => 'u', 'Ụ' => 'u', 'ụ' => 'u', 'Ṳ' => 'u', 'ṳ' => 'u', 'Ṷ' => 'u', 'ṷ' => 'u', 'Ṵ' => 'u', 'ṵ' => 'u', 'Ʉ' => 'u', 'ʉ' => 'u', 'Ṽ' => 'v', 'ṽ' => 'v', 'Ṿ' => 'v', 'ṿ' => 'v', 'Ʋ' => 'v', 'ʋ' => 'v', 'Ẃ' => 'w', 'ẃ' => 'w', 'Ẁ' => 'w', 'ẁ' => 'w', 'Ŵ' => 'w', 'ŵ' => 'w', 'W' => 'w', '̊' => 'w', 'ẘ' => 'w', 'Ẅ' => 'w', 'ẅ' => 'w', 'Ẇ' => 'w', 'ẇ' => 'w', 'Ẉ' => 'w', 'ẉ' => 'w', 'Ẍ' => 'x', 'ẍ' => 'x', 'Ẋ' => 'x', 'ẋ' => 'x', 'Ý' => 'y', 'ý' => 'y', 'Ỳ' => 'y', 'ỳ' => 'y', 'Ŷ' => 'y', 'ŷ' => 'y', 'Y' => 'y', '̊' => 'y', 'ẙ' => 'y', 'Ÿ' => 'y', 'ÿ' => 'y', 'Ỹ' => 'y', 'ỹ' => 'y', 'Ẏ' => 'y', 'ẏ' => 'y', 'Ȳ' => 'y', 'ȳ' => 'y', 'Ỷ' => 'y', 'ỷ' => 'y', 'Ỵ' => 'y', 'ỵ' => 'y', 'ʏ' => 'y', 'Ɏ' => 'y', 'ɏ' => 'y', 'Ƴ' => 'y', 'ƴ' => 'y', 'Ź' => 'z', 'ź' => 'z', 'Ẑ' => 'z', 'ẑ' => 'z', 'Ž' => 'z', 'ž' => 'z', 'Ż' => 'z', 'ż' => 'z', 'Ẓ' => 'z', 'ẓ' => 'z', 'Ẕ' => 'z', 'ẕ' => 'z', 'Ƶ' => 'z', 'ƶ' => 'z', 'Ȥ' => 'z', 'ȥ' => 'z', 'ʐ' => 'z', 'ʑ' => 'z', 'Ⱬ' => 'z', 'ⱬ' => 'z', 'Ǯ' => 'z', 'ǯ' => 'z', 'ƺ' => 'z',
		// Roman fullwidth ascii equivalents => 0xff00 to 0xff5e
		'２' => '2', '６' => '6', 'Ｂ' => 'B', 'Ｆ' => 'F', 'Ｊ' => 'J', 'Ｎ' => 'N', 'Ｒ' => 'R', 'Ｖ' => 'V', 'Ｚ' => 'Z', 'ｂ' => 'b', 'ｆ' => 'f', 'ｊ' => 'j', 'ｎ' => 'n', 'ｒ' => 'r', 'ｖ' => 'v', 'ｚ' => 'z', '１' => '1', '５' => '5', '９' => '9', 'Ａ' => 'A', 'Ｅ' => 'E', 'Ｉ' => 'I', 'Ｍ' => 'M', 'Ｑ' => 'Q', 'Ｕ' => 'U', 'Ｙ' => 'Y', 'ａ' => 'a', 'ｅ' => 'e', 'ｉ' => 'i', 'ｍ' => 'm', 'ｑ' => 'q', 'ｕ' => 'u', 'ｙ' => 'y', '０' => '0', '４' => '4', '８' => '8', 'Ｄ' => 'D', 'Ｈ' => 'H', 'Ｌ' => 'L', 'Ｐ' => 'P', 'Ｔ' => 'T', 'Ｘ' => 'X', 'ｄ' => 'd', 'ｈ' => 'h', 'ｌ' => 'l', 'ｐ' => 'p', 'ｔ' => 't', 'ｘ' => 'x', '３' => '3', '７' => '7', 'Ｃ' => 'C', 'Ｇ' => 'G', 'Ｋ' => 'K', 'Ｏ' => 'O', 'Ｓ' => 'S', 'Ｗ' => 'W', 'ｃ' => 'c', 'ｇ' => 'g', 'ｋ' => 'k', 'ｏ' => 'o', 'ｓ' => 's', 'ｗ' => 'w');
		return str_replace(array_keys($accent_map), array_values($accent_map), $address);
	}

	/**
	 * Advanced array sorting
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return array
	 */
	public static function array_sort($array, $on, $order = SORT_ASC) {
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
		return $new_array;
	}

	/**
	 * Check if required plugins for multilingual support are installed
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return mixed
	 */
	public static function check_multilingual() {
		$lmm_options = get_option('leafletmapsmarker_options');
		if (isset($lmm_options['multilingual_integration_status']) && $lmm_options['multilingual_integration_status'] == 'enabled') {
			if (defined("ICL_SITEPRESS_VERSION") && defined('WPML_ST_VERSION')) {
				return 'wpml';
			} elseif (defined('POLYLANG_VERSION')) {
				return 'pll';
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Single string registration logic
	 * When used in loops, call check_multilingual() before the loop and set $checked to its result to reduce execution time
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 */
	public static function register_single_string($string_name, $string, $checked = false) {
		if (!$checked) {
			$checked = self::check_multilingual();
		}
		if ($checked == 'wpml' || $checked == 'pll') {
			do_action('wpml_register_single_string', 'Maps Marker Pro', $string_name, $string);
		}
	}

	/**
	 * Single string translation logic
	 * When used in loops, call check_multilingual() before the loop and set $checked to its result to reduce execution time
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function translate_single_string($string, $string_name, $checked = false) {
		if (!$checked) {
			$checked = self::check_multilingual();
		}
		if ($checked == 'wpml') {
			return apply_filters('wpml_translate_single_string', $string, 'Maps Marker Pro', $string_name);
		} elseif ($checked == 'pll' && function_exists('pll__')) {
			return pll__($string);
		} else {
			return $string;
		}
	}

	/**
	 * Permalink translation logic
	 * When used in loops, call check_multilingual() before the loop and set $checked to its result to reduce execution time
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function translate_permalink($link, $checked = false) {
		global $wp_rewrite;
		if (!$wp_rewrite->using_mod_rewrite_permalinks()) {
			$base_url = MMP_Rewrite::get_base_url();
			$slug = MMP_Rewrite::get_slug();
			$link = str_replace($base_url, '', $link);
			if (preg_match('%^' . $slug . '/(geositemap|download|webapi|import-export|proxy|changelog|upload)/\??(.+)?%', $link)) {
				$link = preg_replace('%^' . $slug . '/(geositemap|download|webapi|import-export|proxy|changelog|upload)/\??(.+)?%', 'index.php?endpoint=$1&$2', $link);
			} elseif (preg_match('%^' . $slug . '/(fullscreen|geojson|kml|georss|wikitude|qr)/(marker|layer)/(.+)/\??(.+)?%', $link)) {
				$link = preg_replace('%^' . $slug . '/(fullscreen|geojson|kml|georss|wikitude|qr)/(marker|layer)/(.+)/\??(.+)?%', 'index.php?endpoint=$1&$2=$3&$4', $link);
			} elseif (preg_match('%^' . $slug . '/google-places/autocomplete/?([0-9]+)?/\??(.+)?%', $link)) {
				$link = preg_replace('%^' . $slug . '/google-places/autocomplete/?([0-9]+)?/\??(.+)?%', 'index.php?endpoint=$1&$2', $link);
			} elseif (preg_match('%^' . $slug . '/google-places/details/?([0-9]+)?/\??(.+)?%', $link)) {
				$link = preg_replace('%^' . $slug . '/google-places/details/?([0-9]+)?/\??(.+)?%', 'index.php?endpoint=$1&$2', $link);
			}
			$link = $base_url . trim($link, '&');
		}
		if (!$checked) {
			$checked = self::check_multilingual();
		}
		if ($checked == 'wpml') {
			return apply_filters('wpml_permalink', $link);
		} elseif ($checked == 'pll' && function_exists('pll_current_language')) {
			if (is_admin() && !pll_current_language() && function_exists('pll_default_language')) {
				return PLL()->links_model->switch_language_in_link($link, PLL()->model->get_language(pll_default_language()));
			} else {
				return PLL()->links_model->switch_language_in_link($link, PLL()->model->get_language(pll_current_language()));
			}
		} else {
			return $link;
		}
	}

	/**
	 * Check if browser supports MutationObserver
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return boolean
	 */
	public static function check_google_mutant_fallback() {
		if (preg_match('/(?i)msie (10|[5-9])/', $_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Convert order string into readable text
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @param integer $order_by SORT_ASC or SORT_DESC
	 *
	 * @return string
	 */
	public static function get_order_text($order_by) {
		switch ($order_by) {
			case 'm.id':
				return __('ID', 'lmm');
				break;
			case 'm.markername':
				return __('marker name', 'lmm');
				break;
			case 'm.popuptext':
				return __('popuptext', 'lmm');
				break;
			case 'm.icon':
				return __('icon', 'lmm');
				break;
			case 'm.createdby':
				return __('created by', 'lmm');
				break;
			case 'm.createdon':
				return __('created on', 'lmm');
				break;
			case 'm.updatedby':
				return __('updated by', 'lmm');
				break;
			case 'm.updatedon':
				return __('updated on', 'lmm');
				break;
			case 'm.layer':
				return __('layer ID', 'lmm');
				break;
			case 'm.address':
				return __('address', 'lmm');
				break;
			case 'm.kml_timestamp':
				return __('KML timestamp', 'lmm');
				break;
			case 'distance_layer_center':
				return __('distance from layer center', 'lmm');
				break;
			case 'distance_current_position':
				return __('distance from current position', 'lmm');
				break;
			default:
				return '';
				break;
		}
	}

	/**
	 * Render markers list pagination
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @param string  $uid         ID of the map
	 * @param integer $markercount Total number of markers
	 * @param boolean $is_mlm      Whether the map is multi-layer or not
	 * @param string  $mlm_list    The sub-layers of the MLM layer
	 * @param integer $order_by    SORT_ASC or SORT_DESC
	 *
	 * @return string
	 */
	public static function get_markers_pagination($uid, $markercount, $is_mlm, $mlm_list, $order_by) {
		$lmm_options = get_option('leafletmapsmarker_options');
		$lmm_base_url = MMP_Rewrite::get_base_url();
		$lmm_slug = MMP_Rewrite::get_slug();
		$order = $lmm_options['defaults_layer_listmarkers_sort_order'] == 'ASC' ? 'ASC' : 'DESC';
		$pager = '<div class="tablenav">';
		if ($markercount > intval($lmm_options['defaults_layer_listmarkers_limit'])) {
			$maxpage = intval(ceil($markercount / intval($lmm_options['defaults_layer_listmarkers_limit'])));
			if ($maxpage > 1) {
				$pager .= '<div id="pagination_' . $uid . '" class="tablenav-pages">';
				$pager .= '<span class="markercount_' . $uid . '">' . $markercount . '</span> ' . __('markers', 'lmm');
				$pager .= '<div class="lmm-per-page">';
				$pager .= '<input type="text" id="markers_per_page_' . $uid . '" class="lmm-per-page-input" value="' . intval($lmm_options["defaults_layer_listmarkers_limit"]) . '" data-mapid="' . $uid . '" />';
				$pager .= ' ' . __('per page', 'lmm');
				$pager .= '</div>';
				$pager .= '<div class="lmm-pages">';
				$pager .= '<form style="display:inline;" method="POST" action="">' . __('page', 'lmm') . ' ';
				$pager .= '<input type="hidden" id="' . $uid . '_orderby" name="orderby" value="' . $order_by . '" />';
				$pager .= '<input type="hidden" id="' . $uid . '_order" name="order" value="' . $order . '" />';
				$pager .= '<input type="hidden" id="' . $uid . '_multi_layer_map" name="multi_layer_map" value="' . $is_mlm . '" />';
				$pager .= '<input type="hidden" id="' . $uid . '_multi_layer_map_list" name="multi_layer_map_list" value="' . $mlm_list. '" />';
				$pager .= '<input type="hidden" id="' . $uid . '_markercount" name="markercount" value="' . $markercount . '" />';
				$radius = 1;
				$pagenum = 1;
				if ($pagenum > (2 + $radius * 2)) {
					foreach (range(1, 1 + $radius) as $num) {
						$pager .= '<a href="#" class="first-page" data-mapid="' . $uid . '">' . $num . '</a>';
					}
					$pager .= '...';
					foreach (range($pagenum - $radius, $pagenum - 1) as $num) {
						$pager .= '<a href="#" class="first-page" data-mapid="' . $uid . '">' . $num . '</a>';
					}
				} else {
					if ($pagenum > 1) {
						foreach (range(1, $pagenum - 1) as $num) {
							$pager .= '<a href="#" class="first-page" data-mapid="' . $uid . '">' . $num . '</a>';
						}
					}
				}
				$pager .= '<a href="#" class="first-page current-page">' . $pagenum . '</a>';
				if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
					foreach (range($pagenum + 1, $pagenum + $radius) as $num) {
						$pager .= '<a href="#" class="first-page" data-mapid="' . $uid . '">' . $num . '</a>';
					}
					$pager .= '...';
					foreach (range($maxpage - $radius, $maxpage) as $num) {
						$pager .= '<a href="#" class="first-page" data-mapid="' . $uid . '">' . $num . '</a>';
					}
				} else {
					if ($pagenum < $maxpage) {
						foreach (range($pagenum + 1, $maxpage) as $num) {
							$pager .= '<a href="#" class="first-page" data-mapid="' . $uid . '">' . $num . '</a>';
						}
					}
				}
				$pager .= '</div></form></div>';
			}
		}
		$pager .= '</div>';
		return $pager;
	}

	/**
	 * Get HTML row of a marker
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @param object $row The marker object
	 *
	 * @return string
	 */
	public static function get_marker_list_row($row) {
		$lmm_options = get_option('leafletmapsmarker_options');
		$markername = MMP_Globals::translate_single_string($row['mmarkername'], "Marker (ID {$row['mid']}) name", true);
		$lmm_base_url = MMP_Rewrite::get_base_url();
		$lmm_slug = MMP_Rewrite::get_slug();
		$lmm_out = '';
		if ($lmm_options['defaults_marker_custom_icon_url_dir'] == 'no') {
			$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
		} else {
			$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
		}
		if (isset($lmm_options['defaults_layer_listmarkers_show_icon']) && $lmm_options['defaults_layer_listmarkers_show_icon'] == 1) {
			$lmm_out .= '<tr id="marker_' . $row['mid'] . '"><td class="lmm-listmarkers-icon">';
			if ($lmm_options['defaults_layer_listmarkers_link_action'] != 'disabled') {
				$listmarkers_href_a = '<a href="javascript:void(0);" onclick="javascript:listmarkers_openpopup_' . '{mapid}' . '(' . $row['mid'] . ')">';
				$listmarkers_href_b = '</a>';
			} else {
				$listmarkers_href_a = '';
				$listmarkers_href_b = '';
			}
			if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
				$markername_on_hover = 'title="' . stripslashes(htmlspecialchars(preg_replace('/[\x00-\x1F\x7F]/', '', $markername))) . '"';
			} else {
				$markername_on_hover = '';
			}
			if ($row['micon'] != null) {
				$lmm_out .= $listmarkers_href_a . '<img style="border-radius:0;box-shadow:none;" width="' . intval($lmm_options['defaults_marker_icon_iconsize_x']) . '" height="' . intval($lmm_options['defaults_marker_icon_iconsize_y']) . '" alt="marker icon" src="' . $defaults_marker_icon_url . '/' . $row['micon'] . '" ' . $markername_on_hover . ' />' . $listmarkers_href_b;
			} else {
				$lmm_out .= $listmarkers_href_a . '<img style="border-radius:0;box-shadow:none;" alt="marker icon" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" ' . $markername_on_hover . ' />' . $listmarkers_href_b;
			}
		} else {
			$lmm_out .= '<tr><td>';
		}
		$lmm_out .= '</td><td class="lmm-listmarkers-popuptext"><div class="lmm-listmarkers-panel-icons">';
		if (isset($lmm_options['defaults_layer_listmarkers_api_directions' ]) && $lmm_options['defaults_layer_listmarkers_api_directions'] == 1) {
			if ($lmm_options['directions_provider'] == 'googlemaps') {
				if (isset($lmm_options['google_maps_base_domain_custom']) && $lmm_options['google_maps_base_domain_custom'] == null) {
					$gmaps_base_domain_directions = $lmm_options['google_maps_base_domain'];
				} else {
					$gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']);
				}
				if (isset($lmm_options['directions_googlemaps_route_type_walking']) && $lmm_options['directions_googlemaps_route_type_walking'] == 1) {
					$directions_transport_type_icon = 'icon-walk.png';
				} else {
					$directions_transport_type_icon = 'icon-car.png';
				}
				if ($row['maddress'] != null) {
					$google_from = urlencode($row['maddress']);
				} else {
					$google_from = $row['mlat'] . ',' . $row['mlon'];
				}
				$avoidhighways = isset($lmm_options['directions_googlemaps_route_type_highways']) && $lmm_options['directions_googlemaps_route_type_highways'] == 1 ? '&dirflg=h' : '';
				$avoidtolls = isset($lmm_options['directions_googlemaps_route_type_tolls']) && $lmm_options['directions_googlemaps_route_type_tolls'] == 1 ? '&dirflg=t' : '';
				$publictransport = isset($lmm_options['directions_googlemaps_route_type_public_transport']) && $lmm_options['directions_googlemaps_route_type_public_transport'] == 1 ? '&dirflg=r' : '';
				$walking = isset($lmm_options['directions_googlemaps_route_type_walking']) && $lmm_options['directions_googlemaps_route_type_walking'] == 1 ? '&dirflg=w' : '';
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ($locale != null) {
						$google_language = '&hl=' . substr($locale, 0, 2);
					} else {
						$google_language = '&hl=en';
					}
				} else {
					$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
				}
				$lmm_out .= '<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&amp;t=' . $lmm_options['directions_googlemaps_map_type'] . '&amp;layer=' . $lmm_options['directions_googlemaps_traffic'] . '&amp;doflg=' . $lmm_options['directions_googlemaps_distance_units'] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&amp;om=' . $lmm_options['directions_googlemaps_overview_map'] . '" target="_blank" title="' . esc_attr__('Get directions', 'lmm') . '"><img alt="' . esc_attr__('Get directions', 'lmm') . '" src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'yours') {
				if ($lmm_options['directions_yours_type_of_transport'] == 'motorcar') {
					$directions_transport_type_icon = 'icon-car.png';
				} else if ($lmm_options['directions_yours_type_of_transport'] == 'bicycle') {
					$directions_transport_type_icon = 'icon-bicycle.png';
				} else if ($lmm_options['directions_yours_type_of_transport'] == 'foot') {
					$directions_transport_type_icon = 'icon-walk.png';
				}
				$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $row['mlat'] . '&amp;tlon=' . $row['mlon'] . '&amp;v=' . $lmm_options['directions_yours_type_of_transport'] . '&amp;fast=' . $lmm_options['directions_yours_route_type'] . '&amp;layer=' . $lmm_options['directions_yours_layer'] . '" target="_blank" title="' . esc_attr__('Get directions', 'lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions', 'lmm') . '" /></a>';
			} else if ($lmm_options['directions_provider'] == 'ors') {
				if ($lmm_options['directions_ors_routeOpt'] == 'Pedestrian') {
					$directions_transport_type_icon = 'icon-walk.png';
				} else if ($lmm_options['directions_ors_routeOpt'] == 'Bicycle') {
					$directions_transport_type_icon = 'icon-bicycle.png';
				} else {
					$directions_transport_type_icon = 'icon-car.png';
				}
				$lmm_out .= '<a href="http://openrouteservice.org/?pos=' . $row['mlon'] . ',' . $row['mlat'] . '&amp;wp=' . $row['mlon'] . ',' . $row['mlat'] . '&amp;zoom=' . $row['mzoom'] . '&amp;routeOpt=' . $lmm_options['directions_ors_routeOpt'] . '&amp;layer=' . $lmm_options['directions_ors_layer'] . '" target="_blank" title="' . esc_attr__('Get directions', 'lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions', 'lmm') . '" /></a>';
			} else if ($lmm_options['directions_provider'] == 'bingmaps') {
				if ($row['maddress'] != null) {
					$bing_to = '_' . urlencode($row['maddress']);
				} else {
					$bing_to = '';
				}
				$lmm_out .= '<a href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $row['mlat'] . '_' . $row['mlon'] . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions', 'lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions', 'lmm') . '" /></a>';
			}
		}
		if (isset($lmm_options['defaults_layer_listmarkers_api_fullscreen']) && $lmm_options['defaults_layer_listmarkers_api_fullscreen'] == 1) {
			$lmm_out .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['mid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode', 'lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode', 'lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if (isset($lmm_options['defaults_layer_listmarkers_api_kml']) && $lmm_options['defaults_layer_listmarkers_api_kml'] == 1) {
			$lmm_out .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['mid'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps', 'lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps', 'lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if (isset($lmm_options['defaults_layer_listmarkers_api_qr_code']) && $lmm_options['defaults_layer_listmarkers_api_qr_code'] == 1) {
			$lmm_out .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['mid'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode', 'lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode', 'lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if (isset($lmm_options['defaults_layer_listmarkers_api_geojson']) && $lmm_options['defaults_layer_listmarkers_api_geojson'] == 1) {
			$lmm_out .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['mid'] . '/?callback=jsonp&amp;full=yes&amp;full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON', 'lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON', 'lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if (isset($lmm_options['defaults_layer_listmarkers_api_georss']) && $lmm_options['defaults_layer_listmarkers_api_georss'] == 1) {
			$lmm_out .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['mid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS', 'lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS', 'lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if (isset($lmm_options['defaults_layer_listmarkers_api_wikitude']) && $lmm_options['defaults_layer_listmarkers_api_wikitude'] == 1) {
			$lmm_out .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['mid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser', 'lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser', 'lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</div>';
		if (isset($lmm_options['defaults_layer_listmarkers_show_markername']) && $lmm_options['defaults_layer_listmarkers_show_markername'] == 1) {
			if ($lmm_options['defaults_layer_listmarkers_link_action'] != 'disabled') {
				$lmm_out .= '<span class="lmm-listmarkers-markername"><a title="' . esc_attr__('show marker on map', 'lmm') . '" href="javascript:void(0);" onclick="javascript:listmarkers_openpopup_' . '{mapname}' . '(' . $row['mid'] . ')">' . wp_specialchars_decode(stripslashes(esc_js(preg_replace('/[\x00-\x1F\x7F]/', '', $markername)))) . '</a></span>';
			} else {
				$lmm_out .= '<span class="lmm-listmarkers-markername">' . wp_specialchars_decode(stripslashes(esc_js(preg_replace('/[\x00-\x1F\x7F]/', '', $markername)))) . '</span>';
			}
		}
		if (isset($lmm_options['defaults_layer_listmarkers_show_popuptext']) && $lmm_options['defaults_layer_listmarkers_show_popuptext'] == 1) {
			$mpopuptext_prepare = MMP_Globals::sanitize_popuptext($row['mpopuptext'], true, true);
			$popuptext_sanitized = MMP_Globals::sanitize_popuptext(do_shortcode($mpopuptext_prepare), true);
			$lmm_out .= '<br/><span class="lmm-listmarkers-popuptext-only">' . do_shortcode($popuptext_sanitized) . '</span>';
		}
		if (isset($lmm_options['defaults_layer_listmarkers_show_address']) && $lmm_options['defaults_layer_listmarkers_show_address'] == 1) {
			if ($row['mpopuptext'] == null) {
				$lmm_out .= stripslashes(htmlspecialchars($row['maddress']));
			} else if ($row['mpopuptext'] != null && $row['maddress'] != null) {
				$lmm_out .= '<div class="lmm-listmarkers-hr">' . stripslashes(htmlspecialchars($row['maddress'])) . '</div>';
			}
		}
		$lmm_out .= '</td></tr>';
		return $lmm_out;
	}

	/**
	 * Get where the map is being used
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_map_shortcodes($id, $type) {
		global $wpdb;
		$lmm_options = get_option('leafletmapsmarker_options');
		$shortcode = '[' . $lmm_options['shortcode'] . ' ' . $type . '="' . $id . '"';
		$builtin_post_types = array('post', 'page');
		$post_types = get_post_types(array('public' => true, '_builtin' => false));
		$post_types = implode(',', array_map(array('MMP_Globals', 'prepare_post_types'), array_merge($builtin_post_types, $post_types)));
		$posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM $wpdb->posts WHERE post_type IN($post_types) AND post_content LIKE %s ", '%' . $shortcode . '%'));
		$result = '<ul style="margin:0;">';
		foreach ($posts as $post) {
			if ($post->post_title != null) {
				$post_title = $post->post_title;
			} else {
				$post_title = 'ID ' . $post->ID;
			}
			$result .= '<li style="margin-bottom:0;clear:both;">' . ucfirst(get_post_type($post->ID)) . ': <a href="' . get_permalink($post->ID) . '" title="' . esc_attr__('view content', 'lmm') . '" target="_blank">' . esc_html($post_title) . '</a>';
			if (current_user_can('edit_others_posts')) {
				$result .= '<a style="float:right;" href="' . get_edit_post_link($post->ID) . '"> (' . __('edit', 'lmm') . ')</a>';
			}
			$result .= '</li>';
		}
		$widgets = get_option('widget_text');
		if (!empty($widgets)) {
			foreach ($widgets as $w_key => $widget) {
				$shortcode = '[' . $lmm_options['shortcode'] . ' ' . $type . '="' . $id . '"]';
				if (is_array($widget)) {
					if (isset($widget['text']) && $widget['text'] != '') {
						if (strpos($shortcode, $widget['text']) !== false) {
							$result .= '<li style="margin-bottom:0;">';
							$result .= sprintf(__('Found in a <a href="%1$s">widget</a>'), admin_url('widgets.php')) . '</a>';
							$result .= '</li>';
						}
					}
				}
			}
		}
		$result .= '</ul>';
		if ($result == '<ul style="margin:0;"></ul>') {
			$result = __('not used in any content', 'lmm');
		}
		return $result;
	}

	/**
	 * Needed for get_map_shortcodes(), as inline anonymous functions with array_map are only supported since PHP 5.3
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function prepare_post_types($post_type) {
		return sprintf("'%s'", esc_sql($post_type));
	}

	/**
	 * For datetime check in Firefox
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 */
	public static function validate_date($date) {
		return (bool)strtotime($date);
	}

	/**
	 * Check if user has required capabilities
	 *
	 * @since  3.1
	 * @access public
	 * @static
	 *
	 * @return boolean
	 */
	public static function check_capability($capability, $createdby) {
		global $current_user;
		$lmm_options = get_option('leafletmapsmarker_options');
		switch ($capability) {
			case 'add':
				if (current_user_can($lmm_options['capabilities_edit_others']) || current_user_can($lmm_options['capabilities_edit'])) {
					return true;
				} else {
					return false;
				}
				break;
			case 'edit':
				if (current_user_can($lmm_options['capabilities_edit_others']) || current_user_can($lmm_options['capabilities_edit']) && $current_user->user_login == $createdby) {
					return true;
				} else {
					return false;
				}
				break;
			case 'delete':
				if (current_user_can($lmm_options['capabilities_delete_others']) || current_user_can($lmm_options['capabilities_delete']) && $current_user->user_login == $createdby) {
					return true;
				} else {
					return false;
				}
				break;
			case 'view_others':
				if (current_user_can($lmm_options['capabilities_view_others']) || $current_user->user_login == $createdby) {
					return true;
				} else {
					return false;
				}
				break;
			default:
				return false;
				break;
		}
	}
}
