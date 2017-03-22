<?php
/**
 * helper function for Localizations::translate()
 *
 * @param string $key
 * @param array $vars
 *
 * @return string
 */
function trans( $key, $vars = null ) {
	return Language::translate( $key, $vars );
}

/**
 * debugging variables
 * @return string
 */
function dd() {
	$args = func_get_args();
	if ( count( $args ) ) {
		if ( $args ) {
			foreach ( $args as $key => $value ) {
				var_dump( $value );
			}
		}
	}
	exit;
}

/**
 * redirects to given url or url segment
 *
 * @param bool|string $url
 * @param int $status
 * @param array $headers
 */
function redirect( $url = false, $status = 200, $headers = [ ] ) {
	URL::redirect( $url, $status, $headers );
}

/**
 * creates array with specific user data from social network object/array
 *
 * @param array|bool|object $socData (user data from social network object)
 * @param string $type
 * @param bool|object $socNetObject
 * @param string $accessToken
 *
 * @return array
 */
function userData( $socData = false, $type = '', $socNetObject = false, $accessToken = null ) {
	$data = array(
		'soc_id'       => (int) Session::get( 'soc_user.soc_id' ),
		'name'         => Session::get( 'soc_user.name' ),
		'surname'      => Session::get( 'soc_user.surname' ),
		'name_surname' => Session::get( 'soc_user.name_surname' ),
		'nick'         => Session::get( 'soc_user.nick' ),
		'city'         => Session::get( 'soc_user.city' ),
		'age'          => (int) Session::get( 'soc_user.age', 0 ),
		'img_url'      => Session::get( 'soc_user.img_url' ),
		'adult'        => (int) Session::get( 'soc_user.adult', 0 ),
		'ip'           => $_SERVER['REMOTE_ADDR'],
		'agent'        => $_SERVER['HTTP_USER_AGENT'],
		'pubstamp'     => time(),
		'user_key'     => Session::get( 'soc_user.user_key' ),
		'network_type' => Session::get( 'soc_user.network_type', $type ),
		'email'        => Session::get( 'soc_user.email' ),
		'gender'       => Session::get( 'soc_user.gender' ),
		'logged'       => Session::get( 'soc_user.logged', false ),
	);
	switch ( $type ) {
		case 'draugiem':
			$data['soc_id']       = arrayGet( $socData, 'uid' );
			$data['name']         = arrayGet( $socData, 'name' );
			$data['surname']      = arrayGet( $socData, 'surname' );
			$data['name_surname'] = arrayGet( $socData, 'name' ) . ' ' . arrayGet( $socData, 'surname' );
			$data['nick']         = arrayGet( $socData, 'nick' );
			$data['city']         = arrayGet( $socData, 'place' );
			$data['age']          = (int) arrayGet( $socData, 'age' );
			$data['img_url']      = arrayGet( $socData, 'img' );
			$data['email']        = arrayGet( $socData, 'emailHash' );
			$data['gender']       = arrayGet( $socData, 'sex' );
			$data['adult']        = (int) arrayGet( $socData, 'adult' );
			$data['user_key']     = $socNetObject->getUserKey();
			$data['logged']       = true;
			$data['network_type'] = $type;
			break;
		case 'facebook':
			/**
			 * @var GraphUser $socNetObject
			 */
			$data['soc_id']       = $socNetObject->getField( 'id' );
			$data['name']         = $socNetObject->getField( 'first_name' );
			$data['surname']      = $socNetObject->getField( 'last_name' );
			$data['name_surname'] = $data['name'] . ' ' . $data['surname'];
			$data['nick']         = $socNetObject->getField( 'name' );
			$data['email']        = $socNetObject->getField( 'email' );
			$data['img_url']      = $socNetObject->getField( 'picture' );
			$data['gender']       = $socNetObject->getField( 'gender' );
			$data['logged']       = true;
			$data['network_type'] = $type;
			$data['access_token'] = $accessToken;
			break;
		case 'twitter':
			$data['soc_id']       = objectGet( $socData, 'id', 0 );
			$data['name']         = objectGet( $socData, 'name', '' );
			$data['surname']      = objectGet( $socData, 'name', '' );
			$data['name_surname'] = objectGet( $socData, 'name', '' );
			$data['img_url']      = objectGet( $socData, 'profile_image_url_https', '' );
			$data['nick']         = objectGet( $socData, 'screen_name', '' );
			$data['city']         = objectGet( $socData, 'location', '' );
			$data['network_type'] = 'twitter';
			$data['logged']       = true;
			$data['network_type'] = $type;
			break;
	}

	return $data;
}


