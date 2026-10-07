<?php
/**
 * PDO database helper. Every query in this project uses prepared statements,
 * so user input is never concatenated into SQL.
 */

if (!defined('DB_NAME')) {
    http_response_code(500);
    exit('Configuration missing. Please open config.php and fill in your database details.');
}

/**
 * Returns a shared PDO connection.
 */
function db()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $exception) {
            http_response_code(500);
            error_log('DB connection failed: ' . $exception->getMessage());
            exit(
                'Could not connect to the database. Check the DB_ values in config.php. ' .
                'Details were written to your PHP error log.'
            );
        }
    }

    return $pdo;
}

/**
 * Runs a query and returns every row.
 */
function db_all($sql, $params = [])
{
    $statement = db()->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
}

/**
 * Runs a query and returns the first row (or null).
 */
function db_one($sql, $params = [])
{
    $statement = db()->prepare($sql);
    $statement->execute($params);
    $row = $statement->fetch();
    return $row === false ? null : $row;
}

/**
 * Runs a write query and returns the number of affected rows.
 */
function db_run($sql, $params = [])
{
    $statement = db()->prepare($sql);
    $statement->execute($params);
    return $statement->rowCount();
}

/**
 * Inserts a row and returns the new id.
 */
function db_insert($sql, $params = [])
{
    $statement = db()->prepare($sql);
    $statement->execute($params);
    return (int) db()->lastInsertId();
}
