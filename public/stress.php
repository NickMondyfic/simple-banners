<?php

declare(strict_types=1);

$kernel = require_once(dirname(__DIR__) . '/app/bootstrap/loader.php');

// Unsafe script, blocking required

$sql_count = 'SELECT COUNT(1) FROM app_banner_stats';
$count_before = $kernel->db()->fetchColumn($sql_count);

$inserted = 0;
$limit = 100000;
$per_request = 1500;
$top_limit = 1000000;

while ( $inserted < $limit )
{
    $needs = ($left = $limit - $inserted) > $per_request ? $per_request : $left;

    $count_real = $count_before + $inserted;
    if ( ($count_real + $needs > $top_limit) && !$needs = $top_limit - $count_real )
        break;

    try
    {
        $values = multiInsertValues($needs, $kernel->db()->quote(...));
        $kernel->db()->querySimple('INSERT INTO app_banner_stats (views_count,view_date,hash_id,banner_id,ip_address,page_url,user_agent) VALUES ' . $values);
    }
    catch ( Exception $e )
    {
        dd($e);
    }

    $inserted += $needs;
}

$count_after = $kernel->db()->fetchColumn($sql_count);

D(['inserted' => $inserted, 'total' => $count_after, 'max' => $top_limit], 0);

echo '<hr>Filled!';

/**
 * @param int $limit
 * @param callable $quoteFunc
 *
 * @return string
 */
function multiInsertValues(int $limit, callable $quoteFunc): string
{
    $i = 0;
    $values = [];
    do
    {
        $unique_int = rand(1, 300000);
        $ip = $views = rand(1, 50000);
        $banner_id = rand(1, 2);
        $current_page = 'http://infuse.test.com/index' . $banner_id . '.html';
        $user_agent = ($_SERVER['HTTP_USER_AGENT'] ?? 'undefined') . $unique_int;
        $dt = date('Y-m-d H:i:s', time());
        $hash_id = md5($banner_id . $ip . $user_agent . $current_page . time());

        $values[] = implode(',', [$views, $quoteFunc($dt), $quoteFunc($hash_id), $banner_id, $ip, $quoteFunc($current_page), $quoteFunc($user_agent)]);

        $i++;
    }
    while ( $i < $limit );

    return '(' . implode('),(', $values) . ')';
}