/**
 * include files by environment (basically - config files)
 *
 * @param string $env
 * @param string $file
 */
function includeByEnv( $env = '', $file ) {
	$env        = $env . '/';
	$configPath = APP_PATH . 'config/';
	if ( file_exists( $configPath . $env . $file ) ) {
		include_once( $configPath . $env . $file );
	} else {
		include_once( $configPath . $file );
	}
}

/**
 * include files by environment (basically - config files)
 *
 * @param string $env
 * @param string $file
 *
 * @return mixed
 */
function requireByEnv( $env = '', $file ) {
	$env        = $env . '/';
	$configPath = APP_PATH . 'config/';
	if ( file_exists( $configPath . $env . $file ) ) {
		return require( $configPath . $env . $file );
	} else {
		return require( $configPath . $file );
	}
}


/**
 * finds specific key in object, if exists
 *
 * @param object $object
 * @param string $key
 * @param mixed $alter
 *
 * @return mixed
 */
function objectGet( $object, $key, $alter = null ) {
	if ( $object ) {
		if ( isset( $object->$key ) ) {
			return $object->$key;
		}
		if ( is_a( $object, 'Models' ) ) {
			return objectGet( $object->items(), $key, $alter );
		}
	}

	return $alter;
}

/**
 * finds specific key in array, if exists
 *
 * @param array|object $array
 * @param string $key
 * @param mixed $default
 *
 * @return mixed
 */
function arrayGet( $array, $key, $default = null ) {
	$key = explode( '.', $key );
	if ( $key ) {
		$result = $array;
		foreach ( $key as $subK ) {
			if ( is_array( $result ) && isset( $result[ $subK ] ) ) {
				$result = $result[ $subK ];
			} elseif ( is_object( $result ) ) {
				$result = objectGet( $result, $subK, $default );
			} else {
				return $default;
			}
		}

		return $result;
	}

	return $default;
}


/**
 * @param array $array
 * @param string $key
 * @param mixed $value
 *
 * @return array
 */
function arraySet( &$array, $key, $value ) {
	if ( is_null( $key ) ) {
		return $array = $value;
	}
	$keys = explode( '.', $key );
	while ( count( $keys ) > 1 ) {
		$key = array_shift( $keys );
		if ( ! isset( $array[ $key ] ) || ! is_array( $array[ $key ] ) ) {
			$array[ $key ] = array();
		}
		$array =& $array[ $key ];
	}
	$array[ array_shift( $keys ) ] = $value;

	return $array;
}

/**
 * @param array $array
 * @param string|array $keys
 */
function arrayForget( &$array, $keys ) {
	$original =& $array;

	foreach ( (array) $keys as $key ) {
		$parts = explode( '.', $key );

		while ( count( $parts ) > 1 ) {
			$part = array_shift( $parts );

			if ( isset( $array[ $part ] ) && is_array( $array[ $part ] ) ) {
				$array =& $array[ $part ];
			}
		}

		unset( $array[ array_shift( $parts ) ] );

		// clean up after each pass
		$array =& $original;
	}
}

/**
 * @param $str
 * @param bool $use_dot
 *
 * @return mixed|string
 */
