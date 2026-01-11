<?php

return [
    'meta' => [
        'title' => 'pagespeed.world — modern PageSpeed Insights monitoring',
        'description' => 'Monitor Core Web Vitals, bundle size, and lighthouse scores automatically. DebugBear-inspired monitoring with transparent pricing and multi-locale marketing pages.',
        'og_title' => 'pagespeed.world — PageSpeed Insights monitoring',
        'og_description' => 'Actionable Core Web Vitals monitoring with clear guidance and predictable pricing.',
    ],
    'nav' => [
        'features' => 'Features',
        'pricing' => 'Pricing',
        'faq' => 'FAQ',
        'blog' => 'Blog',
        'sign_in' => 'Sign in',
        'get_started' => 'Get started',
    ],
    'footer' => [
        'tagline' => 'Monitor performance budgets, bundles, and Core Web Vitals with confidence.',
        'product' => 'Product',
        'resources' => 'Resources',
        'legal' => 'Legal',
        'contact' => 'Contact',
        'imprint' => 'Imprint',
        'privacy' => 'Privacy',
        'rights' => 'All rights reserved.',
    ],
    'home' => [
        'hero' => [
            'eyebrow' => 'DebugBear-style insights. Friendlier pricing.',
            'title' => 'Ship faster pages and prove it to stakeholders.',
            'subtitle' => 'Automated PageSpeed Insights, bundle tracking, and weekly reports. Built for product teams, priced for startups.',
            'primary' => 'Start monitoring',
            'secondary' => 'See features',
        ],
        'metrics' => [
            'uptime' => '99.9% monitoring uptime',
            'sites' => '300+ monitored origins',
            'savings' => '18% median LCP improvement in 30 days',
        ],
        'features' => [
            [
                'title' => 'Core Web Vitals first',
                'body' => 'Track LCP, CLS, FID/INP across devices with regression alerts and weekly rollups.',
            ],
            [
                'title' => 'Bundle size guardrails',
                'body' => 'Watch JS/CSS bundles, gzip/brotli sizes, and third-party drift before it reaches production.',
            ],
            [
                'title' => 'Actionable guidance',
                'body' => 'Built-in playbooks for render-blocking assets, caching, images, and fonts so teams can act quickly.',
            ],
        ],
        'cta' => [
            'title' => 'Ready to launch faster pages?',
            'subtitle' => 'Create an account, add a URL, and get your first PSI snapshot in minutes.',
            'primary' => 'Start monitoring',
            'secondary' => 'Read the playbooks',
        ],
    ],
    'features' => [
        'title' => 'Features | pagespeed.world',
        'description' => 'DebugBear-inspired PageSpeed monitoring: Core Web Vitals, bundle tracking, alerts, and weekly reporting.',
        'sections' => [
            [
                'title' => 'Performance baselines that stick',
                'body' => 'Define budgets for LCP, CLS, TTFB, and bundle size. We alert on regressions and show trend lines so you can defend performance work.',
            ],
            [
                'title' => 'Deep dives when things spike',
                'body' => 'Drill into render-blocking assets, long tasks, and heavy bundles. Pinpoint offenders with request waterfalls and coverage hints.',
            ],
            [
                'title' => 'Reports stakeholders love',
                'body' => 'Weekly email reports with plain-language summaries and screenshots so non-engineers understand impact.',
            ],
            [
                'title' => 'Caching and CDN aware',
                'body' => 'Verify cache headers, CDN hit ratios, and stale-while-revalidate usage. Stop serving cold responses unintentionally.',
            ],
            [
                'title' => 'Modern DX',
                'body' => 'JSON exports, API access, and webhooks for CI checks. Keep PRs fast and budgets respected.',
            ],
        ],
    ],
    'pricing' => [
        'title' => 'Pricing | pagespeed.world',
        'description' => 'Straightforward pricing for PageSpeed monitoring with generous limits.',
        'plans' => [
            [
                'name' => 'Starter',
                'price' => '$39',
                'period' => 'per month',
                'features' => [
                    '5 monitored origins',
                    'Daily PSI runs (mobile & desktop)',
                    'Core Web Vitals alerts',
                    'Weekly email reports',
                    'Email support',
                ],
            ],
            [
                'name' => 'Growth',
                'price' => '$99',
                'period' => 'per month',
                'featured' => true,
                'features' => [
                    '15 monitored origins',
                    '4x daily PSI runs',
                    'Bundle size tracking',
                    'Slack/webhook alerts',
                    'API access',
                ],
            ],
            [
                'name' => 'Scale',
                'price' => 'Custom',
                'period' => "let's talk",
                'features' => [
                    'Unlimited origins',
                    'Fine-grained budgets',
                    'SLA & onboarding',
                    'Dedicated Slack',
                    'Custom reporting',
                ],
            ],
        ],
        'note' => 'All plans include German and English support, caching-friendly SSR pages, and exportable reports.',
    ],
    'faq' => [
        'title' => 'FAQ | pagespeed.world',
        'description' => 'Answers about PageSpeed monitoring, Core Web Vitals, and pricing.',
        'items' => [
            [
                'q' => 'How do you run PageSpeed Insights?',
                'a' => 'We schedule PSI runs on mobile and desktop profiles, respecting your time zone. Results are cached and compared against previous baselines.',
            ],
            [
                'q' => 'Do you alert on regressions?',
                'a' => 'Yes, define budgets for LCP, CLS, TTFB, JS/CSS bundle size, and total requests. We alert via email or webhooks.',
            ],
            [
                'q' => 'Can I track multiple locales?',
                'a' => 'Yes. Marketing pages support hreflang, and monitoring can target localized URLs.',
            ],
            [
                'q' => 'Is there an API?',
                'a' => 'Growth and Scale plans include API access for CI checks, exporting runs, and managing budgets.',
            ],
            [
                'q' => 'Do you offer onboarding?',
                'a' => 'Scale plans include guided setup, SLO definitions, and dashboards tailored to your team.',
            ],
        ],
    ],
    'about' => [
        'title' => 'About | pagespeed.world',
        'description' => "We're engineers obsessed with fast user experiences.",
        'mission' => 'We built pagespeed.world to make Core Web Vitals monitoring practical for lean teams. Inspired by DebugBear, optimized for startups and agencies.',
        'values' => [
            'Practical guidance over noise',
            'Predictable pricing',
            'Respect for developer time',
        ],
    ],
    'contact' => [
        'title' => 'Contact | pagespeed.world',
        'description' => 'Talk to a human about performance monitoring, budgets, or migrations.',
        'cta' => "Email us at hello@pagespeed.world and we'll reply within one business day.",
        'form' => [
            'name' => 'Name',
            'name_placeholder' => 'Your name',
            'email' => 'Email',
            'email_placeholder' => 'you@example.com',
            'subject' => 'Subject',
            'subject_placeholder' => 'What can we help you with?',
            'message' => 'Message',
            'message_placeholder' => 'Tell us more about your project or question...',
            'submit' => 'Send message',
        ],
        'success' => "Thanks for reaching out! We'll get back to you within one business day.",
        'turnstile_required' => 'Please complete the security challenge.',
        'turnstile_failed' => 'Security verification failed. Please try again.',
        'other_ways' => 'Other ways to reach us',
        'email_label' => 'Email',
    ],
    'blog' => [
        'title' => 'Performance Playbooks',
        'description' => 'Technical guides for Core Web Vitals, caching, bundles, and render-blocking fixes.',
        'read_more' => 'Read more',
        'back' => 'Back to blog',
    ],
];
