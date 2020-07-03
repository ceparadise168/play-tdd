<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function initDatabase()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    protected function resetDatabase()
    {
        Artisan::call('migrate:reset');
    }
}
