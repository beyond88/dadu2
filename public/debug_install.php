<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

$files = [
    'InstallCheckMiddleware'    => __DIR__ . '/../app/Http/Middleware/InstallCheckMiddleware.php',
    'NotInstallCheckMiddleware' => __DIR__ . '/../app/Http/Middleware/NotInstallCheckMiddleware.php',
];

foreach ($files as $name => $path) {
    echo "<h3>$name</h3><pre>" . htmlspecialchars(file_get_contents($path)) . "</pre><hr>";
}
