<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require __DIR__ . '/config.php';
date_default_timezone_set($config['app']['timezone']);

require __DIR__ . '/functions.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/queries.php';

/*
 * Connect to MySQL.
 *
 * - If you installed XAMPP and never set a root password, 'pass' must be ''.
 * - If your MySQL runs on a port other than 3306, put the port into 'host'
 *   (e.g. '127.0.0.1;port=3307') — see config.php.
 * - If the connection fails, we show the exact PDO error so you can fix it.
 */
try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $config['db']['host'],
        $config['db']['name']
    );

    $pdo = new PDO(
        $dsn,
        $config['db']['user'],
        $config['db']['pass'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');

    // Show the exact PDO message so you can fix the real problem.
    $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');

    exit(
        '<!doctype html><html><head><meta charset="utf-8"><title>PaciTrack DB Error</title>'
        . '<style>'
        . 'body{font-family:system-ui,Segoe UI,Roboto,sans-serif;max-width:720px;margin:40px auto;padding:0 20px;color:#1f2d33;background:#f7fbfb}'
        . 'h2{color:#b3433c;margin:0 0 12px}'
        . 'code,pre{background:#0f2b32;color:#c9e5e1;padding:2px 6px;border-radius:4px;font-size:13px}'
        . 'pre{padding:14px;overflow:auto;white-space:pre-wrap;word-break:break-word}'
        . 'table{border-collapse:collapse;margin:18px 0;width:100%;font-size:14px}'
        . 'td,th{border:1px solid #d7e4e4;padding:8px 10px;text-align:left;vertical-align:top}'
        . 'th{background:#eaf4f3}'
        . '.hint{background:#fff8e6;border:1px solid #f0dfa8;padding:12px 14px;border-radius:8px;margin:18px 0}'
        . '</style></head><body>'
        . '<h2>PaciTrack database connection failed</h2>'
        . '<p>The exact MySQL error is:</p>'
        . '<pre>' . $msg . '</pre>'
        . '<div class="hint">'
        . '<strong>Match the error above to the fix below:</strong>'
        . '<table>'
        . '<tr><th>Error message contains</th><th>What to do</th></tr>'
        . '<tr><td><code>actively refused</code> or <code>[2002]</code></td><td>MySQL is not running. Open XAMPP Control Panel and click <strong>Start</strong> next to MySQL.</td></tr>'
        . '<tr><td><code>Unknown database</code> or <code>[1049]</code></td><td>The <code>pacitrack</code> database doesn\'t exist. Import <code>database.sql</code> in phpMyAdmin.</td></tr>'
        . '<tr><td><code>Access denied</code> or <code>[1045]</code></td><td>Wrong username or password. Open <code>config.php</code> and set the right root password (XAMPP default is empty).</td></tr>'
        . '<tr><td><code>could not find driver</code></td><td>PDO MySQL is disabled. In <code>php.ini</code>, remove the <code>;</code> before <code>extension=pdo_mysql</code>, then restart Apache.</td></tr>'
        . '<tr><td><code>getaddrinfo</code> or <code>No such host</code></td><td>Change <code>\'host\' => \'localhost\'</code> to <code>\'host\' => \'127.0.0.1\'</code> in <code>config.php</code>.</td></tr>'
        . '</table>'
        . '</div>'
        . '<p>Current config in use: <code>host=' . htmlspecialchars($config['db']['host'], ENT_QUOTES, 'UTF-8')
        . ' db=' . htmlspecialchars($config['db']['name'], ENT_QUOTES, 'UTF-8')
        . ' user=' . htmlspecialchars($config['db']['user'], ENT_QUOTES, 'UTF-8')
        . ' pass=' . ($config['db']['pass'] === '' ? '(empty)' : '(set)')
        . '</code></p>'
        . '</body></html>'
    );
}