<?php

declare(strict_types=1);

namespace App\lib\database;

interface SqlDatabaseInterface
{
    /**
     * @param string $str
     *
     * @return string
     */
    function quote(string $str): string;

    /**
     * @param string $sql
     *
     * @return mixed
     */
    function fetchOne(string $sql): mixed;

    /**
     * @param string $sql
     *
     * @return mixed
     */
    function fetchColumn(string $sql): mixed;

    /**
     * @param string $sql
     *
     * @return int
     */
    function querySimple(string $sql): int;

    /**
     * @param string $sql
     * @param array $binds
     *
     * @return int
     */
    function queryBindable(string $sql, array $binds): int;
}
