<?php

namespace Tests\Feature;

use App\Models\BundleSize;
use App\Models\CrawlResult;
use App\Models\Organization;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->artisan('migrate:fresh');
    }

    public function test_dashboard_page_includes_bucketed_chart_data_and_hero_background(): void
    {
        $organization = Organization::create([
            'name' => 'Test Org',
            'slug' => Str::slug('Test Org'),
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => false,
        ]);
        $user->organizations()->attach($organization->id);

        $page = Page::create([
            'organization_id' => $organization->id,
            'is_active' => true,
            'url' => 'https://example.com',
            'name' => 'Example',
            'last_crawled_at' => now(),
        ]);

        // Recent crawl results (mobile + desktop)
        $mobileResult = CrawlResult::create([
            'page_id' => $page->id,
            'strategy' => 'mobile',
            'status' => 'success',
            'performance_score' => 88,
            'first_contentful_paint' => 1234,
            'largest_contentful_paint' => 2345,
            'total_blocking_time' => 150,
            'cumulative_layout_shift' => 0.03,
        ]);
        $mobileResult->forceFill(['created_at' => now()->subHours(2)])->saveQuietly();

        $desktopResult = CrawlResult::create([
            'page_id' => $page->id,
            'strategy' => 'desktop',
            'status' => 'success',
            'performance_score' => 92,
            'first_contentful_paint' => 1100,
            'largest_contentful_paint' => 1900,
            'total_blocking_time' => 80,
            'cumulative_layout_shift' => 0.01,
        ]);
        $desktopResult->forceFill(['created_at' => now()->subHours(3)])->saveQuietly();

        // Bundle size with filmstrip for hero background
        $bundle = BundleSize::create([
            'page_id' => $page->id,
            'status' => 'success',
            'total_size' => 102400,
            'total_transfer_size' => 51200,
            'filmstrip' => [
                ['timestamp' => 1200, 'event' => 'lcp', 'path' => 'screenshots/bundle-sizes/1/1200.webp'],
                ['timestamp' => 2000, 'event' => 'load', 'path' => 'screenshots/bundle-sizes/1/2000.webp'],
            ],
        ]);
        $bundle->forceFill(['created_at' => now()->subHour()])->saveQuietly();

        $response = $this->actingAs($user)->get(route('dashboard.page', $page));

        $response->assertStatus(200);
        $response->assertViewHas('heroBackgroundUrl');

        $chartData = $response->viewData('chartData');
        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('bucketed', $chartData);
        $this->assertNotEmpty($chartData['bucketed']['labels']);
        $this->assertNotEmpty(array_filter($chartData['bucketed']['mobile']['performance']));
        $this->assertNotEmpty(array_filter($chartData['bucketed']['desktop']['performance']));
    }
}
