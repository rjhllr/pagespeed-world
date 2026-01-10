<?php

namespace App\Console\Commands;

use App\Models\BundleSize;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class AnalyzeBundleSize extends Command
{
    protected $signature = 'bundle:analyze 
                            {page? : Page ID or URL to analyze}
                            {--url= : Direct URL to analyze (without saving)}
                            {--raw : Show raw JSON output from the analyzer}';

    protected $description = 'Manually analyze bundle size for a page';

    public function handle(): int
    {
        // Direct URL mode (no database)
        if ($url = $this->option('url')) {
            return $this->analyzeUrl($url);
        }

        // Page mode
        $pageInput = $this->argument('page');

        if (!$pageInput) {
            // List available pages
            $pages = Page::where('is_active', true)->get();
            
            if ($pages->isEmpty()) {
                $this->error('No active pages found.');
                return Command::FAILURE;
            }

            $this->info('Available pages:');
            $this->table(
                ['ID', 'Name', 'URL'],
                $pages->map(fn ($p) => [$p->id, $p->name, $p->url])->toArray()
            );

            $pageInput = $this->ask('Enter page ID to analyze');
        }

        $page = Page::find($pageInput);

        if (!$page) {
            $this->error("Page with ID {$pageInput} not found.");
            return Command::FAILURE;
        }

        $this->info("Analyzing bundle size for: {$page->name}");
        $this->info("URL: {$page->url}");
        $this->newLine();

        return $this->analyzeUrl($page->url, $page);
    }

    private function analyzeUrl(string $url, ?Page $page = null): int
    {
        $scriptPath = base_path('scripts/analyze-bundle.js');

        if (!file_exists($scriptPath)) {
            $this->error("Bundle analyzer script not found at: {$scriptPath}");
            return Command::FAILURE;
        }

        // Check if node_modules exists
        $nodeModulesPath = base_path('scripts/node_modules');
        if (!is_dir($nodeModulesPath)) {
            $this->warn('Node modules not installed. Installing...');
            $installResult = Process::path(base_path('scripts'))
                ->timeout(120)
                ->run('npm install');

            if (!$installResult->successful()) {
                $this->error('Failed to install node modules:');
                $this->error($installResult->errorOutput());
                return Command::FAILURE;
            }
            $this->info('Node modules installed.');
        }

        $this->info('Starting bundle analysis...');
        $this->info('This may take up to 60 seconds...');
        $this->newLine();

        $startTime = microtime(true);

        $result = Process::timeout(120)
            ->run(['node', $scriptPath, $url]);

        $duration = round(microtime(true) - $startTime, 2);

        if (!$result->successful()) {
            $this->error('Analysis failed!');
            $this->error('Exit code: ' . $result->exitCode());
            $this->error('Error output:');
            $this->line($result->errorOutput());
            $this->newLine();
            $this->info('Standard output:');
            $this->line($result->output());
            return Command::FAILURE;
        }

        $output = $result->output();
        $data = json_decode($output, true);

        if (!$data) {
            $this->error('Failed to parse JSON output:');
            $this->line($output);
            return Command::FAILURE;
        }

        if ($this->option('raw')) {
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }

        if (!($data['success'] ?? false)) {
            $this->error('Analysis returned error: ' . ($data['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        // Display results
        $this->info("✅ Analysis completed in {$duration}s");
        $this->newLine();

        // Totals
        $this->info('📊 TOTALS');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Size (Uncompressed)', BundleSize::formatBytes($data['totals']['size'] ?? 0)],
                ['Transfer Size (Gzipped)', BundleSize::formatBytes($data['totals']['transferSize'] ?? 0)],
                ['Compression Ratio', ($data['totals']['compressionRatio'] ?? 0) . '%'],
                ['Total Requests', $data['totals']['requests'] ?? 0],
                ['Slow Requests (>1s)', $data['totals']['slowRequestCount'] ?? 0],
            ]
        );

        // Timing
        $this->newLine();
        $this->info('⏱️  TIMING');
        $this->table(
            ['Metric', 'Value'],
            [
                ['DOM Content Loaded', ($data['timing']['domContentLoaded'] ?? 0) . 'ms'],
                ['Full Load Time', ($data['timing']['loadTime'] ?? 0) . 'ms'],
            ]
        );

        // Breakdown
        $this->newLine();
        $this->info('📦 BREAKDOWN BY TYPE');
        $breakdown = $data['breakdown'] ?? [];
        $breakdownTable = [];
        foreach ($breakdown as $type => $info) {
            $breakdownTable[] = [
                ucfirst($type),
                BundleSize::formatBytes($info['size'] ?? 0),
                BundleSize::formatBytes($info['transferSize'] ?? 0),
                $info['requests'] ?? 0,
                ($info['downloadTime'] ?? 0) . 'ms',
            ];
        }
        $this->table(
            ['Type', 'Size', 'Transfer', 'Requests', 'Download Time'],
            $breakdownTable
        );

        // Top 10 largest resources
        $this->newLine();
        $this->info('📁 TOP 10 LARGEST RESOURCES');
        $resources = array_slice($data['resources'] ?? [], 0, 10);
        $resourceTable = [];
        foreach ($resources as $resource) {
            $urlParts = parse_url($resource['url']);
            $shortUrl = ($urlParts['path'] ?? $resource['url']);
            if (strlen($shortUrl) > 60) {
                $shortUrl = '...' . substr($shortUrl, -57);
            }
            $resourceTable[] = [
                $shortUrl,
                $resource['type'],
                BundleSize::formatBytes($resource['size'] ?? 0),
                ($resource['downloadTime'] ?? 0) . 'ms',
            ];
        }
        $this->table(
            ['Resource', 'Type', 'Size', 'Time'],
            $resourceTable
        );

        // Slowest resources
        if (!empty($data['slowestResources'])) {
            $this->newLine();
            $this->info('🐌 TOP 5 SLOWEST RESOURCES');
            $slowest = array_slice($data['slowestResources'], 0, 5);
            $slowTable = [];
            foreach ($slowest as $resource) {
                $urlParts = parse_url($resource['url']);
                $shortUrl = ($urlParts['path'] ?? $resource['url']);
                if (strlen($shortUrl) > 60) {
                    $shortUrl = '...' . substr($shortUrl, -57);
                }
                $slowTable[] = [
                    $shortUrl,
                    $resource['type'],
                    ($resource['downloadTime'] ?? 0) . 'ms',
                    $resource['slow'] ? '⚠️ SLOW' : '',
                ];
            }
            $this->table(
                ['Resource', 'Type', 'Time', 'Status'],
                $slowTable
            );
        }

        // Save to database if page provided
        if ($page) {
            $this->newLine();
            if ($this->confirm('Save this result to the database?', true)) {
                $bundleSize = $this->saveResult($page, $data);
                $this->info("✅ Saved as BundleSize ID: {$bundleSize->id}");
            }
        }

        return Command::SUCCESS;
    }

    private function saveResult(Page $page, array $data): BundleSize
    {
        $breakdown = $data['breakdown'] ?? [];
        $totals = $data['totals'] ?? [];
        $timing = $data['timing'] ?? [];

        return BundleSize::create([
            'page_id' => $page->id,
            'status' => 'success',
            'total_size' => $totals['size'] ?? null,
            'total_transfer_size' => $totals['transferSize'] ?? null,
            'javascript_size' => $breakdown['javascript']['size'] ?? null,
            'javascript_transfer_size' => $breakdown['javascript']['transferSize'] ?? null,
            'javascript_download_time' => $breakdown['javascript']['downloadTime'] ?? null,
            'css_size' => $breakdown['css']['size'] ?? null,
            'css_transfer_size' => $breakdown['css']['transferSize'] ?? null,
            'css_download_time' => $breakdown['css']['downloadTime'] ?? null,
            'image_size' => $breakdown['images']['size'] ?? null,
            'image_transfer_size' => $breakdown['images']['transferSize'] ?? null,
            'image_download_time' => $breakdown['images']['downloadTime'] ?? null,
            'font_size' => $breakdown['fonts']['size'] ?? null,
            'font_transfer_size' => $breakdown['fonts']['transferSize'] ?? null,
            'font_download_time' => $breakdown['fonts']['downloadTime'] ?? null,
            'html_size' => $breakdown['html']['size'] ?? null,
            'html_transfer_size' => $breakdown['html']['transferSize'] ?? null,
            'html_download_time' => $breakdown['html']['downloadTime'] ?? null,
            'other_size' => $breakdown['other']['size'] ?? null,
            'other_transfer_size' => $breakdown['other']['transferSize'] ?? null,
            'total_requests' => $totals['requests'] ?? null,
            'javascript_requests' => $breakdown['javascript']['requests'] ?? null,
            'css_requests' => $breakdown['css']['requests'] ?? null,
            'image_requests' => $breakdown['images']['requests'] ?? null,
            'font_requests' => $breakdown['fonts']['requests'] ?? null,
            'dom_content_loaded' => $timing['domContentLoaded'] ?? null,
            'load_time' => $timing['loadTime'] ?? null,
            'total_download_time' => $totals['downloadTime'] ?? null,
            'slow_request_count' => $totals['slowRequestCount'] ?? null,
            'compression_ratio' => $totals['compressionRatio'] ?? null,
            'raw_data' => $data,
        ]);
    }
}
