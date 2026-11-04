<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_generate_command_runs_successfully(): void
    {
        $this->artisan('sitemap:generate')
            ->assertSuccessful();

        $expectedPath = storage_path('framework/testing/sitemap.xml');
        $this->assertFileExists($expectedPath);
        $content = File::get($expectedPath);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
    }
}
