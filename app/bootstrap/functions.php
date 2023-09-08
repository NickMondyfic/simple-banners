<?php

/**
 * For internal debugging
 *
 * @param mixed $var
 * @param int $isExit
 * @param int $isDump
 *
 * @return void
 *
 * @psalm-suppress ForbiddenCode
 */
function D(mixed $var, int $isExit = 1, int $isDump = 0): void
{
    $cli = 'cli' === PHP_SAPI;
    if ( !$cli )
        print '<div style="background-color: #ffffff; padding: 3px; z-index: 1000;"><pre style="text-align: left;font-size: 15px;color: white;font-family: sans-serif;background: #000000;padding: 15px;border: 1px solid #878787;margin-top: 2px;margin-bottom:0">';
    if ( $isDump )
        var_dump($var);
//    elseif ( function_exists('dd') )
//        dd($var);
    else
        print_r($var);
    if ( !$cli )
        print '</pre></div>';
    if ( $isExit )
        exit;
}
