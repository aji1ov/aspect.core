<?php

namespace Aspect\Lib\PhpUnit;

use PHPUnit\Framework\TestCase;

abstract class AspectTestCase extends TestCase
{
    public function tearDown(): void
    {
        \Mockery::close();
        $this->faker()->close();
    }

    public function faker(): Faker
    {
        return Faker::getInstance();
    }
}