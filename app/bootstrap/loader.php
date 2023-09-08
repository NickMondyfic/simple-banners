<?php

declare(strict_types=1);

require_once('constants.php');
require_once('functions.php');

# Checking VAR folder

if ( !file_exists(APP_PATH_VAR) )
{
    if ( !is_writable(APP_PATH_ROOT) )
        D('root folder is not writable and var/ folder is not exists');

    $umask = umask(0);
    $access = 0777;
    mkdir(APP_PATH_VAR, $access);
    mkdir(APP_PATH_LOG, $access);
    file_put_contents(APP_PATH_VAR . '.htaccess', 'deny from all');
    umask($umask);
}
elseif ( !is_dir(APP_PATH_VAR) )
    D('Error: var/ folder is not a folder');
elseif ( !is_writable(APP_PATH_VAR) )
    D('Error: var/ folder is not writable');

# Composer autoload

if ( !file_exists(APP_PATH_ROOT . 'vendor/autoload.php') )
    D('Error: composer packages are not installed');
require_once(APP_PATH_ROOT . 'vendor/autoload.php');

# Error processing

if ( class_exists('\Symfony\Component\ErrorHandler\Debug') )
{
    Symfony\Component\ErrorHandler\Debug::enable();
}
else
{
    // simple handler
    set_exception_handler(function (Throwable $e) {
        $info = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
        ];

        $url = $_SERVER['REQUEST_URI'] ?? '';
        if ( isset($_SERVER['HTTP_HOST']) )
            $url = $_SERVER['HTTP_HOST'] . $url;

        $data = date('[Y-m-d H:i:s]')
            . ('cli' === PHP_SAPI ? ' script: ' . ($_SERVER['PHP_SELF'] ?? '-') : ' url: ' . $url)
            . PHP_EOL
            . preg_replace('/^/m', str_repeat(' ', 22), var_export($info, true));

        error_log($data . PHP_EOL, 3, APP_PATH_LOG . 'php_errors.log');

        if ( 'cli' === PHP_SAPI )
            echo 'Uncaught exception occurred';
        else
            echo('500 - INTERNAL ERROR');

        http_response_code(500);
        exit();
    });
}

# Creating a kernel class

$config = require_once(APP_PATH_ROOT . 'config.php');

$db = new App\lib\database\MySqlDatabase($config['db'] ?? []);

return new App\Kernel($config, $db);
