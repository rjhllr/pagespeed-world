<?php

return [
    'posts' => [
        [
            'slug' => 'core-web-vitals-playbook',
            'date' => '2025-01-05',
            'reading_time' => '9 min',
            'category' => 'Core Web Vitals',
            'locales' => [
                'en' => [
                    'title' => 'Core Web Vitals playbook: baselines, alerts, and quick wins',
                    'excerpt' => 'How to set practical baselines for LCP, CLS, and INP, with alerts that reduce noise.',
                    'body' => '<p>Start with pragmatic budgets: LCP &lt; 2.5s (mobile) and &lt; 1.8s (desktop), CLS &lt; 0.1, INP &lt; 200ms. Track them per template or route, not just per origin, and distinguish mobile from desktop so regressions are actionable.</p>

<!-- @TODO: Add screenshot of Core Web Vitals dashboard showing metric trends over time -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Core Web Vitals Dashboard Overview</p>
  <p class="text-sm text-gray-500 mt-2">Shows LCP, CLS, and INP metrics tracked over 30 days with 75th percentile trend lines for mobile and desktop</p>
</div>

<h3>How to define baselines</h3>

<p>Rather than guessing at numbers, measure your current performance across real user data. Here\'s a practical approach:</p>

<h4>Step 1: Collect data by percentile</h4>

<p>Use 75th percentile, not averages, and watch p95 for tail problems. Here\'s an example configuration for tracking:</p>

<pre><code class="language-javascript">// Example: Tracking Core Web Vitals with web-vitals library
import {onCLS, onINP, onLCP} from \'web-vitals\';

function sendToAnalytics({name, value, rating, delta, id}) {
  // Send to your analytics endpoint
  fetch(\'/api/vitals\', {
    method: \'POST\',
    body: JSON.stringify({
      metric: name,
      value: value,
      rating: rating,  // "good", "needs-improvement", or "poor"
      delta: delta,
      id: id,
      route: window.location.pathname,
      deviceType: window.innerWidth &lt; 768 ? \'mobile\' : \'desktop\'
    }),
    headers: {\'Content-Type\': \'application/json\'}
  });
}

// Track each vital
onLCP(sendToAnalytics);
onCLS(sendToAnalytics);
onINP(sendToAnalytics);
</code></pre>

<!-- @TODO: Add screenshot showing percentile distribution graph (p50, p75, p95) for LCP metric -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: LCP Percentile Distribution</p>
  <p class="text-sm text-gray-500 mt-2">Bar chart showing p50, p75, and p95 LCP values across different pages, highlighting why averages can be misleading</p>
</div>

<h4>Step 2: Pair with leading indicators</h4>

<p>Core Web Vitals tell you <em>what</em> is slow, but leading indicators help predict <em>why</em>. Track these alongside your vitals:</p>

<ul>
<li><strong>JS execution time</strong>: Total blocking time and long task count</li>
<li><strong>Total transfer size</strong>: Compressed bytes over the wire</li>
<li><strong>Number of requests</strong>: Especially third-party requests</li>
<li><strong>HTML weight</strong>: Uncompressed document size</li>
<li><strong>Image bytes</strong>: Total image payload</li>
</ul>

<pre><code class="language-javascript">// Example: Tracking leading indicators with PerformanceObserver
const perfObserver = new PerformanceObserver((list) =&gt; {
  for (const entry of list.getEntries()) {
    if (entry.entryType === \'resource\') {
      // Track resource metrics
      const metrics = {
        url: entry.name,
        transferSize: entry.transferSize,
        duration: entry.duration,
        type: entry.initiatorType
      };

      // Aggregate by type
      if (entry.initiatorType === \'img\') {
        window.imageBytes = (window.imageBytes || 0) + entry.transferSize;
      }
    }
  }
});

perfObserver.observe({entryTypes: [\'resource\', \'navigation\']});
</code></pre>

<!-- @TODO: Add example chart correlating image bytes with LCP scores -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Leading Indicators vs. LCP Correlation</p>
  <p class="text-sm text-gray-500 mt-2">Scatter plot showing relationship between total image bytes and LCP scores, demonstrating predictive value</p>
</div>

<h3>Noise-free alerting</h3>

<p>The worst alerting systems cry wolf constantly. Build alerts that signal real regressions:</p>

<h4>Delta-based triggers</h4>

<p>Trigger alerts on deltas (e.g., +15% LCP week-over-week) instead of single-run spikes. Example Datadog monitor configuration:</p>

<pre><code class="language-yaml"># Example alerting configuration
alert_name: "LCP Regression on Homepage"
metric: "web.vitals.lcp.p75"
query: "avg:web.vitals.lcp.p75{route:/,device:mobile}"
comparison: "week_before"
threshold:
  critical: 15%  # Alert if LCP increased by 15% or more
  warning: 10%
conditions:
  - sample_size &gt;= 20
  - consecutive_violations: 2  # Must fail twice to reduce noise
group_by:
  - route
  - device_type
</code></pre>

<h4>Sample size gating</h4>

<p>Alert only when sample size &gt;= 20 runs to avoid flicker. Here\'s a JavaScript implementation:</p>

<pre><code class="language-javascript">// Example: Alert logic with sample size gating
function checkForRegression(currentWeek, previousWeek) {
  if (currentWeek.sampleSize &lt; 20 || previousWeek.sampleSize &lt; 20) {
    return {shouldAlert: false, reason: \'Insufficient sample size\'};
  }

  const delta = ((currentWeek.p75 - previousWeek.p75) / previousWeek.p75) * 100;

  if (delta &gt; 15) {
    return {
      shouldAlert: true,
      severity: \'critical\',
      message: `LCP increased by ${delta.toFixed(1)}% (${previousWeek.p75}ms → ${currentWeek.p75}ms)`,
      affectedRoute: currentWeek.route,
      sampleSize: currentWeek.sampleSize
    };
  }

  return {shouldAlert: false};
}
</code></pre>

<!-- @TODO: Add screenshot of alert dashboard showing grouped alerts by template -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Alert Dashboard with Template Grouping</p>
  <p class="text-sm text-gray-500 mt-2">Shows alerts grouped by page template with severity levels, making it easy to identify problem areas</p>
</div>

<h4>Weekly rollup reports</h4>

<p>Roll up reports weekly with sparklines per metric and a short narrative: what changed, likely cause, and the one recommended fix.</p>

<pre><code class="language-markdown">## Weekly Performance Report - Jan 8, 2025

### Summary
2 regressions detected, 1 improvement

### LCP (Homepage Mobile) 📈 REGRESSION
- **Current**: 2.8s (p75) ↑ from 2.3s
- **Change**: +21.7% WoW
- **Likely cause**: New hero image (450KB → 890KB)
- **Recommendation**: Compress hero to WebP/AVIF, add preload

### CLS (Product Pages) 📉 IMPROVED
- **Current**: 0.08 ↓ from 0.14
- **Change**: -42.8% WoW
- **Cause**: Fixed ad slot height reservation

### INP (All pages) ✅ STABLE
- **Current**: 185ms (p75), no significant change
</code></pre>

<!-- @TODO: Add example of weekly report email with sparklines and metrics -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Weekly Performance Report Email</p>
  <p class="text-sm text-gray-500 mt-2">Formatted report showing sparklines, metric trends, and actionable recommendations in digestible format</p>
</div>

<h3>Real-world example: E-commerce site baseline</h3>

<table class="w-full my-6 border">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Metric</th>
      <th class="border p-2">Mobile Target</th>
      <th class="border p-2">Desktop Target</th>
      <th class="border p-2">Current (p75)</th>
      <th class="border p-2">Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">LCP - Homepage</td>
      <td class="border p-2">&lt; 2.5s</td>
      <td class="border p-2">&lt; 1.8s</td>
      <td class="border p-2">2.1s / 1.5s</td>
      <td class="border p-2 text-green-600">✓ Good</td>
    </tr>
    <tr>
      <td class="border p-2">LCP - Product Pages</td>
      <td class="border p-2">&lt; 2.5s</td>
      <td class="border p-2">&lt; 1.8s</td>
      <td class="border p-2">3.2s / 2.1s</td>
      <td class="border p-2 text-red-600">✗ Needs work</td>
    </tr>
    <tr>
      <td class="border p-2">CLS - All pages</td>
      <td class="border p-2">&lt; 0.1</td>
      <td class="border p-2">&lt; 0.1</td>
      <td class="border p-2">0.08</td>
      <td class="border p-2 text-green-600">✓ Good</td>
    </tr>
    <tr>
      <td class="border p-2">INP - All pages</td>
      <td class="border p-2">&lt; 200ms</td>
      <td class="border p-2">&lt; 200ms</td>
      <td class="border p-2">245ms</td>
      <td class="border p-2 text-orange-600">⚠ Monitor</td>
    </tr>
  </tbody>
</table>

<p><strong>Quick wins identified:</strong> Product page hero images need compression and preload. INP spike correlates with third-party chat widget - consider lazy loading.</p>',
                ],
                'de' => [
                    'title' => 'Core Web Vitals Playbook: Baselines, Alerts und Quick Wins',
                    'excerpt' => 'Praktische Budgets für LCP, CLS und INP mit weniger Alarmrauschen.',
                    'body' => '<p>Setze pragmatische Budgets: LCP &lt; 2,5s (Mobile) und &lt; 1,8s (Desktop), CLS &lt; 0,1, INP &lt; 200ms. Metriken pro Template/Route und getrennt nach Mobile/Desktop verfolgen, damit Regressionen umsetzbar bleiben.</p><h3>Baselines definieren</h3><ul><li>75. Perzentil statt Durchschnitt; p95 im Blick für Ausreißer.</li><li>Führe Leading Indicators mit: JS-Laufzeit, Transfer-Bytes, Request-Anzahl.</li><li>HTML-Gewicht und Bild-Bytes loggen – sie sagen künftige Probleme oft voraus.</li></ul><h3>Alerts ohne Rauschen</h3><ul><li>Alerts auf Deltas (z. B. +15% LCP WoW) statt auf einzelne Runs.</li><li>Nur alerten, wenn Stichprobe &gt;= 20 Runs, um Flattereffekte zu vermeiden.</li><li>Alerts nach Template gruppieren, damit Teams sofort wissen, wo sie suchen.</li></ul><p>Wöchentliche Reports mit Sparklines und Kurztext: Was änderte sich, wahrscheinliche Ursache, eine konkrete Maßnahme.</p>',
                ],
            ],
        ],
        [
            'slug' => 'fixing-lcp-like-a-pro',
            'date' => '2025-01-06',
            'reading_time' => '8 min',
            'category' => 'LCP',
            'locales' => [
                'en' => [
                    'title' => 'Fixing LCP like a pro: image priority, critical CSS, TTFB',
                    'excerpt' => 'LCP is usually hero images or headlines. Here is how to load them first.',
                    'body' => '<p>Most LCP is a hero image or headline. Aim to deliver it in the first round-trip: prioritize the resource, remove render blockers, and keep TTFB low.</p>

<!-- @TODO: Add before/after screenshot showing LCP improvement on a hero image -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: LCP Before and After Optimization</p>
  <p class="text-sm text-gray-500 mt-2">Side-by-side comparison showing LCP filmstrip: Before (3.4s) vs After (1.8s) with waterfall showing preload effect</p>
</div>

<h3>Image strategy</h3>

<p>Your hero image is often your LCP element. Getting it to render quickly requires careful prioritization and format optimization.</p>

<h4>Preloading hero images</h4>

<p>Preload the exact hero asset with proper responsive image attributes:</p>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: No preload, browser discovers image late --&gt;
&lt;img src="/hero.jpg" alt="Hero" width="1200" height="600"&gt;

&lt;!-- ✅ AFTER: Preload with responsive srcset --&gt;
&lt;link rel="preload" as="image"
  href="/hero-1200.webp"
  imagesrcset="
    /hero-400.webp 400w,
    /hero-800.webp 800w,
    /hero-1200.webp 1200w,
    /hero-1600.webp 1600w"
  imagesizes="100vw"&gt;

&lt;img src="/hero-1200.webp"
  srcset="/hero-400.webp 400w,
          /hero-800.webp 800w,
          /hero-1200.webp 1200w,
          /hero-1600.webp 1600w"
  sizes="100vw"
  alt="Hero"
  width="1200"
  height="600"
  fetchpriority="high"&gt;
</code></pre>

<!-- @TODO: Add diagram showing network waterfall with and without preload -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Network Waterfall - Preload Impact</p>
  <p class="text-sm text-gray-500 mt-2">Network waterfall showing how preload moves hero image fetch earlier in the cascade, reducing LCP by 800ms</p>
</div>

<h4>Fetch priority hints</h4>

<p>Add <code>fetchpriority="high"</code> to the LCP image and avoid <code>loading="lazy"</code> on it:</p>

<pre><code class="language-html">&lt;!-- Hero image: high priority, eager loading --&gt;
&lt;img src="/hero.webp"
  fetchpriority="high"
  loading="eager"
  alt="Hero image"&gt;

&lt;!-- Below-fold images: low priority, lazy loading --&gt;
&lt;img src="/product-1.webp"
  fetchpriority="low"
  loading="lazy"
  alt="Product thumbnail"&gt;
</code></pre>

<h4>Modern formats and sizing</h4>

<p>Serve modern formats (AVIF/WebP) with fallbacks and avoid 2x oversizing:</p>

<pre><code class="language-html">&lt;picture&gt;
  &lt;!-- AVIF for modern browsers (best compression) --&gt;
  &lt;source type="image/avif"
    srcset="/hero-400.avif 400w,
            /hero-800.avif 800w,
            /hero-1200.avif 1200w"
    sizes="100vw"&gt;

  &lt;!-- WebP fallback --&gt;
  &lt;source type="image/webp"
    srcset="/hero-400.webp 400w,
            /hero-800.webp 800w,
            /hero-1200.webp 1200w"
    sizes="100vw"&gt;

  &lt;!-- JPEG fallback for legacy browsers --&gt;
  &lt;img src="/hero-1200.jpg"
    srcset="/hero-400.jpg 400w,
            /hero-800.jpg 800w,
            /hero-1200.jpg 1200w"
    sizes="100vw"
    alt="Hero"
    width="1200"
    height="600"
    fetchpriority="high"&gt;
&lt;/picture&gt;
</code></pre>

<!-- @TODO: Add comparison chart showing file sizes for JPEG vs WebP vs AVIF -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Format Comparison Chart</p>
  <p class="text-sm text-gray-500 mt-2">Bar chart comparing file sizes: JPEG (450KB) vs WebP (180KB) vs AVIF (120KB) for same visual quality</p>
</div>

<h3>CSS and JS optimization</h3>

<p>Render-blocking CSS and JavaScript delay your LCP element from painting. Separate critical from non-critical resources.</p>

<h4>Critical CSS inlining</h4>

<p>Inline critical CSS for header/hero; defer the rest:</p>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: All CSS blocks rendering --&gt;
&lt;link rel="stylesheet" href="/styles.css"&gt;

&lt;!-- ✅ AFTER: Inline critical, defer rest --&gt;
&lt;style&gt;
  /* Critical CSS for above-the-fold content */
  .header { background: #fff; height: 60px; }
  .hero { min-height: 600px; background: #f0f0f0; }
  .hero-title { font-size: 48px; line-height: 1.2; }
&lt;/style&gt;

&lt;!-- Defer non-critical CSS --&gt;
&lt;link rel="preload" href="/styles.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"&gt;
&lt;noscript&gt;&lt;link rel="stylesheet" href="/styles.css"&gt;&lt;/noscript&gt;
</code></pre>

<h4>JavaScript deferral</h4>

<p>Defer non-critical JS and remove unused polyfills:</p>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: Blocking script in &lt;head&gt; --&gt;
&lt;script src="/analytics.js"&gt;&lt;/script&gt;
&lt;script src="/tag-manager.js"&gt;&lt;/script&gt;

&lt;!-- ✅ AFTER: Defer all non-critical scripts --&gt;
&lt;script defer src="/analytics.js"&gt;&lt;/script&gt;
&lt;script defer src="/tag-manager.js"&gt;&lt;/script&gt;

&lt;!-- Even better: Lazy load after LCP --&gt;
&lt;script&gt;
  // Load analytics after LCP fires
  addEventListener(\'load\', () =&gt; {
    setTimeout(() =&gt; {
      const script = document.createElement(\'script\');
      script.src = \'/analytics.js\';
      document.head.appendChild(script);
    }, 1000);
  });
&lt;/script&gt;
</code></pre>

<!-- @TODO: Add screenshot showing DevTools Coverage panel highlighting unused CSS/JS -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Chrome DevTools Coverage Panel</p>
  <p class="text-sm text-gray-500 mt-2">Screenshot showing 65% unused CSS highlighted in red, demonstrating opportunity for code splitting</p>
</div>

<h3>Server and TTFB optimization</h3>

<p>Fast TTFB means the browser can start downloading your hero image sooner. Optimize at the CDN and origin level.</p>

<h4>HTML caching strategy</h4>

<p>Cache HTML at the CDN for anonymous traffic; keep HTML under ~14KB compressed:</p>

<pre><code class="language-nginx"># Nginx CDN configuration
location / {
    # Cache HTML for anonymous users
    proxy_cache html_cache;
    proxy_cache_key "$scheme$host$request_uri$cookie_logged_in";
    proxy_cache_valid 200 5m;

    # Bypass cache for logged-in users
    proxy_cache_bypass $cookie_logged_in;
    proxy_no_cache $cookie_logged_in;

    # Add cache status header for debugging
    add_header X-Cache-Status $upstream_cache_status;

    # Enable Brotli compression
    brotli on;
    brotli_comp_level 6;
    brotli_types text/html text/css application/javascript;
}
</code></pre>

<h4>HTTP/3 and Early Hints</h4>

<p>Enable HTTP/3 and send Early Hints to kick off hero fetch sooner:</p>

<pre><code class="language-php">&lt;?php
// Send Early Hints (103) before generating HTML
header(\'Link: &lt;/hero-800.webp&gt;; rel=preload; as=image\', false, 103);
header(\'Link: &lt;/critical.css&gt;; rel=preload; as=style\', false, 103);

// Continue with normal response
http_response_code(200);
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html&gt;
...
</code></pre>

<!-- @TODO: Add diagram showing Early Hints timeline vs normal loading -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Early Hints Timeline Comparison</p>
  <p class="text-sm text-gray-500 mt-2">Timeline diagram showing how Early Hints (103) allows browser to fetch resources during server think time, saving 200ms</p>
</div>

<h4>Origin optimization</h4>

<p>Warm origins or functions and optimize serverless cold starts:</p>

<pre><code class="language-javascript">// Vercel serverless function with connection pooling
import { Pool } from \'pg\';

// Reuse connection pool across invocations
const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  max: 1,  // Serverless: keep pool small
});

export default async function handler(req, res) {
  // Fast path: serve from edge cache if possible
  res.setHeader(\'Cache-Control\', \'s-maxage=300, stale-while-revalidate=600\');

  const data = await pool.query(\'SELECT * FROM pages WHERE slug = $1\', [req.query.slug]);

  return res.json(data.rows[0]);
}
</code></pre>

<h3>Real-world case study</h3>

<table class="w-full my-6 border">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Optimization</th>
      <th class="border p-2">Before</th>
      <th class="border p-2">After</th>
      <th class="border p-2">Impact</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">Hero image format</td>
      <td class="border p-2">JPEG (450KB)</td>
      <td class="border p-2">AVIF (120KB)</td>
      <td class="border p-2">-730ms LCP</td>
    </tr>
    <tr>
      <td class="border p-2">Preload hero</td>
      <td class="border p-2">No preload</td>
      <td class="border p-2">With preload</td>
      <td class="border p-2">-400ms LCP</td>
    </tr>
    <tr>
      <td class="border p-2">Critical CSS</td>
      <td class="border p-2">External CSS (245KB)</td>
      <td class="border p-2">Inline (8KB)</td>
      <td class="border p-2">-300ms LCP</td>
    </tr>
    <tr>
      <td class="border p-2">HTML cache</td>
      <td class="border p-2">No cache (420ms TTFB)</td>
      <td class="border p-2">CDN cached (45ms TTFB)</td>
      <td class="border p-2">-375ms LCP</td>
    </tr>
    <tr class="font-bold bg-green-50">
      <td class="border p-2">Total</td>
      <td class="border p-2">3.4s LCP</td>
      <td class="border p-2">1.6s LCP</td>
      <td class="border p-2">-1.8s (53% improvement)</td>
    </tr>
  </tbody>
</table>

<!-- @TODO: Add PageSpeed Insights screenshot showing before/after scores -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: PageSpeed Insights Before/After</p>
  <p class="text-sm text-gray-500 mt-2">Side-by-side PSI reports showing performance score improvement from 45 to 92, with LCP highlighted</p>
</div>

<p>Measure LCP separately for mobile and desktop, and validate in real-user monitoring to catch device-specific issues. Mobile often shows larger improvements due to network constraints.</p>',
                ],
                'de' => [
                    'title' => 'LCP wie ein Profi fixen: Bild-Priorität, Critical CSS, TTFB',
                    'excerpt' => 'LCP ist meist Hero-Bild oder Headline. So lädst du sie zuerst.',
                    'body' => '<p>Meist ist LCP das Hero-Bild oder eine Headline. Ziel: Im ersten Roundtrip laden, Blocker entfernen und TTFB niedrig halten.</p><h3>Bild-Strategie</h3><ul><li>Das exakte Hero-Asset vorladen: <code>&lt;link rel=\"preload\" as=\"image\" imagesrcset=\"...\" imagesizes=\"...\"&gt;</code>.</li><li><code>fetchpriority=\"high\"</code> am LCP-Bild setzen, kein <code>loading=\"lazy\"</code> dort.</li><li>Moderne Formate (AVIF/WebP) und responsive Größen nutzen; kein 2x Oversizing.</li></ul><h3>CSS und JS</h3><ul><li>Critical CSS für Header/Hero inline, Rest via <code>media=\"print\" onload=\"this.media=\'all\'\"</code> oder <code>rel=\"preload\" as=\"style\"</code>.</li><li>Nicht-kritisches JS defern; unnötige Polyfills und Tag-Manager-Ballast entfernen.</li></ul><h3>Server und TTFB</h3><ul><li>HTML für anonyme Nutzer am CDN cachen; HTML komprimiert &lt; ~14KB halten.</li><li>Origins/Functions vorwärmen; HTTP/2 oder HTTP/3 und TLS 1.3 nutzen.</li><li>Early Hints senden, wenn möglich, um den Hero früher anzustoßen.</li></ul><p>LCP getrennt für Mobile/Desktop messen und in RUM prüfen, um gerätespezifische Probleme zu finden.</p>',
                ],
            ],
        ],
        [
            'slug' => 'stop-layout-shifts',
            'date' => '2025-01-07',
            'reading_time' => '7 min',
            'category' => 'CLS',
            'locales' => [
                'en' => [
                    'title' => 'Stop layout shifts: media aspect ratios and font swaps',
                    'excerpt' => 'Most CLS comes from images, embeds, and late-loading fonts.',
                    'body' => '<p>CLS is almost always unreserved space: media without dimensions, delayed fonts, and elements injected above existing content.</p>

<!-- @TODO: Add animated GIF showing common CLS issues (image loading, font swap, dynamic content) -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Common CLS Culprits Animation</p>
  <p class="text-sm text-gray-500 mt-2">Animated recording showing page loading with visible layout shifts from images, fonts, and banner insertion</p>
</div>

<h3>Media and embeds</h3>

<p>Images and embeds are the #1 cause of layout shift. Reserve space before they load to prevent reflow.</p>

<h4>Images without dimensions</h4>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: No dimensions, causes shift --&gt;
&lt;img src="/product.jpg" alt="Product"&gt;

&lt;!-- ✅ AFTER: Width and height reserve space --&gt;
&lt;img src="/product.jpg"
  alt="Product"
  width="800"
  height="600"&gt;

&lt;!-- Even better: Modern aspect-ratio CSS --&gt;
&lt;div style="aspect-ratio: 16/9; width: 100%;"&gt;
  &lt;img src="/product.jpg"
    alt="Product"
    style="width: 100%; height: 100%; object-fit: cover;"&gt;
&lt;/div&gt;
</code></pre>

<!-- @TODO: Add before/after video showing image loading with and without dimensions -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Image Loading Shift Comparison</p>
  <p class="text-sm text-gray-500 mt-2">Side-by-side video: left shows text jumping when image loads, right shows stable layout with reserved space</p>
</div>

<h4>Responsive images with aspect ratio</h4>

<pre><code class="language-html">&lt;!-- Responsive image container with aspect ratio --&gt;
&lt;style&gt;
  .image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #f0f0f0; /* Placeholder color */
  }

  .image-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
&lt;/style&gt;

&lt;div class="image-wrapper"&gt;
  &lt;img src="/hero.jpg" alt="Hero" loading="lazy"&gt;
&lt;/div&gt;
</code></pre>

<h4>Ad slots and embeds</h4>

<p>Reserve ad slots with CSS aspect-ratio boxes; do not collapse them while loading:</p>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: Ad slot collapses, then expands --&gt;
&lt;div id="ad-slot"&gt;&lt;/div&gt;
&lt;script&gt;
  loadAd(\'ad-slot\'); // Causes layout shift when ad appears
&lt;/script&gt;

&lt;!-- ✅ AFTER: Reserved space prevents shift --&gt;
&lt;div class="ad-container" style="min-height: 250px; width: 300px; background: #f5f5f5;"&gt;
  &lt;div id="ad-slot"&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;script&gt;
  loadAd(\'ad-slot\');
&lt;/script&gt;

&lt;!-- Even better: Exact aspect ratio --&gt;
&lt;style&gt;
  .ad-container {
    aspect-ratio: 300 / 250; /* Standard medium rectangle */
    width: 300px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .ad-container:empty::before {
    content: "Advertisement";
    color: #999;
    font-size: 12px;
  }
&lt;/style&gt;
</code></pre>

<!-- @TODO: Add screenshot showing DevTools Layout Shift regions highlighted -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Chrome DevTools Layout Shift Visualization</p>
  <p class="text-sm text-gray-500 mt-2">Screenshot showing blue overlay on shifted regions in Chrome DevTools Performance panel</p>
</div>

<h4>YouTube and iframe embeds</h4>

<pre><code class="language-html">&lt;!-- YouTube embed with aspect ratio --&gt;
&lt;style&gt;
  .video-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #000;
  }

  .video-wrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
  }
&lt;/style&gt;

&lt;div class="video-wrapper"&gt;
  &lt;iframe
    src="https://www.youtube.com/embed/VIDEO_ID"
    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen&gt;
  &lt;/iframe&gt;
&lt;/div&gt;
</code></pre>

<h3>Font loading and FOUT/FOIT</h3>

<p>Web fonts can cause significant layout shift if the fallback font has different metrics. Use font-display and size adjustments.</p>

<h4>Font preloading with size-adjust</h4>

<pre><code class="language-html">&lt;!-- Preload critical fonts --&gt;
&lt;link rel="preload"
  href="/fonts/inter-var.woff2"
  as="font"
  type="font/woff2"
  crossorigin&gt;

&lt;style&gt;
  /* Define web font */
  @font-face {
    font-family: \'Inter\';
    src: url(\'/fonts/inter-var.woff2\') format(\'woff2\');
    font-display: swap;
  }

  /* Adjust fallback to match web font metrics */
  @font-face {
    font-family: \'Arial Adjusted\';
    src: local(\'Arial\');
    size-adjust: 106%; /* Match Inter\'s metrics */
    ascent-override: 90%;
    descent-override: 22%;
    line-gap-override: 0%;
  }

  body {
    font-family: \'Inter\', \'Arial Adjusted\', sans-serif;
  }
&lt;/style&gt;
</code></pre>

<!-- @TODO: Add comparison showing font swap with and without size-adjust -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Font Swap Layout Shift Comparison</p>
  <p class="text-sm text-gray-500 mt-2">Before/after showing text reflow during font swap: without size-adjust (visible jump) vs with size-adjust (seamless)</p>
</div>

<h4>Font-display strategies</h4>

<pre><code class="language-css">/* Option 1: swap - shows fallback immediately, swaps when ready */
@font-face {
  font-family: \'CustomFont\';
  src: url(\'/fonts/custom.woff2\');
  font-display: swap; /* Best for most cases */
}

/* Option 2: optional - only use web font if already cached */
@font-face {
  font-family: \'CustomFont\';
  src: url(\'/fonts/custom.woff2\');
  font-display: optional; /* Zero layout shift, but may not load */
}

/* Option 3: fallback - short block period, then swap */
@font-face {
  font-family: \'CustomFont\';
  src: url(\'/fonts/custom.woff2\');
  font-display: fallback; /* Compromise between swap and optional */
}
</code></pre>

<h3>Dynamic UI and injected content</h3>

<p>Content injected above existing elements is a major CLS culprit. Always reserve space or use non-shifting patterns.</p>

<h4>Cookie banners and consent dialogs</h4>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: Banner pushes content down --&gt;
&lt;div class="cookie-banner"&gt;
  We use cookies...
&lt;/div&gt;
&lt;main&gt;
  Page content...
&lt;/main&gt;

&lt;!-- ✅ AFTER: Fixed position, no layout shift --&gt;
&lt;div class="cookie-banner" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;"&gt;
  We use cookies...
&lt;/div&gt;
&lt;main&gt;
  Page content...
&lt;/main&gt;

&lt;!-- Alternative: Reserved space at top --&gt;
&lt;div style="min-height: 60px;"&gt;
  &lt;div class="cookie-banner" style="position: absolute; top: 0; width: 100%;"&gt;
    We use cookies...
  &lt;/div&gt;
&lt;/div&gt;
</code></pre>

<h4>Loading skeletons for dynamic content</h4>

<pre><code class="language-html">&lt;!-- Show skeleton while loading to reserve space --&gt;
&lt;style&gt;
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }
&lt;/style&gt;

&lt;!-- Skeleton structure matches real content --&gt;
&lt;div class="product-card"&gt;
  &lt;div class="skeleton" style="aspect-ratio: 1; width: 100%; height: 200px; margin-bottom: 12px;"&gt;&lt;/div&gt;
  &lt;div class="skeleton" style="height: 20px; width: 80%; margin-bottom: 8px;"&gt;&lt;/div&gt;
  &lt;div class="skeleton" style="height: 16px; width: 60%;"&gt;&lt;/div&gt;
&lt;/div&gt;
</code></pre>

<!-- @TODO: Add example of skeleton loading animation -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Skeleton Loading Example</p>
  <p class="text-sm text-gray-500 mt-2">Animated demo showing skeleton placeholders gradually replaced with real content without layout shift</p>
</div>

<h4>Animating height changes</h4>

<p>Animate height changes with transforms or max-height, not direct height manipulation:</p>

<pre><code class="language-css">/* ❌ BEFORE: Animating height causes layout thrash */
.accordion-content {
  height: 0;
  transition: height 0.3s;
}

.accordion.open .accordion-content {
  height: auto; /* Cannot animate to auto */
}

/* ✅ AFTER: Use max-height or transform */
.accordion-content {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.accordion.open .accordion-content {
  max-height: 500px; /* Generous max */
}

/* Alternative: Transform-based (zero layout shift) */
.accordion-content {
  transform: scaleY(0);
  transform-origin: top;
  transition: transform 0.3s ease;
}

.accordion.open .accordion-content {
  transform: scaleY(1);
}
</code></pre>

<h3>Debugging CLS in the wild</h3>

<pre><code class="language-javascript">// Track CLS with detailed element attribution
import {onCLS} from \'web-vitals\';

onCLS((metric) =&gt; {
  // Log detailed shift information
  metric.entries.forEach((entry) =&gt; {
    console.log(\'Layout shift:\', {
      score: entry.value,
      sources: entry.sources?.map(source =&gt; ({
        node: source.node,
        previousRect: source.previousRect,
        currentRect: source.currentRect
      }))
    });
  });

  // Send to analytics
  fetch(\'/api/vitals\', {
    method: \'POST\',
    body: JSON.stringify({
      metric: \'CLS\',
      value: metric.value,
      id: metric.id,
      elements: metric.entries.map(e =&gt; e.sources?.[0]?.node?.tagName)
    })
  });
});
</code></pre>

<!-- @TODO: Add Web Vitals extension screenshot showing CLS breakdown -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Web Vitals Chrome Extension CLS Report</p>
  <p class="text-sm text-gray-500 mt-2">Screenshot showing Web Vitals extension with CLS score and list of contributing elements</p>
</div>

<h3>Real-world CLS fixes</h3>

<table class="w-full my-6 border">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Issue</th>
      <th class="border p-2">Element</th>
      <th class="border p-2">Fix</th>
      <th class="border p-2">CLS Reduction</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">No image dimensions</td>
      <td class="border p-2">Hero image</td>
      <td class="border p-2">Added width/height attrs</td>
      <td class="border p-2">-0.08</td>
    </tr>
    <tr>
      <td class="border p-2">Font swap</td>
      <td class="border p-2">Body text</td>
      <td class="border p-2">Added size-adjust to fallback</td>
      <td class="border p-2">-0.04</td>
    </tr>
    <tr>
      <td class="border p-2">Cookie banner</td>
      <td class="border p-2">Top banner</td>
      <td class="border p-2">Changed to fixed bottom position</td>
      <td class="border p-2">-0.12</td>
    </tr>
    <tr>
      <td class="border p-2">Ad slot</td>
      <td class="border p-2">Sidebar ad</td>
      <td class="border p-2">Reserved 300x250 space</td>
      <td class="border p-2">-0.05</td>
    </tr>
    <tr class="font-bold bg-green-50">
      <td class="border p-2" colspan="3">Total CLS improvement</td>
      <td class="border p-2">0.29 → 0.00 ✓</td>
    </tr>
  </tbody>
</table>

<p>Monitor CLS per template and device class. Fix the biggest cumulative contributors first; a few offenders often account for &gt;80% of shifts. Use Chrome DevTools Performance panel to identify shift sources in real-time.</p>',
                ],
                'de' => [
                    'title' => 'Layout-Shifts stoppen: Media-Aspect-Ratios und Font-Swaps',
                    'excerpt' => 'CLS entsteht meist durch Bilder, Embeds und spät ladende Fonts.',
                    'body' => '<p>CLS ist fast immer fehlender Platz: Medien ohne Dimensionen, späte Fonts und Elemente, die über Content geschoben werden.</p><h3>Medien und Embeds</h3><ul><li><code>width</code>/<code>height</code> oder <code>aspect-ratio</code> für Bilder, Videos und Embeds setzen.</li><li>Ad-Slots mit CSS-Aspect-Ratio reservieren; nicht kollabieren lassen.</li><li><code>object-fit: cover</code> mit fixen Containern, um Reflows zu vermeiden.</li></ul><h3>Fonts</h3><ul><li>First-Paint-Fonts preladen; <code>font-display: swap</code> mit metrisch passenden Fallbacks (<code>size-adjust</code> hilft).</li><li>Weniger Font-Varianten; initiales CSS klein halten für weniger FOUT/FOIT.</li></ul><h3>Dynamische UI</h3><ul><li>Keine Consent-Banner/Promos über bestehendem Content einfügen; in reservierten Bereichen oder als Bottom-Sheet platzieren.</li><li>Höhenänderungen mit Transforms animieren, nicht mit Layout-Thrash.</li></ul><p>CLS pro Template und Gerätetyp überwachen. Die größten kumulativen Verursacher zuerst fixen; wenige Elemente machen oft &gt;80% aus.</p>',
                ],
            ],
        ],
        [
            'slug' => 'ttfb-and-server-tuning',
            'date' => '2025-01-08',
            'reading_time' => '8 min',
            'category' => 'TTFB',
            'locales' => [
                'en' => [
                    'title' => 'TTFB and server tuning: caching, TLS, and cold starts',
                    'excerpt' => 'TTFB issues often come from cache misses and TLS bloat.',
                    'body' => '<p>High TTFB usually means cache misses or slow server work. Fix it at the edges first, then the origin.</p>

<!-- @TODO: Add waterfall diagram showing TTFB breakdown (DNS, TCP, TLS, Server) -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: TTFB Waterfall Breakdown</p>
  <p class="text-sm text-gray-500 mt-2">Network timing diagram showing DNS (20ms), TCP (40ms), TLS (80ms), and Server Time (260ms) components of total 400ms TTFB</p>
</div>

<h3>Cache keys and CDN configuration</h3>

<p>Your CDN is the first line of defense against high TTFB. Configure cache keys carefully to maximize hit rates.</p>

<h4>Smart cache key design</h4>

<pre><code class="language-javascript">// Cloudflare Worker: Custom cache key
export default {
  async fetch(request, env) {
    const url = new URL(request.url);

    // Build cache key with only essential variations
    const cacheKey = new URL(request.url);

    // Normalize URL
    cacheKey.pathname = url.pathname.toLowerCase().replace(/\/$/, \'\');

    // Vary on locale and device type only
    const acceptLanguage = request.headers.get(\'Accept-Language\') || \'en\';
    const locale = acceptLanguage.split(\',\')[0].split(\'-\')[0];
    const isMobile = /mobile/i.test(request.headers.get(\'User-Agent\') || \'\');

    cacheKey.searchParams.set(\'_locale\', locale);
    cacheKey.searchParams.set(\'_device\', isMobile ? \'m\' : \'d\');

    // Ignore auth cookies for public pages
    if (!url.pathname.startsWith(\'/account\')) {
      const cache = caches.default;
      let response = await cache.match(cacheKey);

      if (!response) {
        response = await fetch(request);
        // Cache for 5 minutes with 1 hour stale-while-revalidate
        const headers = new Headers(response.headers);
        headers.set(\'Cache-Control\', \'public, s-maxage=300, stale-while-revalidate=3600\');
        response = new Response(response.body, {
          status: response.status,
          headers
        });
        await cache.put(cacheKey, response.clone());
      }

      return response;
    }

    return fetch(request);
  }
};
</code></pre>

<!-- @TODO: Add chart showing cache hit rate improvement after cache key optimization -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Cache Hit Rate Improvement Graph</p>
  <p class="text-sm text-gray-500 mt-2">Line graph showing cache hit rate jumping from 45% to 87% after cache key normalization, with TTFB dropping from 380ms to 65ms</p>
</div>

<h4>Stale-while-revalidate pattern</h4>

<p>Use <code>stale-while-revalidate</code> to mask cold starts and origin slowness:</p>

<pre><code class="language-nginx"># Nginx configuration for SWR
location / {
    proxy_pass http://origin;

    # Cache for 5 minutes, serve stale for 1 hour while revalidating
    proxy_cache_valid 200 5m;
    proxy_cache_use_stale error timeout updating http_500 http_502 http_503;
    proxy_cache_background_update on;

    # Add headers for debugging
    add_header X-Cache-Status $upstream_cache_status;
    add_header Cache-Control "public, max-age=300, stale-while-revalidate=3600";
}
</code></pre>

<pre><code class="language-javascript">// Next.js API route with SWR
export async function GET(request) {
  const data = await fetchExpensiveData();

  return Response.json(data, {
    headers: {
      \'Cache-Control\': \'public, s-maxage=300, stale-while-revalidate=3600\'
    }
  });
}
</code></pre>

<h4>HTTP/2 and HTTP/3 multiplexing</h4>

<pre><code class="language-nginx"># Enable HTTP/2 and HTTP/3 (QUIC)
server {
    listen 443 ssl http2;
    listen 443 quic reuseport;  # HTTP/3

    ssl_protocols TLSv1.3;
    ssl_early_data on;  # Enable 0-RTT

    # Advertise HTTP/3 support
    add_header Alt-Svc \'h3=":443"; ma=86400\';

    # Connection reuse
    keepalive_timeout 65;
    keepalive_requests 100;
}
</code></pre>

<!-- @TODO: Add diagram comparing HTTP/1.1, HTTP/2, and HTTP/3 request waterfalls -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: HTTP Protocol Comparison Waterfall</p>
  <p class="text-sm text-gray-500 mt-2">Three waterfalls showing same page load: HTTP/1.1 (serial), HTTP/2 (multiplexed), HTTP/3 (QUIC, fastest)</p>
</div>

<h3>Origin performance optimization</h3>

<p>When cache misses occur, your origin must respond quickly. Keep server-side rendering lean and efficient.</p>

<h4>Minimal SSR with streaming</h4>

<pre><code class="language-javascript">// React Server Components with streaming
import { Suspense } from \'react\';

export default function Page() {
  return (
    &lt;&gt;
      {/* Send shell immediately */}
      &lt;header&gt;...&lt;/header&gt;

      {/* Stream in heavy data later */}
      &lt;Suspense fallback={&lt;Skeleton /&gt;}&gt;
        &lt;ExpensiveComponent /&gt;
      &lt;/Suspense&gt;
    &lt;/&gt;
  );
}

// Server streams HTML as components resolve
// TTFB stays low because shell sends immediately
</code></pre>

<h4>Database connection pooling</h4>

<pre><code class="language-javascript">// ❌ BEFORE: New connection per request
import { Client } from \'pg\';

export async function handler(req, res) {
  const client = new Client({connectionString: process.env.DATABASE_URL});
  await client.connect();  // Slow: TLS handshake each time
  const result = await client.query(\'SELECT * FROM pages\');
  await client.end();
  return res.json(result.rows);
}

// ✅ AFTER: Connection pooling
import { Pool } from \'pg\';

const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  max: 10,
  idleTimeoutMillis: 30000,
  connectionTimeoutMillis: 2000,
});

export async function handler(req, res) {
  const result = await pool.query(\'SELECT * FROM pages\');  // Reuses connection
  return res.json(result.rows);
}
</code></pre>

<h4>Query optimization and caching</h4>

<pre><code class="language-javascript">// Add response-level caching for slow queries
import { unstable_cache } from \'next/cache\';

const getCachedPosts = unstable_cache(
  async () =&gt; {
    return await db.query(`
      SELECT p.*, COUNT(c.id) as comment_count
      FROM posts p
      LEFT JOIN comments c ON c.post_id = p.id
      GROUP BY p.id
      ORDER BY p.created_at DESC
      LIMIT 10
    `);
  },
  [\'recent-posts\'],
  {
    revalidate: 60, // Cache for 60 seconds
    tags: [\'posts\']
  }
);
</code></pre>

<!-- @TODO: Add screenshot showing database query profiling with slow query highlighted -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Database Query Profiling</p>
  <p class="text-sm text-gray-500 mt-2">Screenshot of query analyzer showing 850ms query highlighted, with suggestion to add index on created_at column</p>
</div>

<h4>HTML compression</h4>

<pre><code class="language-javascript">// Compress HTML with Brotli for best results
import { brotliCompress } from \'zlib\';
import { promisify } from \'util\';

const compress = promisify(brotliCompress);

export async function GET(request) {
  const html = await renderPage();

  const acceptEncoding = request.headers.get(\'Accept-Encoding\') || \'\';

  if (acceptEncoding.includes(\'br\')) {
    const compressed = await compress(html, {
      params: {
        [constants.BROTLI_PARAM_QUALITY]: 6  // Good balance of speed/size
      }
    });

    return new Response(compressed, {
      headers: {
        \'Content-Type\': \'text/html\',
        \'Content-Encoding\': \'br\',
        \'Cache-Control\': \'public, s-maxage=300\'
      }
    });
  }

  return new Response(html);
}
</code></pre>

<table class="w-full my-6 border text-sm">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Compression</th>
      <th class="border p-2">Original</th>
      <th class="border p-2">Gzip</th>
      <th class="border p-2">Brotli</th>
      <th class="border p-2">Savings</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">HTML (45KB)</td>
      <td class="border p-2">45KB</td>
      <td class="border p-2">12.3KB</td>
      <td class="border p-2">9.8KB</td>
      <td class="border p-2">78% smaller</td>
    </tr>
  </tbody>
</table>

<h3>TLS and platform optimization</h3>

<h4>TLS 1.3 and 0-RTT</h4>

<pre><code class="language-nginx"># Optimize TLS handshake
ssl_protocols TLSv1.3;
ssl_early_data on;  # 0-RTT resumption

# Modern cipher suites only
ssl_ciphers TLS13-AES-256-GCM-SHA384:TLS13-CHACHA20-POLY1305-SHA256;
ssl_prefer_server_ciphers off;

# OCSP stapling
ssl_stapling on;
ssl_stapling_verify on;
ssl_trusted_certificate /etc/ssl/certs/ca-bundle.crt;

# Session resumption
ssl_session_cache shared:SSL:50m;
ssl_session_timeout 1d;
ssl_session_tickets on;
</code></pre>

<h4>Serverless cold start mitigation</h4>

<pre><code class="language-javascript">// Vercel: Keep functions warm with scheduled pings
export const config = {
  maxDuration: 10,
};

// Warm critical functions every 5 minutes
export async function GET(request) {
  const url = new URL(request.url);

  // Health check endpoint
  if (url.searchParams.get(\'warmup\') === \'true\') {
    return Response.json({ status: \'warm\' });
  }

  // Regular request logic
  const data = await fetchData();
  return Response.json(data);
}

// GitHub Actions or cron job to keep warm:
// */5 * * * * curl https://yourdomain.com/api/endpoint?warmup=true
</code></pre>

<pre><code class="language-yaml"># AWS Lambda: Provisioned concurrency to eliminate cold starts
Resources:
  MyFunction:
    Type: AWS::Lambda::Function
    Properties:
      Handler: index.handler
      Runtime: nodejs20.x
      ProvisionedConcurrencyConfig:
        ProvisionedConcurrentExecutions: 2  # Keep 2 instances warm
</code></pre>

<!-- @TODO: Add graph showing cold start vs warm start response times over 24 hours -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Cold Start Impact Graph</p>
  <p class="text-sm text-gray-500 mt-2">Timeline showing TTFB spikes (1200ms) at cold starts vs consistent warm responses (80ms), with warmup pings marked</p>
</div>

<h3>Geographic distribution</h3>

<pre><code class="language-javascript">// Cloudflare Workers: Run at the edge closest to users
export default {
  async fetch(request, env) {
    const cf = request.cf;

    // Log where request came from
    console.log(`Request from ${cf?.city}, ${cf?.country}`);

    // Serve from nearest edge location
    // Average TTFB: 20-50ms globally
    return new Response(\'Hello from the edge!\');
  }
};
</code></pre>

<!-- @TODO: Add world map showing CDN edge locations and response times -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Global CDN Performance Map</p>
  <p class="text-sm text-gray-500 mt-2">World map with edge location dots showing TTFB from each region: US East (45ms), Europe (52ms), Asia (67ms), etc.</p>
</div>

<h3>Real-world TTFB improvements</h3>

<table class="w-full my-6 border">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Optimization</th>
      <th class="border p-2">Before</th>
      <th class="border p-2">After</th>
      <th class="border p-2">Impact</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">CDN cache keys</td>
      <td class="border p-2">42% hit rate</td>
      <td class="border p-2">89% hit rate</td>
      <td class="border p-2">-280ms avg TTFB</td>
    </tr>
    <tr>
      <td class="border p-2">DB connection pooling</td>
      <td class="border p-2">120ms connect</td>
      <td class="border p-2">2ms reuse</td>
      <td class="border p-2">-118ms per query</td>
    </tr>
    <tr>
      <td class="border p-2">TLS 1.3 + 0-RTT</td>
      <td class="border p-2">180ms handshake</td>
      <td class="border p-2">0ms (resumed)</td>
      <td class="border p-2">-180ms repeat visits</td>
    </tr>
    <tr>
      <td class="border p-2">Brotli compression</td>
      <td class="border p-2">45KB HTML</td>
      <td class="border p-2">9.8KB HTML</td>
      <td class="border p-2">-140ms download (3G)</td>
    </tr>
    <tr>
      <td class="border p-2">Lambda warmup</td>
      <td class="border p-2">1200ms cold start</td>
      <td class="border p-2">80ms warm</td>
      <td class="border p-2">-1120ms</td>
    </tr>
    <tr class="font-bold bg-green-50">
      <td class="border p-2" colspan="3">Total TTFB improvement (p75)</td>
      <td class="border p-2">620ms → 85ms</td>
    </tr>
  </tbody>
</table>

<p>Track TTFB separately by geography and device. A CDN close to users plus slim HTML is the fastest win. Focus on cache hit rate first, then origin optimization, then TLS/connection improvements.</p>',
                ],
                'de' => [
                    'title' => 'TTFB und Server-Tuning: Caching, TLS und Cold Starts',
                    'excerpt' => 'TTFB-Probleme entstehen oft durch Cache-Misses und TLS-Overhead.',
                    'body' => '<p>Hoher TTFB heißt meist Cache-Miss oder langsame Origin-Arbeit. Korrigiere zuerst am Rand, dann am Ursprung.</p><h3>Cache-Keys und CDN</h3><ul><li>HTML für anonyme Nutzer cachen; nur auf nötige Cookies variieren.</li><li><code>stale-while-revalidate</code> gegen Cold Starts, <code>stale-if-error</code> für Resilienz.</li><li>HTTP/2 oder HTTP/3 nutzen; Verbindungen mit Keep-Alive warm halten.</li></ul><h3>Origin-Performance</h3><ul><li>SSR schlank halten: minimale DB-Calls, keine blockierenden Third-Party-APIs vor dem First Paint.</li><li>DB-Connections poolen; langsame Queries profilieren; Response-Caching nutzen, wo möglich.</li><li>HTML mit Brotli komprimieren; Markup knapp halten.</li></ul><h3>TLS und Plattform</h3><ul><li>TLS 1.3, OCSP Stapling und moderne Cipher Suites aktivieren.</li><li>Functions/Container per Schedule vorwärmen; Scale-to-zero vorsichtig einsetzen.</li></ul><p>TTFB getrennt nach Region und Gerät verfolgen. CDN nahe beim Nutzer + schlankes HTML bringt den schnellsten Effekt.</p>',
                ],
            ],
        ],
        [
            'slug' => 'render-blocking-assets',
            'date' => '2025-01-09',
            'reading_time' => '9 min',
            'category' => 'Render-blocking',
            'locales' => [
                'en' => [
                    'title' => 'Render-blocking assets: CSS splitting and script strategy',
                    'excerpt' => 'Targeted CSS splitting and defer/async can remove critical blocking.',
                    'body' => '<p>Blocking assets delay first paint. Split CSS by criticality and load scripts with intent.</p>

<!-- @TODO: Add waterfall showing render-blocking resources delaying First Paint -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Render-Blocking Resources Waterfall</p>
  <p class="text-sm text-gray-500 mt-2">Network waterfall showing CSS and JS files blocking First Contentful Paint (FCP) at 2.1s</p>
</div>

<h3>Critical CSS extraction and inlining</h3>

<pre><code class="language-javascript">// Extract critical CSS with Critical library
import { generate } from \'critical\';

const { css, html } = await generate({
  base: \'dist/\',
  src: \'index.html\',
  target: {
    css: \'critical.css\',
    html: \'index-critical.html\'
  },
  width: 1300,
  height: 900,
  inline: true  // Inline critical CSS
});

// Result: &lt;14KB of above-fold CSS inlined
</code></pre>

<pre><code class="language-html">&lt;!-- ❌ BEFORE: External CSS blocks rendering --&gt;
&lt;link rel="stylesheet" href="/styles.css"&gt;  &lt;!-- 245KB, blocks 800ms --&gt;

&lt;!-- ✅ AFTER: Inline critical, defer rest --&gt;
&lt;style&gt;
  /* Critical CSS inlined - 8KB compressed */
  .header{background:#fff;height:60px;position:fixed;top:0;width:100%}
  .hero{min-height:600px;display:flex;align-items:center}
  /* ... more critical styles ... */
&lt;/style&gt;

&lt;!-- Load remaining CSS asynchronously --&gt;
&lt;link rel="preload" href="/styles.css" as="style"
  onload="this.onload=null;this.rel=\'stylesheet\'"&gt;
&lt;noscript&gt;&lt;link rel="stylesheet" href="/styles.css"&gt;&lt;/noscript&gt;
</code></pre>

<!-- @TODO: Add screenshot of before/after showing FCP improvement -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Critical CSS Impact on FCP</p>
  <p class="text-sm text-gray-500 mt-2">Before/after filmstrip showing FCP improving from 2.1s to 0.9s after critical CSS inlining</p>
</div>

<h4>Component-scoped CSS</h4>

<pre><code class="language-javascript">// CSS Modules: Only load CSS for rendered components
import styles from \'./Button.module.css\';

export function Button() {
  return &lt;button className={styles.primary}&gt;Click&lt;/button&gt;;
}

// Generated output: button-abc123.css (only 2KB)
// No unused CSS from other components
</code></pre>

<h3>JavaScript loading strategies</h3>

<h4>Defer vs Async comparison</h4>

<pre><code class="language-html">&lt;!-- Default: Blocking, executes immediately --&gt;
&lt;script src="/app.js"&gt;&lt;/script&gt;

&lt;!-- Async: Non-blocking download, executes ASAP (order not guaranteed) --&gt;
&lt;script async src="/analytics.js"&gt;&lt;/script&gt;

&lt;!-- Defer: Non-blocking download, executes after HTML parsed (maintains order) --&gt;
&lt;script defer src="/app.js"&gt;&lt;/script&gt;
&lt;script defer src="/components.js"&gt;&lt;/script&gt;  &lt;!-- Runs after app.js --&gt;
</code></pre>

<!-- @TODO: Add diagram showing async vs defer execution timelines -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Script Loading Strategy Timeline</p>
  <p class="text-sm text-gray-500 mt-2">Diagram comparing blocking, async, and defer script execution relative to HTML parsing and DOMContentLoaded</p>
</div>

<h4>Module scripts with defer</h4>

<pre><code class="language-html">&lt;!-- Modern browsers: type=module is deferred by default --&gt;
&lt;script type="module" src="/app.js"&gt;&lt;/script&gt;

&lt;!-- Only modern browsers load modules, legacy gets polyfilled bundle --&gt;
&lt;script type="module" src="/app.modern.js"&gt;&lt;/script&gt;
&lt;script nomodule src="/app.legacy.js"&gt;&lt;/script&gt;
</code></pre>

<h4>Lazy loading non-critical scripts</h4>

<pre><code class="language-javascript">// Delay heavy third-party scripts until after page interactive
function loadScriptAfterInteractive(src) {
  if (document.readyState === \'complete\') {
    loadScript(src);
  } else {
    window.addEventListener(\'load\', () =&gt; {
      // Wait an additional second after load
      setTimeout(() =&gt; loadScript(src), 1000);
    });
  }
}

function loadScript(src) {
  const script = document.createElement(\'script\');
  script.src = src;
  script.defer = true;
  document.head.appendChild(script);
}

// Lazy load chat widget
loadScriptAfterInteractive(\'/chat-widget.js\');
</code></pre>

<h3>Third-party script audit</h3>

<table class="w-full my-6 border text-sm">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Script</th>
      <th class="border p-2">Size</th>
      <th class="border p-2">Blocking Time</th>
      <th class="border p-2">Action</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">Google Tag Manager</td>
      <td class="border p-2">38KB</td>
      <td class="border p-2">180ms</td>
      <td class="border p-2 text-blue-600">Defer until after FCP</td>
    </tr>
    <tr>
      <td class="border p-2">Intercom Chat</td>
      <td class="border p-2">285KB</td>
      <td class="border p-2">540ms</td>
      <td class="border p-2 text-blue-600">Lazy load on scroll/click</td>
    </tr>
    <tr>
      <td class="border p-2">Legacy jQuery</td>
      <td class="border p-2">95KB</td>
      <td class="border p-2">120ms</td>
      <td class="border p-2 text-red-600">Remove (unused)</td>
    </tr>
    <tr>
      <td class="border p-2">Old Facebook Pixel</td>
      <td class="border p-2">22KB</td>
      <td class="border p-2">45ms</td>
      <td class="border p-2 text-red-600">Remove (deprecated)</td>
    </tr>
  </tbody>
</table>

<!-- @TODO: Add screenshot of third-party impact analysis from Request Blocking -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Third-Party Impact Report</p>
  <p class="text-sm text-gray-500 mt-2">Chrome DevTools showing third-party scripts consuming 1.2s of main thread time, highlighted in red</p>
</div>

<h4>Budget enforcement</h4>

<pre><code class="language-json">// webpack bundle size budget
{
  "performance": {
    "maxEntrypointSize": 250000,
    "maxAssetSize": 100000,
    "hints": "error",
    "assetFilter": function(assetFilename) {
      return assetFilename.endsWith(\'.js\') || assetFilename.endsWith(\'.css\');
    }
  }
}
</code></pre>

<pre><code class="language-javascript">// Next.js bundle analyzer
module.exports = {
  webpack: (config, { isServer }) =&gt; {
    if (!isServer) {
      config.plugins.push(
        new BundleAnalyzerPlugin({
          analyzerMode: \'static\',
          openAnalyzer: false,
          reportFilename: \'bundle-report.html\'
        })
      );
    }
    return config;
  }
};
</code></pre>

<!-- @TODO: Add bundle analyzer screenshot showing large dependencies -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Webpack Bundle Analyzer Treemap</p>
  <p class="text-sm text-gray-500 mt-2">Treemap visualization showing moment.js taking 66KB of bundle (highlighted) - candidate for removal</p>
</div>

<h3>Polyfill optimization</h3>

<pre><code class="language-javascript">// ❌ BEFORE: Ship all polyfills to everyone
import \'core-js/stable\';
import \'regenerator-runtime/runtime\';

// ✅ AFTER: Conditional polyfill loading
&lt;script&gt;
  // Only load polyfills for browsers that need them
  if (!window.IntersectionObserver || !window.fetch) {
    const script = document.createElement(\'script\');
    script.src = \'/polyfills.js\';
    document.head.appendChild(script);
  }
&lt;/script&gt;

// Or use Polyfill.io for automatic detection
&lt;script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver,fetch"&gt;&lt;/script&gt;
</code></pre>

<h3>Real-world improvement</h3>

<table class="w-full my-6 border">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Optimization</th>
      <th class="border p-2">Before</th>
      <th class="border p-2">After</th>
      <th class="border p-2">FCP Improvement</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">Inline critical CSS</td>
      <td class="border p-2">245KB external</td>
      <td class="border p-2">8KB inline + defer rest</td>
      <td class="border p-2">-650ms</td>
    </tr>
    <tr>
      <td class="border p-2">Defer tag manager</td>
      <td class="border p-2">Blocking</td>
      <td class="border p-2">Load after FCP</td>
      <td class="border p-2">-180ms</td>
    </tr>
    <tr>
      <td class="border p-2">Remove jQuery</td>
      <td class="border p-2">95KB</td>
      <td class="border p-2">0KB (vanilla JS)</td>
      <td class="border p-2">-120ms</td>
    </tr>
    <tr>
      <td class="border p-2">Lazy load chat widget</td>
      <td class="border p-2">285KB blocking</td>
      <td class="border p-2">Load on interaction</td>
      <td class="border p-2">-540ms</td>
    </tr>
    <tr class="font-bold bg-green-50">
      <td class="border p-2" colspan="3">Total FCP improvement</td>
      <td class="border p-2">2.8s → 1.3s</td>
    </tr>
  </tbody>
</table>

<p>Measure render-blocking time in PSI and RUM. Small, prioritized CSS plus deferred scripts usually clears the path. Use Chrome DevTools Coverage panel to find unused code, and enforce budgets to prevent regression.</p>',
                ],
                'de' => [
                    'title' => 'Render-blocking Assets: CSS-Splitting und Script-Strategie',
                    'excerpt' => 'Gezieltes CSS-Splitting und defer/async entfernen Blocker.',
                    'body' => '<p>Blockierende Assets verzögern den First Paint. CSS nach Kritikalität aufteilen, Scripts bewusst laden.</p><h3>CSS-Delivery</h3><ul><li>Nur das wirklich nötige Critical CSS inline (&lt; 14KB komprimiert).</li><li>Rest per <code>rel=\"preload\" as=\"style\"</code> oder <code>media=\"print\" onload=\"this.media=\'all\'\"</code> laden.</li><li>Unbenutztes CSS entfernen; komponentenbasierte Styles bevorzugen.</li></ul><h3>Skript-Ladung</h3><ul><li><code>defer</code> für alle nicht-kritischen Skripte; <code>type=\"module\"</code> + <code>defer</code> für moderne Browser.</li><li><code>async</code> vermeiden, wenn Reihenfolge zählt; Tag-Manager erst nach First Paint laden.</li><li>Unnötige Polyfills streichen; moderne Bundles mit sauberem Targeting ausliefern.</li></ul><h3>Third Parties</h3><ul><li>Jeden Tag prüfen: Zweck, Owner, gesammelte Daten. Alte Pixel entfernen.</li><li>Chat-Widgets und AB-Tools lazy-loaden; klare Budgets vergeben.</li></ul><p>Render-Blocking-Zeit in PSI und RUM messen. Kleine, priorisierte CSS + deferte Skripte räumen meist den Weg frei.</p>',
                ],
            ],
        ],
        [
            'slug' => 'image-optimization-checklist',
            'date' => '2025-01-10',
            'reading_time' => '8 min',
            'category' => 'Images',
            'locales' => [
                'en' => [
                    'title' => 'Image optimization checklist: formats, DPR, and CDNs',
                    'excerpt' => 'Switching to AVIF/WebP and sizing per breakpoint is the fastest win.',
                    'body' => '<p>Large images dominate transfer. Optimize format, sizing, delivery, and caching.</p>

<!-- @TODO: Add comparison image showing JPEG vs WebP vs AVIF file sizes -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Format Comparison Visual</p>
  <p class="text-sm text-gray-500 mt-2">Three identical hero images side-by-side: JPEG (850KB), WebP (320KB), AVIF (210KB) showing quality and size tradeoffs</p>
</div>

<h3>Modern image formats</h3>

<pre><code class="language-html">&lt;picture&gt;
  &lt;!-- AVIF: Best compression (50-60% smaller than JPEG) --&gt;
  &lt;source
    type="image/avif"
    srcset="/hero-400.avif 400w,
            /hero-800.avif 800w,
            /hero-1200.avif 1200w,
            /hero-1600.avif 1600w"
    sizes="(max-width: 768px) 100vw, 1200px"&gt;

  &lt;!-- WebP: Good compression, wider support --&gt;
  &lt;source
    type="image/webp"
    srcset="/hero-400.webp 400w,
            /hero-800.webp 800w,
            /hero-1200.webp 1200w,
            /hero-1600.webp 1600w"
    sizes="(max-width: 768px) 100vw, 1200px"&gt;

  &lt;!-- JPEG fallback for legacy browsers --&gt;
  &lt;img
    src="/hero-1200.jpg"
    srcset="/hero-400.jpg 400w,
            /hero-800.jpg 800w,
            /hero-1200.jpg 1200w,
            /hero-1600.jpg 1600w"
    sizes="(max-width: 768px) 100vw, 1200px"
    alt="Hero image"
    width="1200"
    height="675"
    loading="eager"
    fetchpriority="high"&gt;
&lt;/picture&gt;
</code></pre>

<h4>Sharp image processing pipeline</h4>

<pre><code class="language-javascript">// Node.js: Generate responsive images with Sharp
import sharp from \'sharp\';

async function generateResponsiveImages(inputPath, outputBase) {
  const widths = [400, 800, 1200, 1600];
  const formats = [\'avif\', \'webp\', \'jpeg\'];

  for (const width of widths) {
    for (const format of formats) {
      await sharp(inputPath)
        .resize(width, null, { withoutEnlargement: true })
        .toFormat(format, {
          quality: format === \'avif\' ? 50 : format === \'webp\' ? 55 : 75,
          effort: 6  // AVIF compression effort
        })
        .toFile(`${outputBase}-${width}.${format}`);
    }
  }
}

// Usage
await generateResponsiveImages(\'hero.jpg\', \'dist/hero\');
// Output: hero-400.avif, hero-400.webp, hero-400.jpg, etc.
</code></pre>

<!-- @TODO: Add screenshot of image CDN control panel showing automatic optimization -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: CDN Image Optimization Dashboard</p>
  <p class="text-sm text-gray-500 mt-2">Cloudinary/Imgix dashboard showing auto-format, auto-quality settings and 68% bandwidth reduction</p>
</div>

<h3>CDN image transformation</h3>

<pre><code class="language-html">&lt;!-- Cloudinary: On-the-fly transformation --&gt;
&lt;img src="https://res.cloudinary.com/demo/image/upload/
  f_auto,q_auto,w_800,dpr_2.0/
  sample.jpg" alt="Product"&gt;

&lt;!-- Imgix: URL-based transformation --&gt;
&lt;img src="https://demo.imgix.net/image.jpg?
  auto=format,compress&amp;
  w=800&amp;
  dpr=2&amp;
  fit=crop" alt="Product"&gt;

&lt;!-- Next.js Image component (automatic optimization) --&gt;
&lt;Image
  src="/hero.jpg"
  alt="Hero"
  width={1200}
  height={675}
  priority
  sizes="(max-width: 768px) 100vw, 1200px"
/&gt;
</code></pre>

<h4>DPR (Device Pixel Ratio) targeting</h4>

<pre><code class="language-html">&lt;!-- Serve 2x images for retina, 1x for standard displays --&gt;
&lt;img
  srcset="/product-400.jpg 400w,
          /product-800.jpg 800w,
          /product-1200.jpg 1200w"
  sizes="(max-width: 768px) calc(100vw - 32px), 400px"
  src="/product-800.jpg"
  alt="Product"&gt;

&lt;!-- Browser automatically selects:
     - 400w for 400px viewport @ 1x DPR
     - 800w for 400px viewport @ 2x DPR (retina)
--&gt;
</code></pre>

<!-- @TODO: Add diagram showing responsive image selection logic -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: srcset Selection Flow Diagram</p>
  <p class="text-sm text-gray-500 mt-2">Flowchart showing how browser selects image based on viewport width, DPR, and sizes attribute</p>
</div>

<h3>Lazy loading strategies</h3>

<pre><code class="language-html">&lt;!-- Native lazy loading --&gt;
&lt;img src="/product.jpg" loading="lazy" decoding="async" alt="Product"&gt;

&lt;!-- Intersection Observer for custom lazy loading --&gt;
&lt;script&gt;
const imageObserver = new IntersectionObserver((entries, observer) =&gt; {
  entries.forEach(entry =&gt; {
    if (entry.isIntersecting) {
      const img = entry.target;
      img.src = img.dataset.src;
      img.srcset = img.dataset.srcset;
      img.classList.remove(\'lazy\');
      observer.unobserve(img);
    }
  });
}, {
  rootMargin: \'50px 0px\'  // Start loading 50px before viewport
});

document.querySelectorAll(\'img.lazy\').forEach(img =&gt; {
  imageObserver.observe(img);
});
&lt;/script&gt;
</code></pre>

<h3>Image delivery checklist</h3>

<table class="w-full my-6 border text-sm">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Optimization</th>
      <th class="border p-2">Implementation</th>
      <th class="border p-2">Savings</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">Use AVIF format</td>
      <td class="border p-2">&lt;picture&gt; + srcset</td>
      <td class="border p-2">~55% vs JPEG</td>
    </tr>
    <tr>
      <td class="border p-2">Responsive sizing</td>
      <td class="border p-2">srcset with sizes</td>
      <td class="border p-2">~40% bandwidth</td>
    </tr>
    <tr>
      <td class="border p-2">Lazy loading</td>
      <td class="border p-2">loading="lazy"</td>
      <td class="border p-2">~60% fewer requests</td>
    </tr>
    <tr>
      <td class="border p-2">Strip metadata</td>
      <td class="border p-2">Remove EXIF data</td>
      <td class="border p-2">~3-8KB per image</td>
    </tr>
    <tr>
      <td class="border p-2">CDN caching</td>
      <td class="border p-2">immutable + versioned URLs</td>
      <td class="border p-2">~95% cache hit rate</td>
    </tr>
  </tbody>
</table>

<!-- @TODO: Add before/after PageSpeed Insights showing image optimization impact -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Image Optimization Impact on Performance Score</p>
  <p class="text-sm text-gray-500 mt-2">PSI report showing "Properly size images" and "Serve images in modern formats" going from red (2.4MB saved) to green</p>
</div>

<p>Track image weight as a budget. Small wins across many assets can cut LCP and bandwidth costs quickly. Use Chrome DevTools Network panel filtered to images to identify the heaviest offenders first.</p>',
                ],
                'de' => [
                    'title' => 'Image-Optimierung: Formate, DPR und CDNs',
                    'excerpt' => 'AVIF/WebP plus Größen pro Breakpoint bringen den größten Effekt.',
                    'body' => '<p>Große Bilder dominieren die Transfermenge. Formate, Größe, Auslieferung und Caching optimieren.</p><h3>Formate und Größen</h3><ul><li>AVIF/WebP via <code>&lt;picture&gt;</code> mit <code>srcset</code> pro Breakpoint und DPR (1x/2x) ausliefern.</li><li>Fallback JPEG/PNG für ältere Browser.</li><li>Keine Oversized-Assets; gerenderte Breite mit <code>sizes</code> matchen.</li></ul><h3>Auslieferung</h3><ul><li>CDN mit On-the-fly-Resize/Kompression nutzen; EXIF strippen.</li><li>Lange Cache-Control mit immutable, versionierten URLs; bei Deploy purgen.</li><li>Hero-Bilder preladen; Below-the-Fold mit <code>loading=\"lazy\"</code> und <code>decoding=\"async\"</code>.</li></ul><h3>Qualität und Priorität</h3><ul><li>Mit Qualität ~45–55 für AVIF/WebP starten; nach Inhalt feinjustieren.</li><li><code>fetchpriority=\"low\"</code> für nicht-kritische Medien (z. B. Karussells).</li></ul><p>Bildgewicht als Budget tracken. Viele kleine Einsparungen senken LCP und Bandbreite spürbar.</p>',
                ],
            ],
        ],
        [
            'slug' => 'caching-and-cdn-strategy',
            'date' => '2025-01-11',
            'reading_time' => '9 min',
            'category' => 'Caching',
            'locales' => [
                'en' => [
                    'title' => 'Caching and CDN strategy: cache keys, SWR, and invalidation',
                    'excerpt' => 'Most slow sites misuse cache keys or skip stale-while-revalidate.',
                    'body' => '<p>Good caching starts with correct keys and predictable invalidation. Many sites vary on cookies or headers they do not need.</p>

<!-- @TODO: Add diagram showing cache hit vs miss performance impact -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Cache Hit Rate Impact on TTFB</p>
  <p class="text-sm text-gray-500 mt-2">Graph showing TTFB correlation with cache hit rate: 95% hits = 45ms avg, 50% hits = 380ms avg</p>
</div>

<h3>Smart cache key design</h3>

<pre><code class="language-javascript">// ❌ BEFORE: Too many variations fragment cache
Cache-Key: url + all cookies + user-agent + accept-language

// Result: 2% hit rate, mostly misses

// ✅ AFTER: Minimal essential variations
Cache-Key: normalized_url + locale + device_class

// Result: 87% hit rate
</code></pre>

<pre><code class="language-javascript">// Cloudflare Worker: Custom cache key normalization
export default {
  async fetch(request) {
    const url = new URL(request.url);

    // Normalize URL
    url.pathname = url.pathname.toLowerCase();
    if (url.pathname.endsWith(\'/\') &amp;&amp; url.pathname !== \'/\') {
      url.pathname = url.pathname.slice(0, -1);
    }

    // Extract only essential variations
    const locale = (request.headers.get(\'Accept-Language\') || \'en\').split(\',\')[0];
    const deviceType = /mobile/i.test(request.headers.get(\'User-Agent\') || \'\') ? \'mobile\' : \'desktop\';

    // Build minimal cache key
    const cacheKey = `${url.pathname}|${locale}|${deviceType}`;

    return fetch(request);
  }
};
</code></pre>

<h3>Stale-while-revalidate implementation</h3>

<pre><code class="language-nginx"># Nginx: SWR configuration
location / {
    proxy_cache my_cache;
    proxy_cache_valid 200 5m;

    # Serve stale content while revalidating
    proxy_cache_use_stale updating error timeout;
    proxy_cache_background_update on;

    # Stale content can be served for up to 1 hour
    proxy_cache_revalidate on;

    add_header Cache-Control "public, max-age=300, stale-while-revalidate=3600";
    add_header X-Cache-Status $upstream_cache_status;
}
</code></pre>

<!-- @TODO: Add timeline showing SWR masking origin delays -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Stale-While-Revalidate Timeline</p>
  <p class="text-sm text-gray-500 mt-2">Timeline showing user getting stale response instantly (50ms) while revalidation happens in background (no user wait)</p>
</div>

<h3>Cache invalidation strategies</h3>

<pre><code class="language-javascript">// Purge cache on deploy (Vercel example)
import { purgeCache } from \'@vercel/edge-config\';

// In CI/CD pipeline
async function deployHook() {
  // Purge specific paths
  await purgeCache([
    \'/\',
    \'/blog\',
    \'/blog/*\'
  ]);

  // Or purge by tags
  await purgeCache({ tags: [\'blog-posts\'] });
}
</code></pre>

<table class="w-full my-6 border text-sm">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Resource Type</th>
      <th class="border p-2">Cache-Control</th>
      <th class="border p-2">Invalidation</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">HTML pages</td>
      <td class="border p-2">s-maxage=300, stale-while-revalidate=3600</td>
      <td class="border p-2">On deploy, CMS update</td>
    </tr>
    <tr>
      <td class="border p-2">Static JS/CSS</td>
      <td class="border p-2">max-age=31536000, immutable</td>
      <td class="border p-2">Never (versioned URLs)</td>
    </tr>
    <tr>
      <td class="border p-2">Images</td>
      <td class="border p-2">max-age=31536000, immutable</td>
      <td class="border p-2">Never (versioned URLs)</td>
    </tr>
    <tr>
      <td class="border p-2">API responses</td>
      <td class="border p-2">s-maxage=60, stale-while-revalidate=600</td>
      <td class="border p-2">On data change</td>
    </tr>
  </tbody>
</table>

<!-- @TODO: Add screenshot of CDN analytics showing cache hit rate metrics -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: CDN Cache Analytics Dashboard</p>
  <p class="text-sm text-gray-500 mt-2">Cloudflare analytics showing 89% cache hit rate, bandwidth saved: 4.2TB, requests served: 12M</p>
</div>

<p>Write down the cache key and invalidation rules for every path type. Consistency is the performance feature. Monitor hit ratio closely - drops often signal cache key fragmentation or over-aggressive purging.</p>',
                ],
                'de' => [
                    'title' => 'Caching- und CDN-Strategie: Cache-Keys, SWR und Invalidation',
                    'excerpt' => 'Langsame Seiten leiden oft unter falschen Cache-Keys oder fehlendem SWR.',
                    'body' => '<p>Gutes Caching beginnt mit korrekten Keys und planbarer Invalidation. Viele Sites variieren auf Cookies/Headers, die sie nicht brauchen.</p><h3>Cache-Keys</h3><ul><li>Nur auf das Nötige variieren: Locale, Device-Klasse, ggf. AB-Bucket.</li><li>Auth-Cookies für öffentliche Seiten ignorieren; störende Header am Edge strippen.</li><li>URLs normalisieren (Trailing Slash, Kleinschreibung), um Fragmentierung zu vermeiden.</li></ul><h3>Policies</h3><ul><li><code>stale-while-revalidate</code> gegen Cold Starts, <code>stale-if-error</code> für Resilienz.</li><li>Statische Assets versioniert + immutable; lange TTLs.</li><li>HTML, wo möglich, pro Locale/Device cachen; kurze TTL, aber hohe Hit-Rate.</li></ul><h3>Invalidation</h3><ul><li>Purge automatisiert bei Deploy und CMS-Änderungen; nur geänderte Pfade invalidieren.</li><li>Hit-Ratio mit TTFB koppeln, um Fragmentierung früh zu sehen.</li></ul><p>Für jeden Pfadtyp Cache-Key und Invalidation notieren. Konsistenz ist hier das Performance-Feature.</p>',
                ],
            ],
        ],
        [
            'slug' => 'fonts-and-hydration',
            'date' => '2025-01-12',
            'reading_time' => '7 min',
            'category' => 'Fonts',
            'locales' => [
                'en' => [
                    'title' => 'Fonts and hydration: reducing FOIT, FOUT, and long tasks',
                    'excerpt' => 'Font loading and hydration often hurt INP and CLS.',
                    'body' => '<p>Fonts and heavy hydration drive long tasks and CLS. Tame both.</p>

<!-- @TODO: Add before/after showing font swap layout shift -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Font Swap FOUT Visualization</p>
  <p class="text-sm text-gray-500 mt-2">Side-by-side comparison showing text reflow during font loading: Arial → Custom Font causing 0.12 CLS</p>
</div>

<h3>Font loading optimization</h3>

<h4>Preload and font-display</h4>

<pre><code class="language-html">&lt;!-- Preload critical fonts --&gt;
&lt;link rel="preload"
  href="/fonts/inter-var.woff2"
  as="font"
  type="font/woff2"
  crossorigin&gt;

&lt;style&gt;
  @font-face {
    font-family: \'Inter\';
    src: url(\'/fonts/inter-var.woff2\') format(\'woff2-variations\');
    font-weight: 100 900;
    font-display: swap;  /* Show fallback immediately */
  }

  body {
    font-family: \'Inter\', system-ui, -apple-system, sans-serif;
  }
&lt;/style&gt;
</code></pre>

<h4>Size-adjusted fallback fonts</h4>

<pre><code class="language-css">/* Match fallback font metrics to web font */
@font-face {
  font-family: \'Inter\';
  src: url(\'/fonts/inter-var.woff2\') format(\'woff2-variations\');
  font-weight: 100 900;
  font-display: swap;
}

/* Adjust Arial to match Inter metrics exactly */
@font-face {
  font-family: \'Arial Adjusted\';
  src: local(\'Arial\');
  size-adjust: 106%;
  ascent-override: 90%;
  descent-override: 22%;
  line-gap-override: 0%;
}

body {
  font-family: \'Inter\', \'Arial Adjusted\', sans-serif;
}

/* Result: Zero layout shift when font swaps */
</code></pre>

<!-- @TODO: Add tool screenshot showing font metric comparison -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Font Metrics Comparison Tool</p>
  <p class="text-sm text-gray-500 mt-2">Screenshot of Fontaine or Capsize tool showing metric overlay of web font vs fallback for calculating size-adjust</p>
</div>

<h4>Variable fonts for weight consolidation</h4>

<pre><code class="language-css">/* ❌ BEFORE: Multiple font files for different weights */
@font-face {
  font-family: \'Roboto\';
  src: url(\'/roboto-regular.woff2\');
  font-weight: 400;
}
@font-face {
  font-family: \'Roboto\';
  src: url(\'/roboto-medium.woff2\');
  font-weight: 500;
}
@font-face {
  font-family: \'Roboto\';
  src: url(\'/roboto-bold.woff2\');
  font-weight: 700;
}
/* Total: 180KB (60KB × 3) */

/* ✅ AFTER: Single variable font */
@font-face {
  font-family: \'Roboto\';
  src: url(\'/roboto-variable.woff2\') format(\'woff2-variations\');
  font-weight: 100 900;
}
/* Total: 95KB (47% smaller) */
</code></pre>

<h3>Hydration optimization</h3>

<h4>Islands architecture (partial hydration)</h4>

<pre><code class="language-jsx">// ❌ BEFORE: Full page hydration
import { useState } from \'react\';

export default function Page({ products }) {
  return (
    &lt;div&gt;
      &lt;Header /&gt;  {/* Hydrates entire header */}
      &lt;Hero /&gt;     {/* Hydrates static hero */}
      &lt;ProductList products={products} /&gt;  {/* Hydrates all products */}
      &lt;Footer /&gt;   {/* Hydrates entire footer */}
    &lt;/div&gt;
  );
}
// Result: 285KB JS, 540ms long task

// ✅ AFTER: Island-based hydration (Astro, Fresh, etc.)
---
import Header from \'./Header.astro\';  // Static, no hydration
import Hero from \'./Hero.astro\';      // Static, no hydration
import ProductList from \'./ProductList.jsx\';
import Footer from \'./Footer.astro\';  // Static, no hydration
---

&lt;div&gt;
  &lt;Header /&gt;
  &lt;Hero /&gt;
  &lt;!-- Only hydrate interactive component --&gt;
  &lt;ProductList client:visible products={products} /&gt;
  &lt;Footer /&gt;
&lt;/div&gt;

// Result: 42KB JS, 85ms long task (84% reduction)
</code></pre>

<!-- @TODO: Add diagram showing islands architecture -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Islands Architecture Diagram</p>
  <p class="text-sm text-gray-500 mt-2">Page layout showing static HTML (gray) with small interactive "islands" (blue) for SearchBar, AddToCart, Newsletter</p>
</div>

<h4>Progressive hydration</h4>

<pre><code class="language-javascript">// Hydrate on visibility (lazy hydration)
import { Suspense, lazy } from \'react\';

const HeavyComponent = lazy(() =&gt; import(\'./HeavyComponent\'));

function App() {
  return (
    &lt;&gt;
      {/* Static content renders immediately */}
      &lt;Header /&gt;
      &lt;Hero /&gt;

      {/* Heavy component only loads when in viewport */}
      &lt;Suspense fallback={&lt;Skeleton /&gt;}&gt;
        &lt;HeavyComponent /&gt;
      &lt;/Suspense&gt;
    &lt;/&gt;
  );
}
</code></pre>

<h4>Measuring long tasks</h4>

<pre><code class="language-javascript">// Track long tasks (> 50ms)
const observer = new PerformanceObserver((list) =&gt; {
  for (const entry of list.getEntries()) {
    if (entry.duration &gt; 50) {
      console.warn(\'Long task detected:\', {
        duration: entry.duration,
        startTime: entry.startTime,
        attribution: entry.attribution
      });

      // Send to analytics
      fetch(\'/api/metrics\', {
        method: \'POST\',
        body: JSON.stringify({
          type: \'long-task\',
          duration: entry.duration,
          url: window.location.pathname
        })
      });
    }
  }
});

observer.observe({ entryTypes: [\'longtask\'] });
</code></pre>

<!-- @TODO: Add screenshot of Performance panel showing long tasks -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: Chrome DevTools Long Tasks</p>
  <p class="text-sm text-gray-500 mt-2">Performance panel showing main thread with red bars indicating long tasks (>50ms), highlighting hydration at 420ms</p>
</div>

<h3>Real-world optimization results</h3>

<table class="w-full my-6 border">
  <thead class="bg-gray-50">
    <tr>
      <th class="border p-2">Optimization</th>
      <th class="border p-2">Before</th>
      <th class="border p-2">After</th>
      <th class="border p-2">INP Impact</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border p-2">Font preload + swap</td>
      <td class="border p-2">FOIT 800ms</td>
      <td class="border p-2">Instant fallback</td>
      <td class="border p-2">-95ms INP</td>
    </tr>
    <tr>
      <td class="border p-2">Size-adjusted fallback</td>
      <td class="border p-2">0.12 CLS</td>
      <td class="border p-2">0.00 CLS</td>
      <td class="border p-2">Improved perceived speed</td>
    </tr>
    <tr>
      <td class="border p-2">Variable font</td>
      <td class="border p-2">180KB (3 files)</td>
      <td class="border p-2">95KB (1 file)</td>
      <td class="border p-2">-85KB transfer</td>
    </tr>
    <tr>
      <td class="border p-2">Islands hydration</td>
      <td class="border p-2">285KB JS</td>
      <td class="border p-2">42KB JS</td>
      <td class="border p-2">-180ms INP</td>
    </tr>
    <tr>
      <td class="border p-2">Reduce long tasks</td>
      <td class="border p-2">540ms hydration</td>
      <td class="border p-2">85ms hydration</td>
      <td class="border p-2">-140ms INP</td>
    </tr>
    <tr class="font-bold bg-green-50">
      <td class="border p-2" colspan="3">Total INP improvement</td>
      <td class="border p-2">385ms → 70ms ✓</td>
    </tr>
  </tbody>
</table>

<!-- @TODO: Add graph showing INP improvements over time -->
<div class="image-placeholder bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 my-6 text-center">
  <p class="text-gray-600 font-medium">Image: INP Improvement Timeline</p>
  <p class="text-sm text-gray-500 mt-2">Line graph showing INP dropping from 385ms to 70ms over 4 optimization iterations, crossing into "good" threshold</p>
</div>

<p>Track INP and CLS after font and hydration changes. Small reductions in long tasks often produce big INP gains. Use Chrome DevTools Performance panel to profile hydration costs, and consider partial hydration frameworks like Astro or Qwik for marketing pages.</p>',
                ],
                'de' => [
                    'title' => 'Fonts und Hydration: FOIT, FOUT und Long Tasks senken',
                    'excerpt' => 'Font-Loading und Hydration verschlechtern oft INP und CLS.',
                    'body' => '<p>Fonts und schwere Hydration erzeugen Long Tasks und CLS. Beides zähmen.</p><h3>Font-Loading</h3><ul><li>Primäre Textschrift preladen; <code>font-display: swap</code> mit size-adjusted Fallbacks gegen Sprünge.</li><li>Nur nötige Gewichte/Stile ausliefern; Variable Fonts nutzen, wenn kleiner.</li><li>Self-host mit gutem Caching; keine render-blocking Font-Loader von Drittanbietern.</li></ul><h3>Hydration-Strategie</h3><ul><li>Marketing-Seiten serverseitig rendern; kleine JS-Sprinkles statt voller SPA-Hydration.</li><li>Nur interaktive Inseln hydratisieren; nicht das gesamte Above-the-Fold.</li><li>Long Tasks vor/nachher messen; Ziel &lt; 50ms auf Midrange-Geräten.</li></ul><p>INP und CLS nach Font- und Hydration-Änderungen beobachten. Kleine Reduktionen bei Long Tasks bringen oft große INP-Gewinne.</p>',
                ],
            ],
        ],
    ],
];
