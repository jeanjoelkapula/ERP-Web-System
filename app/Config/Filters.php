<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
		'password' => \App\Filters\PasswordFilter::class,
		//'authfilter' => \CodeIgniter\Filters\AuthFilter::class,
		//'auth' => \App\Filters\AuthFilter::class
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			//'authfilter' => ['except'=>['login']],
			'password'=>['except'=>['login','login/process','login/logout','team/*','/','logout','auth/*']],
			//'honeypot'
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			//'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [];
}
