@extends('layouts.marketing')

@push('head')
<!-- Prism.js for code syntax highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css">
<style>
    .article-content h2 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: #fff;
        scroll-margin-top: 2rem;
    }
    .article-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
        color: #e2e8f0;
        scroll-margin-top: 2rem;
    }
    .article-content p {
        margin-bottom: 1.25rem;
        line-height: 1.75;
    }
    .article-content ul, .article-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .article-content ul {
        list-style: disc;
    }
    .article-content ol {
        list-style: decimal;
    }
    .article-content li {
        margin-bottom: 0.5rem;
        line-height: 1.75;
    }
    .article-content code.inline-code {
        background: #2d2d2d;
        color: #ccc;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.85em;
        font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
        border: 1px solid #404040;
        white-space: nowrap;
    }
    /* Inline code syntax highlighting (matches Prism tomorrow theme) */
    .inline-code .hl-punct { color: #ccc; }
    .inline-code .hl-tag { color: #e2777a; }
    .inline-code .hl-attr { color: #d19a66; }
    .inline-code .hl-string { color: #98c379; }
    .article-content code {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-family: 'Courier New', monospace;
    }
    .article-content pre {
        margin: 1.5rem 0;
        border-radius: 0.5rem;
        overflow-x: auto;
    }
    .article-content pre code {
        background: transparent;
        padding: 0;
    }
    .article-content a {
        color: #67e8f9;
        text-decoration: underline;
    }
    .article-content a:hover {
        color: #a5f3fc;
    }
    .article-content strong {
        font-weight: 600;
        color: #fff;
    }
    .toc-link {
        transition: all 0.2s;
    }
    .toc-link:hover {
        color: #67e8f9;
    }
</style>
@endpush

@section('content')
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            <div class="space-y-2">
                <a href="{{ route('marketing.blog.index', app()->getLocale()) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-200 hover:text-white">
                    ← {{ __('marketing.blog.back') }}
                </a>
                <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $post['category'] }} · {{ $post['reading_time'] }}</p>
                <h1 class="text-4xl font-bold text-white">{{ $content['title'] ?? $post['slug'] }}</h1>
            </div>

            <article class="article-content text-slate-200 leading-relaxed">
                {!! $content['body'] ?? '' !!}
            </article>
        </div>

        <!-- Table of Contents Sidebar -->
        @if(!empty($toc))
        <aside class="lg:col-span-4">
            <div class="lg:sticky lg:top-6 space-y-4">
                <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-lg p-6">
                    <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wide mb-4">Table of Contents</h2>
                    <nav class="space-y-2">
                        @foreach($toc as $item)
                            <a href="#{{ $item['id'] }}"
                               class="toc-link block text-sm text-slate-400 hover:text-cyan-300 {{ $item['level'] === 3 ? 'ml-4' : '' }}">
                                {{ $item['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </aside>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<!-- Prism.js for code syntax highlighting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
<script>
    // Add line-numbers class to all pre elements
    document.querySelectorAll('pre').forEach(pre => {
        pre.classList.add('line-numbers');
    });
</script>
@endpush
