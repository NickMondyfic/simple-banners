<?php

declare(strict_types=1);

namespace App\lib;

abstract class Network
{
    /**
     * Accurate determination of the user's IP address
     *
     * @return string
     */
    public static function userIP(): string
    {
        $client = $_SERVER['HTTP_CLIENT_IP'] ?? '';
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';

        if ( filter_var($client, FILTER_VALIDATE_IP) )
            $ip = $client;
        elseif ( filter_var($forward, FILTER_VALIDATE_IP) )
            $ip = $forward;
        else
            $ip = $remote;

        return $ip;
    }
}