function slug( $str, $use_dot = true ) {
    return Str::slug($str, $use_dot);
}


function isCLI() {
	return ( php_sapi_name() === 'cli' );
}

function validEmail( $email ) {
	return filter_var( $email, FILTER_VALIDATE_EMAIL );
}

function YTVideoIdFromLink( $link ) {
	if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match ) ) {
		$video_id = $match[1];

		return $video_id;
	}

	return null;
}

/**
 * @param array $array
 *
 * @param string $delimiter
 *
 * @return null|string
 */
function array2csv( array &$array, $delimiter = "," ) {
	if ( count( $array ) == 0 ) {
		return null;
	}
	ob_start();
	$df = fopen( "php://output", 'w' );
	fputcsv( $df, array_keys( reset( $array ) ), $delimiter );
	foreach ( $array as $row ) {
		fputcsv( $df, $row );
	}
	fclose( $df );

	return ob_get_clean();
}

/**
 * @param string $file
 * @param array $array
 * @param string $delimiter
 *
 * @return void
 */

function array2csvFile( $file, array &$array, $delimiter = "," ) {
	if ( count( $array ) == 0 ) {
		return null;
	}
	$df = fopen( $file, 'w+' );
	fputcsv( $df, array_keys( reset( $array ) ), $delimiter );
	foreach ( $array as $row ) {
		fputcsv( $df, $row );
	}
	fclose( $df );
}

/**
 * @param string $filename
 */
function sendDwonloadHeaders( $filename ) {
	header( "Pragma: public" );
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
	header( "Content-Type: application/force-download" );
	header( "Content-Type: application/octet-stream" );
	header( "Content-Type: application/download" );
	header('Content-type: text/csv');
	header( "Content-Disposition: attachment;filename={$filename}" );
	header( "Content-Transfer-Encoding: binary" );
}

/**
 * @return bool
 */
function authorized() {
	return Session::get( 'soc_user', false );
}

if(!function_exists('env')) {
	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	function env($key, $default = null) {
		$value = getenv($key);
		return $value !== false ? $value : $default;
	}
}

/**
 * @return string
 */
function subDirPrefix()
{
	$rewriteBase = Router::rawRewriteBase();
	if(!empty($rewriteBase)) {
		return '/'.$rewriteBase[0];
	}
	return '';
}

/**
 * @param string $key
 * @param string $default
 *
 * @return string
 */
function old($key, $default = '') {
    return Input::get($key, $default, 'POST');
}

/**
 * @return array
 */
