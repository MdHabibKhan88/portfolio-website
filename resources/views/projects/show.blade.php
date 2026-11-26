@extends('layouts.app')

@section('title', $project->tr('title').' | '.setting('site_title', 'MD. HABIB KHAN'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) ($project->tr('short_description') ?: $project->tr('description'))), 160))
@section('og_image', file_url($project->image) ?? '')

@section('content')
<section class="section pt-5 mt-5">
    <div class="container" style="max-width: 920px;">
        <a href="{{ route('home') }}#projects" class="btn btn-sm btn-outline-accent mb-4"><i class="fa-solid fa-arrow-left me-2"></i>{{ t('back_to_projects') }}</a>

        <article class="reveal">
            @if($project->image)
                <img src="{{ file_url($project->image) }}" alt="{{ $project->tr('title') }}" class="img-fluid rounded-4 w-100 mb-4 project-hero-img" loading="lazy">
            @else
                <div class="project-placeholder rounded-4 mb-4" role="img" aria-label="{{ $project->tr('title') }}"><i class="fa-solid fa-diagram-project"></i></div>
            @endif

            <h1 class="fw-bold">{{ $project->tr('title') }}</h1>
            <div class="d-flex flex-wrap gap-2 my-3">
                @if($project->category)<span class="badge badge-accent">{{ $project->category }}</span>@endif
                @if($project->is_featured)<span class="badge badge-accent"><i class="fa-solid fa-star me-1"></i>{{ t('featured') }}</span>@endif
            </div>

            <p class="lead" style="white-space: pre-line;">{{ $project->tr('description') ?: $project->tr('short_description') }}</p>

            @if($project->techList())
                <h2 class="h5 mt-4">{{ t('technologies') }}</h2>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach($project->techList() as $tech)<span class="chip">{{ $tech }}</span>@endforeach
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2">
                @if($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-accent"><i class="fa-brands fa-github me-2"></i>{{ t('github') }}</a>
                @endif
                @if($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-accent"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>{{ t('live_demo') }}</a>
                @endif
            </div>
        </article>
    </div>
</section>
@endsection
