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

	$str = preg_replace( '|\&[^;]+;|U', '', $str );
	$str = strip_tags( $str );
	$str = mb_strtolower( $str, 'UTF-8' );

	$str = trim( $str );

	// garumziimju aizvaakshana

	$search = array(
		'Ā',
		'Č',
		'Ē',
		'Ģ',
		'Ī',
		'Ķ',
		'Ļ',
		'Ņ',
		'Ō',
		'Ŗ',
		'Š',
		'Ū',
		'Ž',
		'ā',
		'č',
		'ē',
		'ģ',
		'ī',
		'ķ',
		'ļ',
		'ņ',
		'ō',
		'ŗ',
		'š',
		'ū',
		'ž',
		' ',
		','
	);
	if ( $use_dot ) {
		$search = array_merge( $search, array( '.' ) );
	} else {
		$search = array_merge( $search, array( '-' ) );
	}

	$replace = array(
		'a',
		'c',
		'e',
		'g',
		'i',
		'k',
		'l',
		'n',
		'o',
		'r',
		's',
		'u',
		'z',
		'a',
		'c',
		'e',
		'g',
		'i',
		'k',
		'l',
		'n',
		'o',
		'r',
		's',
		'u',
		'z'
	);
	$replace = array_merge( $replace, array( '_', '_', '_' ) );
	$str     = str_replace( $search, $replace, $str );
	// kirilicas paarveidoshana

	$kirilica = array(
		'а',
		'б',
		'в',
		'г',
		'д',
		'е',
		'ё',
		'ж',
		'з',
		'и',
		'й',
		'к',
		'л',
		'м',
		'н',
		'о',
		'п',
		'р',
		'с',
		'т',
		'у',
		'ф',
		'х',
		'ц',
		'ч',
		'ш',
		'щ',
		'ъ',
		'ы',
		'ь',
		'э',
		'ю',
		'я',
		'А',
		'Б',
		'В',
		'Г',
		'Д',
		'Е',
		'Ё',
		'Ж',
		'З',
		'И',
		'Й',
		'К',
		'Л',
		'М',
		'Н',
		'О',
		'П',
		'Р',
		'С',
		'Т',
		'У',
		'Ф',
		'Х',
		'Ц',
		'Ч',
		'Ш',
		'Щ',
		'Ъ',
		'Ы',
		'Ь',
		'Э',
		'Ю',
		'Я'
	);

	$latin = array(
		'a',
		'b',
		'v',
		'g',
		'd',
		'e',
		'jo',
		'zh',
		'z',
		'i',
		'j',
		'k',
		'l',
		'm',
		'n',
		'o',
		'p',
		'r',
		's',
		't',
		'u',
		'f',
		'h',
		'c',
		'ch',
		'sh',
		'sch',
		'-',
		'y',
		'-',
		'je',
		'ju',
		'ja',
		'a',
		'b',
		'v',
		'g',
		'd',
		'e',
		'jo',
		'zh',
		'z',
		'i',
		'j',
		'k',
		'l',
		'm',
		'n',
		'o',
		'p',
		'r',
		's',
		't',
		'u',
		'f',
		'h',
		'c',
		'ch',
		'sh',
		's',
		't',
		'u',
		'f',
		'h',
		'c',
		'ch',
		'sh',
		'sch',
		'-',
		'y',
		'-',
		'je',
		'ju',
		'ja'
	);

	$str = str_replace( $kirilica, $latin, $str );

	if ( $use_dot ) {
		$str = preg_replace( '|[^a-z0-9_\-]|', '', $str );
	} else {
		$str = preg_replace( '|[^a-z0-9_\.\(\)]|', '', $str );
	}

	while ( strpos( $str, '--' ) !== false ) {
		$str = str_replace( '--', '-', $str );
	}

	if ( substr( $str, - 1 ) == '-' ) {
		$str = substr( $str, 0, - 1 );
	}

	return $str;
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

function subDirPrefix()
{
	$rewriteBase = Router::rawRewriteBase();
	if(!empty($rewriteBase)) {
		return '/'.$rewriteBase[0];
	}
	return '';
}