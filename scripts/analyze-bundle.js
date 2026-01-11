#!/usr/bin/env node

/**
 * Bundle Size Analyzer
 * 
 * Uses Puppeteer to measure page bundle sizes by intercepting network requests.
 * Tracks download timing, compression ratios, and detects slow/stalling requests.
 * Also captures filmstrip screenshots during page load for visual analysis.
 * 
 * Usage: node analyze-bundle.js <url>
 * Output: JSON with size breakdown, timing data, and filmstrip screenshots
 */

const puppeteer = require('puppeteer');

const url = process.argv[2];

if (!url) {
    console.error(JSON.stringify({ error: 'URL is required' }));
    process.exit(1);
}

// Validate URL
try {
    new URL(url);
} catch (e) {
    console.error(JSON.stringify({ error: 'Invalid URL provided' }));
    process.exit(1);
}

const resourceTypes = {
    script: { size: 0, transferSize: 0, count: 0, downloadTime: 0 },
    stylesheet: { size: 0, transferSize: 0, count: 0, downloadTime: 0 },
    image: { size: 0, transferSize: 0, count: 0, downloadTime: 0 },
    font: { size: 0, transferSize: 0, count: 0, downloadTime: 0 },
    document: { size: 0, transferSize: 0, count: 0, downloadTime: 0 },
    other: { size: 0, transferSize: 0, count: 0, downloadTime: 0 },
};

const resources = [];
const requestTimings = new Map(); // Track request start times
const filmstrip = []; // Store filmstrip screenshots

// Screenshot capture configuration
const SCREENSHOT_INTERVAL_MS = 500;
const SCREENSHOT_QUALITY = 80; // WebP quality (0-100)
const MAX_FRAMES = 12; // cap total frames per run (including event frames)

/**
 * Capture a screenshot and add it to the filmstrip
 */
async function captureScreenshot(page, startTime, event = null) {
    try {
        const timestamp = Date.now() - startTime;
        const screenshot = await page.screenshot({
            type: 'webp',
            quality: SCREENSHOT_QUALITY,
            encoding: 'base64',
        });
        
        filmstrip.push({
            timestamp,
            event,
            image: screenshot,
        });
    } catch (e) {
        // Ignore screenshot errors (page might be navigating)
    }
}

