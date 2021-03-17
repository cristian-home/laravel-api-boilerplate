<?php

/**
 * Create sqlite file if it does not exist and return file path
 *
 * @return string
 */
function makeSqliteFile()
{
    $sqlite_file = env('DB_DATABASE', 'database.sqlite');
    $sqlite_file_path = database_path($sqlite_file);

    // Si está en modo test
    if ($sqlite_file == ':memory:') {
        return $sqlite_file;
    }

    if (env('DB_CONNECTION') == 'sqlite') {
        if (!file_exists($sqlite_file_path)) {
            fopen($sqlite_file_path, 'w');
        }
    }

    return $sqlite_file_path;
}
