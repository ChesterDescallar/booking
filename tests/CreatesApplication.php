<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        // Override APP_URL for testing before bootstrapping
        putenv('APP_URL=http://localhost');
        $_ENV['APP_URL'] = 'http://localhost';
        $_SERVER['APP_URL'] = 'http://localhost';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
