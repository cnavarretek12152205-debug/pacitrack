<?php
declare(strict_types=1);

return [
    'db' => [
        // If MySQL is on a non-standard port, use '127.0.0.1;port=3307'.
        'host' => '127.0.0.1;port=3307',

        'name' => 'pacitrack',
        'user' => 'root',

        // XAMPP default MySQL root password is empty. If you set one,
        // put it here between the quotes.
        'pass' => '',
    ],
    'app' => [
        'name'               => 'PaciTrack',
        'tagline'            => 'Equipment intelligence',
        'timezone'           => 'Asia/Manila',
        'session_lifetime'   => 60 * 60 * 24 * 30,
        'demo_password_hint' => 'PaciTrack2025!',
    ],
];