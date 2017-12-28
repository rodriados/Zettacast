<?php
/**
 * Zettacast bootstrap file.
 * This file is responsible for booting the framework up and starting all basic
 * code needed for a perfectly functional execution.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */

/**#@+
 * These constants hold the path of the framework's directories. You should
 * modify these constants every time you change the location or name of the
 * framework's folders.
 * @var string Framework's directories constants.
 */
define('APPPATH', DOCROOT.'/app');
define('BINPATH', DOCROOT.'/bin');
define('SRCPATH', DOCROOT.'/src');
define('TMPPATH', DOCROOT.'/tmp');
define('TESTSPATH', DOCROOT.'/tests');
define('PUBLICPATH', DOCROOT.'/public');
define('ASSETSPATH', PUBLICPATH.'/assets');
/**#@-*/

/**#@+
 * These constants hold the path of the application's directories. You should
 * modify these constants every time you change the location or name of the
 * application's folders.
 * @var string Application's directories constants.
 */
define('APPBINPATH', APPPATH.'/bin');
define('APPSRCPATH', APPPATH.'/src');
define('APPTESTSPATH', APPPATH.'/tests');
define('APPCONFIGPATH', APPPATH.'/config');
define('APPTEMPLATESPATH', APPPATH.'/templates');
define('APPRESOURCESPATH', APPPATH.'/resources');
/**#@-*/
