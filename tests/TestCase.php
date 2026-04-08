<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesWordPressData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWordPressDatabase();
    }
}
