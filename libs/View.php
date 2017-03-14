<?php

class View {
	public $layout = null;
	public $layoutVars = [];
	public $sharedVars = [];
	public $jsonData = null;

	public static $_instance = null;

	public static function make( $view, $__data = [] ) {
		$file = self::parseLocation( $view );
		ob_start();
		$obj = self::getInstance();
		if ( ! empty( $obj->sharedVars ) ) {
			foreach ( $obj->sharedVars as $k => $v ) {
				$$k = $v;
			}
		}
		foreach ( $__data as $k => $v ) {
			$$k = $v;
		}
		include( $file );
		$data = ob_get_contents();
		ob_end_clean();

		return $data;
	}

	public static function parseLocation( $view ) {
		if ( ENVIRONMENT == 'production' ) {
			$location = APP_PATH . 'views/build/' . str_replace( '.', '/', $view ) . '.php';
			if ( file_exists( $location ) ) {
				return $location;
			}
		}
		$location = APP_PATH . 'views/' . str_replace( '.', '/', $view ) . '.php';
		if ( file_exists( $location ) ) {
			return $location;
		}

		die( "Didn't find view: " . $view );
	}

	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	public static function initLayout( $layout ) {
		$obj         = self::getInstance();
		$obj->layout = $layout;

		return $obj;
	}

	public function add( $key, $__data ) {
		$obj                     = self::getInstance();
		$obj->layoutVars[ $key ] = $__data;
		return $obj;
	}

	public static function share( $key, $__data ) {
		$obj                     = self::getInstance();
		$obj->sharedVars[ $key ] = $__data;

		return $obj;
	}

	public static function json($data)
	{
		$obj = self::getInstance();
		$obj->layout = null;
		$obj->jsonData = json_encode($data);
		return $obj->jsonData;
	}

	public static function push() {
		$obj = self::getInstance();
		if ( $obj->layout !== null ) {
			echo self::make( $obj->layout, $obj->layoutVars );
		} else if ( $obj->jsonData !== null ) {
			echo $obj->jsonData;
		} else {
			echo implode( PHP_EOL, $obj->layoutVars );
		}
	}
}