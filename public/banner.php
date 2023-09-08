<?php

declare(strict_types=1);

use App\lib\Network;

$kernel = require_once(dirname(__DIR__) . '/app/bootstrap/loader.php');

if ( !isset($_GET['bid']) || !is_numeric($_GET['bid']) )
    $_GET['bid'] = 1; // if bid is not specified, we assume that this is the first banner

$banner_id = (int)$_GET['bid'];
if ( !$banner = $kernel->db()->fetchOne("SELECT * FROM app_banner WHERE banner_id={$banner_id} LIMIT 1") )
    error404();

$banner_file = APP_PATH_IMAGES . $banner['banner_image'];
if ( file_exists($banner_file) && is_file($banner_file) && ($image_info = getimagesize($banner_file)) && !empty($image_info['mime']) )
{
    $dt = $kernel->dateTime;
    $ip = ip2long(Network::userIP()) ?: 0;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'undefined';
    $current_page = $_SERVER['HTTP_REFERER'] ?? 'undefined';
    $hash_id = md5($banner_id . $ip . $user_agent . $current_page);
    $hash_id_sql = $kernel->db()->quote($hash_id);

    $last_views = $kernel->db()->fetchColumn("SELECT views_count FROM app_banner_stats WHERE hash_id={$hash_id_sql} LIMIT 1");

    if ( false === $last_views )
    {
        $sth = $kernel->db()->queryBindable('INSERT INTO app_banner_stats SET views_count=1,view_date=?,hash_id=?,banner_id=?,ip_address=?,page_url=?,user_agent=?',
            [$dt, $hash_id, $banner_id, $ip, $current_page, $user_agent]);
    }
    else
    {
        $sth = $kernel->db()->queryBindable('UPDATE app_banner_stats SET views_count=?,view_date=? WHERE hash_id=? LIMIT 1',
            [++$last_views, $dt, $hash_id]);
    }

    header('Content-type: ' . $image_info['mime']);
    readfile($banner_file);
}

error404();

function error404(): never
{
    header("HTTP/1.1 404 Not Found");
    exit;
}
