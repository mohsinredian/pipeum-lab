<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function test_env_example_exists(): void
    {
        $this->assertFileExists(__DIR__ . '/../../.env.example');
    }

    public function test_env_example_has_no_values(): void
    {
        $content = file_get_contents(__DIR__ . '/../../.env.example');
        $this->assertStringNotContainsString('AWS_SECRET_ACCESS_KEY=AKIA', $content);
        $this->assertDoesNotMatchRegularExpression('/^DB_PASSWORD=.+$/m', $content);
    }
}
