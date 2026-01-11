<?php

return [
    'meta' => [
        'title' => 'pagespeed.world — modernes PageSpeed-Insights-Monitoring',
        'description' => 'Überwache Core Web Vitals, Bundle-Größen und Lighthouse-Scores automatisch. DebugBear-inspiriertes Monitoring mit transparenten Preisen und zweisprachigen Seiten.',
        'og_title' => 'pagespeed.world — PageSpeed-Insights-Monitoring',
        'og_description' => 'Handlungsfähiges Core-Web-Vitals-Monitoring mit klaren Empfehlungen und fairen Preisen.',
    ],
    'nav' => [
        'features' => 'Funktionen',
        'pricing' => 'Preise',
        'faq' => 'FAQ',
        'blog' => 'Blog',
        'sign_in' => 'Login',
        'get_started' => 'Loslegen',
    ],
    'footer' => [
        'tagline' => 'Performance-Budgets, Bundles und Core Web Vitals zuverlässig im Blick.',
        'product' => 'Produkt',
        'resources' => 'Ressourcen',
        'legal' => 'Rechtliches',
        'contact' => 'Kontakt',
        'imprint' => 'Impressum',
        'privacy' => 'Datenschutz',
        'rights' => 'Alle Rechte vorbehalten.',
    ],
    'home' => [
        'hero' => [
            'eyebrow' => 'DebugBear-Einblicke. Freundlichere Preise.',
            'title' => 'Schnellere Seiten ausliefern und belegen.',
            'subtitle' => 'Automatisches PageSpeed Insights, Bundle-Tracking und Wochenberichte. Für Produktteams gebaut, für Startups bepreist.',
            'primary' => 'Monitoring starten',
            'secondary' => 'Funktionen ansehen',
        ],
        'metrics' => [
            'uptime' => '99,9 % Monitoring-Uptime',
            'sites' => '300+ überwachte Domains',
            'savings' => '18 % mediane LCP-Verbesserung in 30 Tagen',
        ],
        'features' => [
            [
                'title' => 'Core Web Vitals zuerst',
                'body' => 'LCP, CLS, FID/INP über Geräte hinweg mit Regressions-Alerts und Wochen-Trends.',
            ],
            [
                'title' => 'Bundle-Guardrails',
                'body' => 'JS/CSS-Bundles, Gzip/Brotli-Größen und Third-Party-Drift im Blick, bevor es live geht.',
            ],
            [
                'title' => 'Konkrete Anleitungen',
                'body' => 'Playbooks für render-blocking Assets, Caching, Bilder und Fonts, damit Teams schnell handeln.',
            ],
        ],
        'cta' => [
            'title' => 'Bereit für schnellere Seiten?',
            'subtitle' => 'Account erstellen, URL hinzufügen und in wenigen Minuten den ersten PSI-Run erhalten.',
            'primary' => 'Monitoring starten',
            'secondary' => 'Playbooks lesen',
        ],
    ],
    'features' => [
        'title' => 'Funktionen | pagespeed.world',
        'description' => 'DebugBear-inspiriertes PageSpeed-Monitoring: Core Web Vitals, Bundle-Tracking, Alerts und Wochenberichte.',
        'sections' => [
            [
                'title' => 'Stabile Performance-Baselines',
                'body' => 'Budgets für LCP, CLS, TTFB und Bundle-Größe definieren. Wir alarmieren bei Regressionen und zeigen Trends, damit Performance verteidigt werden kann.',
            ],
            [
                'title' => 'Deep Dives bei Spikes',
                'body' => 'Render-blocking Assets, Long Tasks und große Bundles aufschlüsseln. Übeltäter mit Requests-Wasserfällen und Coverage-Hinweisen finden.',
            ],
            [
                'title' => 'Reports, die verstanden werden',
                'body' => 'Wöchentliche E-Mail-Reports mit Klartext-Zusammenfassungen und Screenshots, damit auch Nicht-Techs Wirkung sehen.',
            ],
            [
                'title' => 'Caching- und CDN-bewusst',
                'body' => 'Cache-Header, CDN-Hit-Rates und stale-while-revalidate prüfen. Verhindert ungewollt kalte Antworten.',
            ],
            [
                'title' => 'Moderne DX',
                'body' => 'JSON-Exporte, API-Zugang und Webhooks für CI-Checks. PRs bleiben schnell und Budgets eingehalten.',
            ],
        ],
    ],
    'pricing' => [
        'title' => 'Preise | pagespeed.world',
        'description' => 'Einfache Preise für PageSpeed-Monitoring mit großzügigen Limits.',
        'plans' => [
            [
                'name' => 'Starter',
                'price' => '39 €',
                'period' => 'pro Monat',
                'features' => [
                    '5 überwachte Domains',
                    'Tägliche PSI-Runs (Mobile & Desktop)',
                    'Core-Web-Vitals-Alerts',
                    'Wöchentliche E-Mail-Reports',
                    'E-Mail-Support',
                ],
            ],
            [
                'name' => 'Growth',
                'price' => '99 €',
                'period' => 'pro Monat',
                'featured' => true,
                'features' => [
                    '15 überwachte Domains',
                    '4 PSI-Runs pro Tag',
                    'Bundle-Tracking',
                    'Slack/Webhook-Alerts',
                    'API-Zugang',
                ],
            ],
            [
                'name' => 'Scale',
                'price' => 'Individuell',
                'period' => 'lass uns sprechen',
                'features' => [
                    'Unlimitierte Domains',
                    'Feingranulare Budgets',
                    'SLA & Onboarding',
                    'Dedizierter Slack',
                    'Custom Reporting',
                ],
            ],
        ],
        'note' => 'Alle Pläne beinhalten Support auf Deutsch und Englisch, caching-freundliche SSR-Seiten und exportierbare Reports.',
    ],
    'faq' => [
        'title' => 'FAQ | pagespeed.world',
        'description' => 'Antworten zu PageSpeed-Monitoring, Core Web Vitals und Preisen.',
        'items' => [
            [
                'q' => 'Wie führt ihr PageSpeed Insights aus?',
                'a' => 'Wir planen PSI-Runs auf Mobile- und Desktop-Profilen, in deiner Zeitzone. Ergebnisse werden gecacht und mit Baselines verglichen.',
            ],
            [
                'q' => 'Alarmiert ihr bei Regressionen?',
                'a' => 'Ja, Budgets für LCP, CLS, TTFB, JS/CSS-Bundle und Requests. Alerts via E-Mail oder Webhook.',
            ],
            [
                'q' => 'Kann ich mehrere Sprachen verfolgen?',
                'a' => 'Ja. Marketing-Seiten haben hreflang, Monitoring kann lokalisierten URLs folgen.',
            ],
            [
                'q' => 'Gibt es eine API?',
                'a' => 'Growth und Scale enthalten API-Zugang für CI-Checks, Exporte und Budget-Management.',
            ],
            [
                'q' => 'Bietet ihr Onboarding an?',
                'a' => 'Scale enthält geführtes Setup, SLO-Definitionen und individuelle Dashboards.',
            ],
        ],
    ],
    'about' => [
        'title' => 'Über uns | pagespeed.world',
        'description' => 'Wir sind Engineers mit Fokus auf schnelle Nutzererlebnisse.',
        'mission' => 'pagespeed.world macht Core-Web-Vitals-Monitoring praktikabel für schlanke Teams. Von DebugBear inspiriert, für Startups und Agenturen optimiert.',
        'values' => [
            'Pragmatische Empfehlungen statt Rauschen',
            'Planbare Preise',
            'Respekt vor Entwicklerzeit',
        ],
    ],
    'contact' => [
        'title' => 'Kontakt | pagespeed.world',
        'description' => 'Sprich mit einem Menschen über Monitoring, Budgets oder Migrationen.',
        'cta' => 'Schreib an hello@pagespeed.world – Antwort werktags innerhalb eines Tages.',
        'form' => [
            'name' => 'Name',
            'name_placeholder' => 'Dein Name',
            'email' => 'E-Mail',
            'email_placeholder' => 'du@beispiel.de',
            'subject' => 'Betreff',
            'subject_placeholder' => 'Wie können wir helfen?',
            'message' => 'Nachricht',
            'message_placeholder' => 'Erzähl uns mehr über dein Projekt oder deine Frage...',
            'submit' => 'Nachricht senden',
        ],
        'success' => 'Danke für deine Nachricht! Wir melden uns innerhalb eines Werktags.',
        'turnstile_required' => 'Bitte schließe die Sicherheitsprüfung ab.',
        'turnstile_failed' => 'Sicherheitsprüfung fehlgeschlagen. Bitte erneut versuchen.',
        'other_ways' => 'Weitere Kontaktmöglichkeiten',
        'email_label' => 'E-Mail',
    ],
    'blog' => [
        'title' => 'Performance-Playbooks',
        'description' => 'Technische Guides zu Core Web Vitals, Caching, Bundles und render-blocking Fixes.',
        'read_more' => 'Weiterlesen',
        'back' => 'Zurück zum Blog',
    ],
];
