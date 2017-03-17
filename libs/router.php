<?php

class Router {
	public $routes = ['GET' => [], 'POST' => []];
	public $missing = null;
	private $rewriteBase = [];
	private $rawRewriteBase = [];
	protected static $_instance = null;

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public static function build($uri = null) {
		if(!$uri) $uri = URI::raw();
		$o = self::getInstance();
		$uri = rtrim($uri, '/');
		if(!is_null($o->rewriteBase)) {
			$uri = preg_replace($o->rewriteBase, '', $uri);
			if(empty($uri)) $uri = '/';
		}
		$method = Request::method();
		$routes = isset($o->routes[$method]) ? $o->routes[$method] : null;
		if(!is_array($routes) or empty($routes)) {
			die('Please provide routes in '.APP_PATH.'routes.php file!');
		}
		foreach($routes as $route => $controller) {
			$route = str_replace('/', '\/', $route);
			$param = [];
			if(preg_match_all('/({[^\/]+})/', $route, $matches)) {
				foreach($matches[1] as $m) {
					$param[] = str_replace(['{', '}'], '', $m);
					$route = str_replace($m, '([^\/]+)', $route);
				}
			}
			if(preg_match('/^'.$route.'$/', $uri, $r)) {
				if(is_callable($controller)) {
					if(count($r) > 1) {
						array_shift($r);
						if(count($param) > 1) {
							return call_user_func($controller, $r);
						} else {
							return call_user_func_array($controller, $r);
						}
					}
				} else {
					list($contr, $method) = explode('@', $controller);
					$c = new $contr();
					array_shift($r);
					$r = explode('/', trim(implode('/', $r), '/'));
					return call_user_func_array([$c, $method], $r);
					//future (php5.6+)
//                    return $c->$method(...$r);
				}
			}
		}

		if(is_null($o->missing)) {
			FWError::raise(404);
		} else {
			if(is_callable($o->missing)) {
				return call_user_func_array($o->missing, URL::current());
			} else {
				list($contr, $method) = explode('@', $o->missing);
				$c = new $contr();
				return $c->$method();
			}
		}
	}

	public static function get($route, $controller) {
		$o = self::getInstance();
		$route = '/'.trim($route, '/');
		$o->routes['GET'][$route] = $controller;
	}

	public static function post($route, $controller) {
		$o = self::getInstance();
		$route = '/'.trim($route, '/');
		$o->routes['POST'][$route] = $controller;
	}

	public static function any($route, $controller) {
		$o = self::getInstance();
		$route = '/'.trim($route, '/');
		$o->routes['GET'][$route] = $controller;
		$o->routes['POST'][$route] = $controller;
	}

	public static function missing($controller) {
		$o = self::getInstance();
		$o->missing = $controller;
	}

	public static function detect($module, $controller) {
		$classMethods = get_class_methods($controller);
		if($classMethods !== null) {
			foreach($classMethods as $method) {
				if(preg_match('/^get(.*)/', $method, $route)) {
					$rt = self::makeRoute($route[1]);
					self::get('/'.$module.'/'.$rt, $controller.'@'.$route[0]);
					self::get('/'.$module.'/'.$rt.'/(.*)', $controller.'@'.$route[0]);
					if($rt === 'index') {
						self::get('/'.$module, $controller.'@'.$route[0]);
					}
				} else if(preg_match('/^post(.*)/', $method, $route)) {
					$rt = self::makeRoute($route[1]);
					self::post('/'.$module.'/'.$rt, $controller.'@'.$route[0]);
					self::post('/'.$module.'/'.$rt.'/(.*)', $controller.'@'.$route[0]);
					if($rt === 'index') {
						self::post('/' . $module, $controller . '@' . $route[0]);
					}
				} else if(preg_match('/^any(.*)/', $method, $route)) {
					$rt = self::makeRoute($route[1]);
					if($rt === 'index') {
						self::any('/' . $module, $controller . '@' . $route[0]);
					}
				}
			}
		}
	}

	private static function makeRoute($route) {
		$route = lcfirst($route);
		return preg_replace_callback('/[A-Z]/', function($m) {
			return '-'.lcfirst($m[0]);
		}, $route);
	}

	public static function rewriteBase($dir = null)
	{
		$o = self::getInstance();
		if(!is_null($dir)) {
			$rawDir = [];
			if(!is_array($dir)) {
				$dir = [$dir];
			}
			foreach($dir as $k => $d) {
				$d = trim($d, '/ ');
				$dir[$k] = '@^/'.$d.'@';
				$rawDir[$k] = $d;
			}
			$o->rawRewriteBase = array_merge($o->rawRewriteBase, $rawDir);
			$o->rewriteBase = array_merge($o->rewriteBase, $dir);
		}
		return $o->rewriteBase;
	}

	public static function rawRewriteBase()
	{
		$o = self::getInstance();
		return $o->rawRewriteBase;
	}

	public static function routes()
	{
		$o = self::getInstance();
		return $o->routes;
	}
}

require_once(APP_PATH.'routes.php');

Router::build();