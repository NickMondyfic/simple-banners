<?php

declare(strict_types=1);

namespace App\lib\database;

use PDO;
use RuntimeException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class MySqlDatabase implements SqlDatabaseInterface, DatabaseInterface
{
    /**
     * Connection instance
     */
    private ?PDO $pdo = null;

    // Credentials
    private readonly string $host;
    private readonly string $user;
    private readonly string $password;
    private readonly string $name;
    private readonly string $charset;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $required = ['host', 'user', 'password', 'name', 'charset'];
        foreach ( $required as $key )
        {
            if ( empty($config[$key]) || !is_string($config[$key]) )
                throw new RuntimeException('Database ' . $key . ' is not configured');
            else
                $this->$key = $config[$key];
        }
    }

    /**
     * Make connection
     *
     * @return $this
     */
    public function connect(): static
    {
        if ( is_null($this->pdo) )
        {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->name}", $this->user, $this->password, [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::MYSQL_ATTR_COMPRESS => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET names ' . $this->charset]);

            if ( version_compare(strval($this->pdo->query('SELECT VERSION()')->fetchColumn()), '5.0.0') < 0 )
                throw new RuntimeException('MySQL 5.0.0 or higher required');
        }

        return $this;
    }

    /**
     * For calls to the database with a guarantee that the connection is open
     *
     * @return PDO
     *
     * @throws RuntimeException
     */
    private function pdo(): PDO
    {
        $pdo = $this->connect()->pdo;
        if ( is_null($pdo) )
            throw new RuntimeException('PDO connection to the database was not established');

        return $pdo;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function quote(string $str): string
    {
        return $this->pdo()->quote($str);
    }

    /**
     * @param string $sql
     * @param bool $fetchColumn
     *
     * @return mixed
     */
    function fetchOne(string $sql, bool $fetchColumn = false): mixed
    {
        return $this->pdo()->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchColumn(string $sql): mixed
    {
        return $this->pdo()->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * @param string $sql
     *
     * @return int
     */
    public function querySimple(string $sql): int
    {
        return (int)$this->pdo()->exec($sql);
    }

    /**
     * @param string $sql
     * @param array $binds
     *
     * @return int
     */
    public function queryBindable(string $sql, array $binds): int
    {
        $sth = $this->pdo()->prepare($sql);
        $sth->execute($binds);

        return $sth->rowCount();
    }
}
