<?php

namespace App\Ship\Tests;

use App\Ship\Abstracts\Tests\PhpUnit\TestCase as AbstractTestCase;
use App\Ship\Traits\CreatesApplication;

abstract class TestCase extends AbstractTestCase
{
    use CreatesApplication;
}
