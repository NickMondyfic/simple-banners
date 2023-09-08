<?php

declare(strict_types=1);

namespace App;

use RuntimeException;
use App\lib\database\SqlDatabaseInterface;

class Kernel
{
    const MIN_PHP_VERSION = '8.2.9';

    /**
     * Current unix timestamp
     */
    public readonly int $timeStamp;

    /**
     * Current date (YYYY-MM-DD)
     */
    public readonly string $date;

    /**
     * Current datetime (YYYY-MM-DD HH:MM:SS)
     */
    public readonly string $dateTime;

    /**
     * Configuration data
     */
    private array $config;

    /**
     * Database interface
     */
    private readonly SqlDatabaseInterface $database;

    /**
     * @param array $config
     * @param SqlDatabaseInterface $database
     *
     * @throws RuntimeException
     */
    public function __construct(array $config, SqlDatabaseInterface $database)
    {
        if ( 0 < version_compare(self::MIN_PHP_VERSION, phpversion()) )
            throw new RuntimeException('PHP >= ' . self::MIN_PHP_VERSION . ' is required');

        $this->config = $config;

        $this->database = $database;

        # Default time zone
        if ( $time_zone = $this->config('timeZone') )
            date_default_timezone_set($time_zone);

        # Time variables
        $this->timeStamp = time();
        $this->date = date('Y-m-d', $this->timeStamp);
        $this->dateTime = date('Y-m-d H:i:s', $this->timeStamp);
    }

    /**
     * Getting config param value
     *
     * @param string $key
     *
     * @return mixed
     */
    public function config(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    /**
     * @return SqlDatabaseInterface
     */
    public function db(): SqlDatabaseInterface
    {
        return $this->database;
    }
}