async function analyzeBundleSize() {
    let browser;
    let screenshotInterval = null;
    
    try {
        // Use PUPPETEER_EXECUTABLE_PATH if set (for Alpine Linux with system Chromium)
        const executablePath = process.env.PUPPETEER_EXECUTABLE_PATH || undefined;
        
        browser = await puppeteer.launch({
            headless: 'new',
            executablePath,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--disable-gpu',
                '--window-size=1920x1080',
            ],
        });

        const page = await browser.newPage();
        
        // Set viewport
        await page.setViewport({ width: 1920, height: 1080 });
        
        // Set user agent
        await page.setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        // Enable request interception
        await page.setRequestInterception(true);
        
        page.on('request', (request) => {
            // Track when each request starts
            requestTimings.set(request.url(), {
                startTime: Date.now(),
                resourceType: request.resourceType(),
            });
            request.continue();
        });

        // Track responses
        page.on('response', async (response) => {
            try {
                const request = response.request();
                const resourceType = request.resourceType();
                const responseUrl = response.url();
                
                // Skip data URLs
                if (responseUrl.startsWith('data:')) {
                    return;
                }

                // Calculate download time
                const timing = requestTimings.get(responseUrl);
                const downloadStartTime = timing ? timing.startTime : Date.now();
                
                const headers = response.headers();
                let transferSize = 0;
                let size = 0;

                // Get content-length for transfer size
                if (headers['content-length']) {
                    transferSize = parseInt(headers['content-length'], 10);
                }

                // Try to get actual body size (this also completes the download)
                try {
                    const buffer = await response.buffer();
                    size = buffer.length;
                    if (!transferSize) {
                        transferSize = size;
                    }
                } catch (e) {
                    // Some responses can't be buffered
                    size = transferSize;
                }

                // Calculate total download time (including body download)
                const downloadTime = Date.now() - downloadStartTime;

                // Categorize resource
                let category = 'other';
                const contentType = headers['content-type'] || '';
                
                if (resourceType === 'script' || contentType.includes('javascript')) {
                    category = 'script';
                } else if (resourceType === 'stylesheet' || contentType.includes('css')) {
                    category = 'stylesheet';
                } else if (resourceType === 'image' || contentType.includes('image')) {
                    category = 'image';
                } else if (resourceType === 'font' || contentType.includes('font')) {
                    category = 'font';
                } else if (resourceType === 'document' || contentType.includes('html')) {
                    category = 'document';
                }

                resourceTypes[category].size += size;
                resourceTypes[category].transferSize += transferSize;
                resourceTypes[category].count += 1;
                resourceTypes[category].downloadTime += downloadTime;

                // Calculate compression ratio (0 = no compression, higher = better compression)
                const compressionRatio = size > 0 && transferSize > 0 && transferSize < size
                    ? Math.round((1 - transferSize / size) * 100) 
                    : 0;

                resources.push({
                    url: responseUrl,
                    type: category,
                    size,
                    transferSize,
                    downloadTime,
                    compressionRatio,
                    mimeType: contentType,
                    slow: downloadTime > 1000, // Flag if download took > 1 second
                });
            } catch (e) {
                // Ignore errors for individual resources
            }
        });

        // Navigate and wait for network idle
        const startTime = Date.now();
        let domContentLoaded = null;
        let loadEventTime = null;
        
        // Capture initial screenshot (before navigation completes)
        await captureScreenshot(page, startTime, 'start');
        
        // Start interval-based screenshot capture
        screenshotInterval = setInterval(() => {
            captureScreenshot(page, startTime);
        }, SCREENSHOT_INTERVAL_MS);
        
        // Track DOMContentLoaded event
        page.on('domcontentloaded', async () => {
            domContentLoaded = Date.now() - startTime;
            await captureScreenshot(page, startTime, 'domContentLoaded');
        });
        
        // Track load event
        page.on('load', async () => {
            loadEventTime = Date.now() - startTime;
            await captureScreenshot(page, startTime, 'load');
        });

        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 60000,
        });

        // Stop interval capture and take final screenshot
        if (screenshotInterval) {
            clearInterval(screenshotInterval);
            screenshotInterval = null;
        }
        
        const loadTime = Date.now() - startTime;
        
        // Capture final screenshot at network idle
        await captureScreenshot(page, startTime, 'networkIdle');

        // Calculate totals
        let totalSize = 0;
        let totalTransferSize = 0;
        let totalRequests = 0;
        let totalDownloadTime = 0;

        for (const [type, data] of Object.entries(resourceTypes)) {
            totalSize += data.size;
            totalTransferSize += data.transferSize;
            totalRequests += data.count;
            totalDownloadTime += data.downloadTime;
        }

        // Calculate overall compression ratio
        const overallCompressionRatio = totalSize > 0 && totalTransferSize > 0
            ? Math.round((1 - totalTransferSize / totalSize) * 100)
            : 0;

        // Find slow resources (> 1 second download time)
        const slowResources = resources.filter(r => r.downloadTime > 1000);

        // Sort resources by size for the raw data
        const sortedResources = resources
            .sort((a, b) => b.size - a.size)
            .slice(0, 100); // Top 100 largest resources

        // Sort by download time to find slowest
        const slowestResources = [...resources]
            .sort((a, b) => b.downloadTime - a.downloadTime)
            .slice(0, 20); // Top 20 slowest resources
        
        // Sort filmstrip by timestamp and deduplicate close timestamps, then cap total frames
        const sortedFilmstrip = filmstrip
            .sort((a, b) => a.timestamp - b.timestamp)
            .filter((frame, index, arr) => {
                // Keep event frames and frames that are at least 300ms apart
                if (frame.event) return true;
                if (index === 0) return true;
                const prevFrame = arr[index - 1];
                return frame.timestamp - prevFrame.timestamp >= 300;
            })
            .slice(0, MAX_FRAMES);

        const result = {
            success: true,
            url,
            timestamp: new Date().toISOString(),
            totals: {
                size: totalSize,
                transferSize: totalTransferSize,
                requests: totalRequests,
                downloadTime: totalDownloadTime,
                compressionRatio: overallCompressionRatio,
                slowRequestCount: slowResources.length,
            },
            breakdown: {
                javascript: {
                    size: resourceTypes.script.size,
                    transferSize: resourceTypes.script.transferSize,
                    requests: resourceTypes.script.count,
                    downloadTime: resourceTypes.script.downloadTime,
                    avgDownloadTime: resourceTypes.script.count > 0 
                        ? Math.round(resourceTypes.script.downloadTime / resourceTypes.script.count) 
                        : 0,
                },
                css: {
                    size: resourceTypes.stylesheet.size,
                    transferSize: resourceTypes.stylesheet.transferSize,
                    requests: resourceTypes.stylesheet.count,
                    downloadTime: resourceTypes.stylesheet.downloadTime,
                    avgDownloadTime: resourceTypes.stylesheet.count > 0 
                        ? Math.round(resourceTypes.stylesheet.downloadTime / resourceTypes.stylesheet.count) 
                        : 0,
                },
                images: {
                    size: resourceTypes.image.size,
                    transferSize: resourceTypes.image.transferSize,
                    requests: resourceTypes.image.count,
                    downloadTime: resourceTypes.image.downloadTime,
                    avgDownloadTime: resourceTypes.image.count > 0 
                        ? Math.round(resourceTypes.image.downloadTime / resourceTypes.image.count) 
                        : 0,
                },
                fonts: {
                    size: resourceTypes.font.size,
                    transferSize: resourceTypes.font.transferSize,
                    requests: resourceTypes.font.count,
                    downloadTime: resourceTypes.font.downloadTime,
                    avgDownloadTime: resourceTypes.font.count > 0 
                        ? Math.round(resourceTypes.font.downloadTime / resourceTypes.font.count) 
                        : 0,
                },
                html: {
                    size: resourceTypes.document.size,
                    transferSize: resourceTypes.document.transferSize,
                    requests: resourceTypes.document.count,
                    downloadTime: resourceTypes.document.downloadTime,
                    avgDownloadTime: resourceTypes.document.count > 0 
                        ? Math.round(resourceTypes.document.downloadTime / resourceTypes.document.count) 
                        : 0,
                },
                other: {
                    size: resourceTypes.other.size,
                    transferSize: resourceTypes.other.transferSize,
                    requests: resourceTypes.other.count,
                    downloadTime: resourceTypes.other.downloadTime,
                    avgDownloadTime: resourceTypes.other.count > 0 
                        ? Math.round(resourceTypes.other.downloadTime / resourceTypes.other.count) 
                        : 0,
                },
            },
            timing: {
                domContentLoaded: domContentLoaded,
                loadEvent: loadEventTime,
                loadTime: loadTime,
            },
            resources: sortedResources,
            slowestResources: slowestResources,
            filmstrip: sortedFilmstrip,
        };

        console.log(JSON.stringify(result));

    } catch (error) {
        // Clean up interval on error
        if (screenshotInterval) {
            clearInterval(screenshotInterval);
        }
        
        console.log(JSON.stringify({
            success: false,
            error: error.message,
            url,
        }));
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

analyzeBundleSize();