function countryList() {
    return array(
        "AF" => "Afghanistan",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil",
        "BQ" => "British Antarctic Territory",
        "IO" => "British Indian Ocean Territory",
        "VG" => "British Virgin Islands",
        "BN" => "Brunei",
        "BG" => "Bulgaria",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CT" => "Canton and Enderbury Islands",
        "CV" => "Cape Verde",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "Christmas Island",
        "CC" => "Cocos [Keeling] Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo - Brazzaville",
        "CD" => "Congo - Kinshasa",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "HR" => "Croatia",
        "CU" => "Cuba",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "CI" => "Côte d’Ivoire",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "NQ" => "Dronning Maud Land",
        "DD" => "East Germany",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands",
        "FO" => "Faroe Islands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "French Guiana",
        "PF" => "French Polynesia",
        "TF" => "French Southern Territories",
        "FQ" => "French Southern and Antarctic Territories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GG" => "Guernsey",
        "GN" => "Guinea",
        "GW" => "Guinea-Bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard Island and McDonald Islands",
        "HN" => "Honduras",
        "HK" => "Hong Kong SAR China",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IM" => "Isle of Man",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JE" => "Jersey",
        "JT" => "Johnston Island",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "Laos",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macau SAR China",
        "MK" => "Macedonia",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "FX" => "Metropolitan France",
        "MX" => "Mexico",
        "FM" => "Micronesia",
        "MI" => "Midway Islands",
        "MD" => "Moldova",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "ME" => "Montenegro",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar [Burma]",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "Netherlands Antilles",
        "NT" => "Neutral Zone",
        "NC" => "New Caledonia",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "KP" => "North Korea",
        "VD" => "North Vietnam",
        "MP" => "Northern Mariana Islands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PC" => "Pacific Islands Trust Territory",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PS" => "Palestinian Territories",
        "PA" => "Panama",
        "PZ" => "Panama Canal Zone",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "YD" => "People's Democratic Republic of Yemen",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn Islands",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar",
        "RO" => "Romania",
        "RU" => "Russia",
        "RW" => "Rwanda",
        "RE" => "Réunion",
        "BL" => "Saint Barthélemy",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "MF" => "Saint Martin",
        "PM" => "Saint Pierre and Miquelon",
        "VC" => "Saint Vincent and the Grenadines",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "SA" => "Saudi Arabia",
        "SN" => "Senegal",
        "RS" => "Serbia",
        "CS" => "Serbia and Montenegro",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "SB" => "Solomon Islands",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "GS" => "South Georgia and the South Sandwich Islands",
        "KR" => "South Korea",
        "ES" => "Spain",
        "LK" => "Sri Lanka",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "Syria",
        "ST" => "São Tomé and Príncipe",
        "TW" => "Taiwan",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania",
        "TH" => "Thailand",
        "TL" => "Timor-Leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UM" => "U.S. Minor Outlying Islands",
        "PU" => "U.S. Miscellaneous Pacific Islands",
        "VI" => "U.S. Virgin Islands",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "SU" => "Union of Soviet Socialist Republics",
        "AE" => "United Arab Emirates",
        "GB" => "United Kingdom",
        "US" => "United States",
        "ZZ" => "Unknown or Invalid Region",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VA" => "Vatican City",
        "VE" => "Venezuela",
        "VN" => "Vietnam",
        "WK" => "Wake Island",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe",
        "AX" => "Åland Islands",
    );
}

function getCountry($code, $default = 'unknown') {
    $code = strtoupper($code);
    return arrayGet(countryList(), $code, $default);
}

/**
 * @return array
 */
function industryList() {
    return [
        'Agriculture, forestry and fishing' => 'Agriculture, forestry and fishing',
        'Architecture and design' => 'Architecture and design',
        'Construction' => 'Construction',
        'Consulting' => 'Consulting',
        'Culture and arts' => 'Culture and arts',
        'Education and science' => 'Education and science',
        'Energetics' => 'Energetics',
        'Finance and insurance' => 'Finance and insurance',
        'Food and catering' => 'Food and catering',
        'Health and social care' => 'Health and social care',
        'IT and communications' => 'IT and communications',
        'Law and regulations' => 'Law and regulations',
        'Manufacturing, production industry' => 'Manufacturing, production industry',
        'Media and information services' => 'Media and information services',
        'Medicine, pharmaceuticals and healthcare' => 'Medicine, pharmaceuticals and healthcare',
        'Public sector, NGOs' => 'Public sector, NGOs',
        'Real estate' => 'Real estate',
        'Public sector, government' => 'Public sector, government',
        'Tourism, accommodation and food service' => 'Tourism, accommodation and food service',
        'Transportation, logistics and storage' => 'Transportation, logistics and storage',
        'Wholesale and retail trade' => 'Wholesale and retail trade',
        'Other' => 'Other',
    ];
}

/**
 * New lines to paragraphs (helper function for Str::nl2p())
 *
 * @param string $string
 * @param bool $lineBreaks - if true, two line breaks becomes p and one becomes br.
 * @param bool $xml - if XHTML, tan need to set to true
 *
 * @return string
 */
function nl2p($string, $lineBreaks = true, $xml = false) {
    return Str::nl2p($string, $lineBreaks, $xml);
}