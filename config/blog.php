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
                    'body' => '<p>Start with pragmatic budgets: LCP &lt; 2.5s (mobile) and &lt; 1.8s (desktop), CLS &lt; 0.1, INP &lt; 200ms. Track them per template or route, not just per origin, and distinguish mobile from desktop so regressions are actionable.</p><h3>How to define baselines</h3><ul><li>Use 75th percentile, not averages, and watch p95 for tail problems.</li><li>Pair vitals with leading indicators: JS execution time, total transfer size, number of requests.</li><li>Snapshot HTML weight and image bytes; they often predict future regressions.</li></ul><h3>Noise-free alerting</h3><ul><li>Trigger alerts on deltas (e.g., +15% LCP week-over-week) instead of single-run spikes.</li><li>Alert only when sample size &gt;= 20 runs to avoid flicker.</li><li>Group alerts by template so teams know where to look.</li></ul><p>Roll up reports weekly with sparklines per metric and a short narrative: what changed, likely cause, and the one recommended fix.</p>',
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
                    'body' => '<p>Most LCP is a hero image or headline. Aim to deliver it in the first round-trip: prioritize the resource, remove render blockers, and keep TTFB low.</p><h3>Image strategy</h3><ul><li>Preload the exact hero asset with <code>&lt;link rel=\"preload\" as=\"image\" imagesrcset=\"...\" imagesizes=\"...\"&gt;</code>.</li><li>Add <code>fetchpriority=\"high\"</code> to the LCP image and avoid <code>loading=\"lazy\"</code> on it.</li><li>Serve modern formats (AVIF/WebP) and responsive sizes; avoid 2x oversizing.</li></ul><h3>CSS and JS</h3><ul><li>Inline critical CSS for header/hero; defer the rest with <code>media=\"print\" onload=\"this.media=\'all\'\"</code> or <code>rel=\"preload\" as=\"style\"</code>.</li><li>Defer non-critical JS; remove unused polyfills and heavy tag-manager payloads.</li></ul><h3>Server and TTFB</h3><ul><li>Cache HTML at the CDN for anonymous traffic; keep HTML under ~14KB compressed.</li><li>Warm origins or functions; enable HTTP/2 or HTTP/3 and TLS 1.3.</li><li>Send Early Hints if available to kick off hero fetch sooner.</li></ul><p>Measure LCP separately for mobile and desktop, and validate in real-user monitoring to catch device-specific issues.</p>',
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
                    'body' => '<p>CLS is almost always unreserved space: media without dimensions, delayed fonts, and elements injected above existing content.</p><h3>Media and embeds</h3><ul><li>Set <code>width</code>/<code>height</code> or <code>aspect-ratio</code> on images, videos, and embeds.</li><li>Reserve ad slots with CSS aspect-ratio boxes; do not collapse them while loading.</li><li>Use <code>object-fit: cover</code> with fixed containers to avoid reflow.</li></ul><h3>Fonts</h3><ul><li>Preload first-paint fonts; use <code>font-display: swap</code> with metric-compatible fallbacks (<code>size-adjust</code> helps).</li><li>Limit font variants; reduce FOUT/FOIT by keeping initial CSS small.</li></ul><h3>Dynamic UI</h3><ul><li>Do not insert consent banners or promos above existing content; place them in reserved regions or as bottom sheets.</li><li>Animate height changes with transforms, not layout thrash.</li></ul><p>Monitor CLS per template and device class. Fix the biggest cumulative contributors first; a few offenders often account for &gt;80% of shifts.</p>',
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
                    'body' => '<p>High TTFB usually means cache misses or slow server work. Fix it at the edges first, then the origin.</p><h3>Cache keys and CDN</h3><ul><li>Cache HTML for anonymous traffic; avoid varying on cookies unless necessary.</li><li>Use <code>stale-while-revalidate</code> to mask cold starts, <code>stale-if-error</code> for resilience.</li><li>Prefer HTTP/2 or HTTP/3; keep connection reuse high with keep-alive.</li></ul><h3>Origin performance</h3><ul><li>Keep SSR light: minimal DB calls, no blocking third-party APIs on first paint.</li><li>Pool DB connections; profile slow queries; add response-level caching where possible.</li><li>Compress HTML with Brotli; keep markup concise.</li></ul><h3>TLS and platform</h3><ul><li>Enable TLS 1.3, OCSP stapling, and modern cipher suites.</li><li>Warm functions/containers with scheduled pings; scale to zero cautiously.</li></ul><p>Track TTFB separately by geography and device. A CDN close to users plus slim HTML is the fastest win.</p>',
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
                    'body' => '<p>Blocking assets delay first paint. Split CSS by criticality and load scripts with intent.</p><h3>CSS delivery</h3><ul><li>Inline only the critical CSS for above-the-fold shell; keep it &lt; 14KB compressed.</li><li>Load the rest via <code>rel=\"preload\" as=\"style\"</code> or <code>media=\"print\" onload=\"this.media=\'all\'\"</code>.</li><li>Remove unused CSS; prefer component-scoped styles.</li></ul><h3>Script loading</h3><ul><li>Set <code>defer</code> on all non-critical scripts; use <code>type=\"module\"</code> + <code>defer</code> for modern browsers.</li><li>Avoid <code>async</code> where order matters; defer tag-manager containers until after first paint.</li><li>Drop unused polyfills; ship modern bundles with good browser targeting.</li></ul><h3>Third parties</h3><ul><li>Audit each tag: purpose, owner, data collected. Remove legacy pixels.</li><li>Lazy-load chat widgets and AB tools; give them budgets.</li></ul><p>Measure render-blocking time in PSI and RUM. Small, prioritized CSS plus deferred scripts usually clears the path.</p>',
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
                    'body' => '<p>Large images dominate transfer. Optimize format, sizing, delivery, and caching.</p><h3>Formats and sizing</h3><ul><li>Serve AVIF/WebP via <code>&lt;picture&gt;</code> with <code>srcset</code> per breakpoint and DPR (1x/2x).</li><li>Provide a fallback JPEG/PNG for legacy browsers.</li><li>Avoid oversized assets; match rendered size with <code>sizes</code>.</li></ul><h3>Delivery</h3><ul><li>Use a CDN with on-the-fly resize and compression; strip EXIF.</li><li>Long cache-control with immutable, versioned URLs; purge on deploy.</li><li>Preload hero images; lazy-load below-the-fold with <code>loading=\"lazy\"</code> and <code>decoding=\"async\"</code>.</li></ul><h3>Quality and formats</h3><ul><li>Start at quality ~45–55 for AVIF/WebP; adjust by content type.</li><li>Use <code>fetchpriority=\"low\"</code> on non-critical media in carousels.</li></ul><p>Track image weight as a budget. Small wins across many assets can cut LCP and bandwidth costs quickly.</p>',
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
                    'body' => '<p>Good caching starts with correct keys and predictable invalidation. Many sites vary on cookies or headers they do not need.</p><h3>Cache keys</h3><ul><li>Vary only on essentials: locale, device class, sometimes AB bucket.</li><li>Ignore auth cookies for public pages; strip noisy headers at the edge.</li><li>Normalize URLs (trailing slash, lowercase) to avoid fragmentation.</li></ul><h3>Policies</h3><ul><li>Use <code>stale-while-revalidate</code> to mask cold starts; <code>stale-if-error</code> for resilience.</li><li>Immutable, versioned static assets; long TTLs.</li><li>HTML cached per locale/device where possible; short TTL but high hit rate.</li></ul><h3>Invalidation</h3><ul><li>Automate purge on deploy and CMS updates; keep it scoped to changed paths.</li><li>Monitor hit ratio with TTFB to catch fragmentation fast.</li></ul><p>Write down the cache key and invalidation rules for every path type. Consistency is the performance feature.</p>',
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
                    'body' => '<p>Fonts and heavy hydration drive long tasks and CLS. Tame both.</p><h3>Font loading</h3><ul><li>Preload primary text font; use <code>font-display: swap</code> with size-adjusted fallbacks to limit jumps.</li><li>Ship only needed weights/styles; prefer variable fonts if smaller.</li><li>Self-host with caching; avoid render-blocking third-party font loaders.</li></ul><h3>Hydration strategy</h3><ul><li>Keep marketing pages server-rendered; add small sprinkles instead of full SPA hydration.</li><li>Hydrate only interactive islands; avoid hydrating the whole above-the-fold.</li><li>Measure long tasks before/after; aim for &lt; 50ms tasks on mid-tier devices.</li></ul><p>Track INP and CLS after font and hydration changes. Small reductions in long tasks often produce big INP gains.</p>',
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
