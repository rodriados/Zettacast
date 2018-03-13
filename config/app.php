<?php

use Zettacast\Zettacast;

return [
	/*
	 * Application name.
	 * This is the name of your application. This is the value that will be
	 * used whenever the framework is need of placing your application's name.
	 */
	'name' => 'Zettacast',
	
	/*
	 * Application environment.
	 * Informs the kind of environment your application is currently running
	 * in. This may affect the behavior of some framework's modules slightly.
	 */
	'env' => Zettacast::DEVELOPMENT,
	
	/**
	 * Application hostname.
	 * This value is the hostname used by your application. This the address
	 * through which your application's users will be able to reach it. The
	 * framework will use it for generating correct application links.
	 */
	'url' => 'zettacast.localhost',

	/*
	 * Application language configuration.
	 * The locale determines the language to be used as the application's
	 * default. The framework will translate all of its strings to the given
	 * locale, if supported.
	 */
	'locale' => 'en_US',
	
	/*
	 * Application default timezone.
	 * Here you can set the timezone to be used as the default by your
	 * application. Whenever the framework or your application needs to show
	 * date and time, this timezone will be used. Unless, it's explicitly
	 * customized by users or application's modules.
	 */
	'timezone' => 'UTC',
	
	/*
	 * Application charset.
	 * The character set determines which codification texts across the
	 * application should use. Although UTF-8 is a good choice for most cases,
	 * feel free to change it if you think you need to.
	 */
	'charset' => 'UTF-8',
];
