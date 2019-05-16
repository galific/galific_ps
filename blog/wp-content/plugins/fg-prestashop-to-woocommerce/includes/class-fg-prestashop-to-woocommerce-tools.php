<?php

/**
 * Useful functions
 *
 * @link       https://www.fredericgilles.net/fg-prestashop-to-woocommerce/
 * @since      2.0.0
 *
 * @package    FG_PrestaShop_to_WooCommerce_Premium
 * @subpackage FG_PrestaShop_to_WooCommerce_Premium/includes
 */

/**
 * Useful functions class
 *
 * @since      2.0.0
 * @package    FG_PrestaShop_to_WooCommerce_Premium
 * @subpackage FG_PrestaShop_to_WooCommerce_Premium/includes
 * @author     Frédéric GILLES
 */

if ( !class_exists('FG_PrestaShop_to_WooCommerce_Tools', false) ) {
	class FG_PrestaShop_to_WooCommerce_Tools {
		
		/**
		 * Convert string to latin
		 * 
		 * @param string $string String
		 * @return string Translated string
		 */
		public static function convert_to_latin($string) {
			$string = self::greek_to_latin($string); // For Greek characters
			$string = self::cyrillic_to_latin($string); // For Cyrillic characters
			$string = self::arabic_to_latin($string); // For Arabic characters
			$string = self::bengali_to_latin($string); // For Bengali characters
			$string = remove_accents($string); // For accented characters
			return $string;
		}
		
		/**
		 * Convert Greek characters to latin
		 * 
		 * @param string $string String
		 * @return string Translated string
		 */
		private static function greek_to_latin($string) {
			static $from = array('Α','Ά','Β','Γ','Δ','Ε','Έ','Ζ','Η','Θ','Ι','Κ','Λ','Μ','Ν','Ξ','Ο','Π','Ρ','Σ','Τ','Υ','Φ','Χ','Ψ','Ω','α','ά','β','γ','δ','ε','έ','ζ','η','ή','θ','ι','ί','ϊ','κ','λ','μ','ν','ξ','ο','ό','π','ρ','ς','σ','τ','υ','ύ','φ','χ','ψ','ω','ώ','ϑ','ϒ','ϖ');
			static $to = array('A','A','V','G','D','E','E','Z','I','TH','I','K','L','M','N','X','O','P','R','S','T','Y','F','CH','PS','O','a','a','v','g','d','e','e','z','i','i','th','i','i','i','k','l','m','n','x','o','o','p','r','s','s','t','y','y','f','ch','ps','o','o','th','y','p');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Cyrillic (Russian) characters to latin
		 * 
		 * @param string $string String
		 * @return string Translated string
		 */
		private static function cyrillic_to_latin($string) {
			static $from = array('ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', 'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
			static $to = array('zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q', 'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q');
			return str_replace($from, $to, $string);
		}
		
		/**
		 * Convert Arabic characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Arabic characters converted to latin
		 */
		private static function arabic_to_latin($string) {
			static $from = array('أ', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي');
			static $to = array('a', 'b', 't', 'th', 'g', 'h', 'kh', 'd', 'th', 'r', 'z', 's', 'sh', 's', 'd', 't', 'th', 'aa', 'gh', 'f', 'k', 'k', 'l', 'm', 'n', 'h', 'o', 'y');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Bengali characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Bengali characters converted to latin
		 */
		private static function bengali_to_latin($string) {
			static $from = array('অ', 'আ', 'ই', 'ঈ', 'উ', 'ঊ', 'ঋ', 'ৠ', 'ঌ', 'এ', 'ঐ', 'ও', 'ঔ',
				'ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ড়', 'ঢ', 'ঢ়', 'ণ', 'ত', 'ৎ', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব','ভ', 'ম', 'য', 'য়', 'র', 'ল', 'ব', 'শ', 'ষ', 'স', 'হ', 'ং', 'ঃ', 'ঁ', 'ऽ',
				'০', '১', '২', '৩', '৪', '৫', '৬', '	৭', '৮', '৯');
			static $to = array('a', 'ā', 'I', 'ī', 'u', 'ū', 'ri', 'rri', 'li', 'e', 'ai', 'o', 'au',
				'ka', 'kha', 'ga', 'gha', 'ṅa', 'ca', 'cha', 'ja', 'jha', 'ña', 'ṭa', 'ṭha', 'ḍa', 'ṛa', 'ḍha', 'ṛha', 'ṇa', 'ta', 't', 'tha', 'da', 'dha', 'na', 'pa', 'pha', 'ba', 'bha', 'ma', 'ya', 'ẏa', 'ra', 'la', 'ba', 'śa', 'sha', 'sa', 'ha', 'ṃ', 'ḥ', 'n', "'",
				'0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
			return str_replace($from, $to, $string);
		}

	}
}
