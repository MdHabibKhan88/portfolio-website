@extends('layouts.app')

@section('title', ($profile->tr('name') ?: 'MD. HABIB KHAN').' | '.(setting('site_title') ?: 'Portfolio'))

@section('content')
@php
    $words = array_values(array_filter(explode(' ', preg_replace('/[^A-Za-z ]/', '', $profile->name_en ?: 'MD HABIB KHAN'))));
    $initials = implode('', array_map(fn ($w) => $w[0], array_slice($words, -2)));
    $photo = file_url($profile->photo);
@endphp

{{-- ============ HERO ============ --}}
<section id="home" class="hero d-flex align-items-center">
    <div class="container">
        @if(session('cv_missing'))
            <div class="alert alert-warning" role="alert">{{ t('cv_unavailable') }}</div>
        @endif
        <div class="row align-items-center g-5">
            <div class="col-lg-7 order-2 order-lg-1 reveal">
                <p class="eyebrow">{{ t('hello') }}</p>
                <h1 class="display-4 fw-bold hero-name">{{ $profile->tr('name') ?: 'MD. HABIB KHAN' }}</h1>
                <h2 class="h5 headline">{{ $profile->tr('headline') }}</h2>
                <p class="lead muted mt-3">{{ $profile->tr('intro') }}</p>
                <div class="d-flex flex-wrap gap-2 my-4">
                    <a href="{{ route('cv.download') }}" class="btn btn-accent btn-lg"><i class="fa-solid fa-download me-2"></i>{{ t('btn_download_cv') }}</a>
                    <a href="#projects" class="btn btn-outline-accent btn-lg"><i class="fa-solid fa-diagram-project me-2"></i>{{ t('btn_view_projects') }}</a>
                    <a href="#contact" class="btn btn-outline-accent btn-lg"><i class="fa-solid fa-paper-plane me-2"></i>{{ t('btn_contact_me') }}</a>
                </div>
                @include('partials.social', ['links' => $socialLinks])
            </div>
            <div class="col-lg-5 order-1 order-lg-2 text-center reveal">
                @if($photo)
                    <img src="{{ $photo }}" alt="{{ $profile->tr('name') }}" class="profile-img" width="340" height="340">
                @else
                    <div class="profile-img placeholder-img" role="img" aria-label="{{ $profile->tr('name') }}">{{ $initials }}</div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ============ ABOUT ============ --}}
<section id="about" class="section">
    <div class="container">
        @include('partials.section-title', ['title' => t('about_title')])
        <div class="row g-4">
            <div class="col-lg-7 reveal">
                <p class="lead" style="white-space: pre-line;">{{ $profile->tr('about') }}</p>
                @foreach(['academic' => 'academic_background', 'interests' => 'interests', 'objectives' => 'objectives'] as $field => $label)
                    @if($profile->tr($field))
                        <h3 class="h5 accent mt-4">{{ t($label) }}</h3>
                        <p style="white-space: pre-line;">{{ $profile->tr($field) }}</p>
                    @endif
                @endforeach
            </div>
            <div class="col-lg-5 reveal">
                <div class="card-x p-4">
                    <h3 class="h5 mb-3">{{ t('profile_details') }}</h3>
                    <dl class="mb-0 detail-list">
                        @foreach([
                            ['fa-user', 'f_name', $profile->tr('name')],
                            ['fa-building-columns', 'f_university', $profile->tr('university')],
                            ['fa-laptop-code', 'f_department', $profile->tr('department')],
                            ['fa-location-dot', 'f_location', $profile->tr('location')],
                            ['fa-envelope', 'f_email', $profile->email],
                            ['fa-phone', 'f_phone', $profile->phone],
                        ] as [$icon, $label, $value])
                            @if($value)
                                <div class="d-flex gap-3 mb-3">
                                    <i class="fa-solid {{ $icon }} accent mt-1"></i>
                                    <div><dt class="small muted">{{ t($label) }}</dt><dd class="mb-0">{{ $value }}</dd></div>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ SKILLS ============ --}}
<section id="skills" class="section alt">
    <div class="container">
        @include('partials.section-title', ['title' => t('skills_title')])
        @php $shown = $categories->filter(fn ($c) => $c->skills->isNotEmpty()); @endphp
        @if($shown->isEmpty())
            @include('partials.empty')
        @else
            <div class="row g-4">
                @foreach($shown as $category)
                    <div class="col-md-6 col-xl-4 reveal">
                        <div class="card-x p-4 h-100">
                            <h3 class="h5 mb-4"><i class="{{ $category->icon ?: 'fa-solid fa-code' }} accent me-2"></i>{{ $category->tr('name') }}</h3>
                            @foreach($category->skills as $skill)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-semibold">{{ $skill->name }}</span>
                                        @if($skill->level !== null)<span class="muted">{{ $skill->level }}%</span>@endif
                                    </div>
                                    @if($skill->level !== null)
                                        <div class="progress skill-bar" role="progressbar" aria-label="{{ $skill->name }}" aria-valuenow="{{ $skill->level }}" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar" style="width: {{ (int) $skill->level }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ PROJECTS ============ --}}
<section id="projects" class="section">
    <div class="container">
        @include('partials.section-title', ['title' => t('projects_title')])
        @if($projects->isEmpty())
            @include('partials.empty')
        @else
            <div class="row g-4">
                @foreach($projects as $project)
                    <div class="col-md-6 col-xl-4 reveal">
                        <article class="card-x project-card h-100 d-flex flex-column">
                            <a href="{{ route('projects.show', $project->slug) }}" class="project-thumb" tabindex="-1" aria-hidden="true">
                                @if($project->image)
                                    <img src="{{ file_url($project->image) }}" alt="{{ $project->tr('title') }}" loading="lazy">
                                @else
                                    <div class="project-placeholder"><i class="fa-solid fa-diagram-project"></i></div>
                                @endif
                            </a>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @if($project->category)<span class="badge badge-accent">{{ $project->category }}</span>@endif
                                    @if($project->is_featured)<span class="badge badge-accent"><i class="fa-solid fa-star me-1"></i>{{ t('featured') }}</span>@endif
                                </div>
                                <h3 class="h5"><a href="{{ route('projects.show', $project->slug) }}" class="stretched-link-plain">{{ $project->tr('title') }}</a></h3>
                                <p class="muted flex-grow-1">{{ \Illuminate\Support\Str::limit($project->tr('short_description') ?: $project->tr('description'), 140) }}</p>
                                @if($project->techList())
                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        @foreach($project->techList() as $tech)<span class="chip">{{ $tech }}</span>@endforeach
                                    </div>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mt-auto">
                                    <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-sm btn-accent">{{ t('view_details') }}</a>
                                    @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-accent"><i class="fa-brands fa-github me-1"></i>{{ t('github') }}</a>@endif
                                    @if($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-accent"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>{{ t('live_demo') }}</a>@endif
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ EDUCATION ============ --}}
<section id="education" class="section alt">
    <div class="container" style="max-width: 860px;">
        @include('partials.section-title', ['title' => t('education_title')])
        @if($education->isEmpty())
            @include('partials.empty')
        @else
            <div class="timeline">
                @foreach($education as $item)
                    <div class="timeline-item reveal">
                        <div class="card-x p-4">
                            <h3 class="h5 mb-1">{{ $item->tr('institution') }}</h3>
                            <p class="accent mb-1">{{ $item->tr('degree') }}@if($item->tr('field')) &middot; {{ $item->tr('field') }}@endif</p>
                            @if($item->start_year || $item->end_year || $item->is_current)
                                <p class="small muted mb-2">
                                    {{ $item->start_year }}@if($item->start_year && ($item->end_year || $item->is_current)) &ndash; @endif{{ $item->is_current ? t('present') : $item->end_year }}
                                </p>
                            @endif
                            @if($item->tr('description'))<p class="mb-0" style="white-space: pre-line;">{{ $item->tr('description') }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ EXPERIENCE ============ --}}
<section id="experience" class="section">
    <div class="container" style="max-width: 860px;">
        @include('partials.section-title', ['title' => t('experience_title')])
        @if($experiences->isEmpty())
            @include('partials.empty')
        @else
            <div class="timeline">
                @foreach($experiences as $item)
                    <div class="timeline-item reveal">
                        <div class="card-x p-4">
                            <h3 class="h5 mb-1">{{ $item->tr('title') }}</h3>
                            <p class="accent mb-1">{{ $item->tr('organization') }}@if($item->location) &middot; {{ $item->location }}@endif</p>
                            @if($item->start_date || $item->is_current)
                                <p class="small muted mb-2">
                                    {{ $item->start_date?->format('M Y') }}@if($item->start_date) &ndash; @endif{{ $item->is_current ? t('present') : $item->end_date?->format('M Y') }}
                                </p>
                            @endif
                            @if($item->tr('description'))<p class="mb-0" style="white-space: pre-line;">{{ $item->tr('description') }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ VOLUNTEERING ============ --}}
<section id="volunteering" class="section alt">
    <div class="container">
        @include('partials.section-title', ['title' => t('volunteering_title')])
        @if($volunteers->isEmpty())
            @include('partials.empty')
        @else
            <div class="row g-4 justify-content-center">
                @foreach($volunteers as $v)
                    <div class="col-md-6 col-xl-4 reveal">
                        <div class="card-x p-4 h-100 text-center">
                            @if($v->image)
                                <img src="{{ file_url($v->image) }}" alt="{{ $v->tr('organization') }}" class="vol-img mb-3" loading="lazy">
                            @else
                                <div class="vol-icon mb-3"><i class="fa-solid fa-hand-holding-heart"></i></div>
                            @endif
                            <h3 class="h5">{{ $v->tr('organization') }}</h3>
                            @if($v->tr('role'))<p class="accent mb-1">{{ $v->tr('role') }}</p>@endif
                            @if($v->period)<p class="small muted mb-2">{{ $v->period }}</p>@endif
                            @if($v->tr('description'))<p class="mb-0" style="white-space: pre-line;">{{ $v->tr('description') }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ CERTIFICATES ============ --}}
<section id="certificates" class="section">
    <div class="container">
        @include('partials.section-title', ['title' => t('certificates_title')])
        @if($certificates->isEmpty())
            @include('partials.empty')
        @else
            <div class="row g-4">
                @foreach($certificates as $c)
                    @php $isPdf = $c->file && \Illuminate\Support\Str::endsWith(strtolower($c->file), '.pdf'); @endphp
                    <div class="col-md-6 col-xl-4 reveal">
                        <div class="card-x h-100 d-flex flex-column">
                            @if($c->file && ! $isPdf)
                                <a href="{{ file_url($c->file) }}" target="_blank" rel="noopener noreferrer" class="project-thumb">
                                    <img src="{{ file_url($c->file) }}" alt="{{ $c->tr('title') }}" loading="lazy">
                                </a>
                            @else
                                <div class="project-placeholder"><i class="fa-solid {{ $isPdf ? 'fa-file-pdf' : 'fa-certificate' }}"></i></div>
                            @endif
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h3 class="h5">{{ $c->tr('title') }}</h3>
                                @if($c->tr('issuer'))<p class="accent mb-1">{{ $c->tr('issuer') }}</p>@endif
                                @if($c->issue_date)<p class="small muted mb-2">{{ $c->issue_date->format('M Y') }}</p>@endif
                                @if($c->tr('description'))<p class="muted">{{ $c->tr('description') }}</p>@endif
                                @if($c->file)
                                    <a href="{{ file_url($c->file) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-accent mt-auto align-self-start"><i class="fa-solid fa-eye me-1"></i>{{ t('view_file') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ ACHIEVEMENTS ============ --}}
<section id="achievements" class="section alt">
    <div class="container" style="max-width: 860px;">
        @include('partials.section-title', ['title' => t('achievements_title')])
        @if($achievements->isEmpty())
            @include('partials.empty')
        @else
            <div class="timeline">
                @foreach($achievements as $a)
                    <div class="timeline-item reveal">
                        <div class="card-x p-4">
                            <h3 class="h5 mb-1"><i class="fa-solid fa-trophy accent me-2"></i>{{ $a->tr('title') }}</h3>
                            @if($a->achieved_on)<p class="small muted mb-2">{{ $a->achieved_on->format('M Y') }}</p>@endif
                            @if($a->tr('description'))<p class="mb-0" style="white-space: pre-line;">{{ $a->tr('description') }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ GALLERY (only when items exist) ============ --}}
@if($gallery->isNotEmpty())
<section id="gallery" class="section">
    <div class="container">
        @include('partials.section-title', ['title' => t('gallery_title')])
        <div class="row g-3">
            @foreach($gallery as $g)
                <div class="col-6 col-md-4 col-lg-3 reveal">
                    <a href="{{ file_url($g->image) }}" target="_blank" rel="noopener noreferrer" class="gallery-item d-block">
                        @if($g->image)
                            <img src="{{ file_url($g->image) }}" alt="{{ $g->tr('title') }}" loading="lazy" class="w-100">
                        @else
                            <div class="project-placeholder"><i class="fa-solid fa-image"></i></div>
                        @endif
                        <span class="small d-block mt-1 muted">{{ $g->tr('caption') ?: $g->tr('title') }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ CONTACT ============ --}}
<section id="contact" class="section alt">
    <div class="container" style="max-width: 980px;">
        @include('partials.section-title', ['title' => t('contact_title')])
        <div class="row g-4">
            <div class="col-lg-5 reveal">
                <p class="lead">{{ t('contact_intro') }}</p>
                @if($profile->email)
                    <p><i class="fa-solid fa-envelope accent me-2"></i><a href="mailto:{{ $profile->email }}" class="footer-link">{{ $profile->email }}</a></p>
                @endif
                @include('partials.social', ['links' => $socialLinks])
            </div>
            <div class="col-lg-7 reveal">
                @if(session('contact_success'))
                    <div class="alert alert-success" role="status"><i class="fa-solid fa-circle-check me-2"></i>{{ t('contact_success') }}</div>
                @endif
                @if($errors->contact->any())
                    <div class="alert alert-danger" role="alert"><i class="fa-solid fa-circle-exclamation me-2"></i>{{ t('contact_failed') }}</div>
                @endif
                <form method="POST" action="{{ route('contact.store') }}" class="card-x p-4" data-loading novalidate>
                    @csrf
                    <div class="d-none" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="c_name" class="form-label">{{ t('f_name') }}</label>
                            <input type="text" id="c_name" name="name" value="{{ old('name') }}" maxlength="100" required class="form-control @if($errors->contact->has('name')) is-invalid @endif">
                            @if($errors->contact->has('name'))<div class="invalid-feedback">{{ $errors->contact->first('name') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label for="c_email" class="form-label">{{ t('f_email') }}</label>
                            <input type="email" id="c_email" name="email" value="{{ old('email') }}" maxlength="150" required class="form-control @if($errors->contact->has('email')) is-invalid @endif">
                            @if($errors->contact->has('email'))<div class="invalid-feedback">{{ $errors->contact->first('email') }}</div>@endif
                        </div>
                        <div class="col-12">
                            <label for="c_subject" class="form-label">{{ t('f_subject') }}</label>
                            <input type="text" id="c_subject" name="subject" value="{{ old('subject') }}" maxlength="150" required class="form-control @if($errors->contact->has('subject')) is-invalid @endif">
                            @if($errors->contact->has('subject'))<div class="invalid-feedback">{{ $errors->contact->first('subject') }}</div>@endif
                        </div>
                        <div class="col-12">
                            <label for="c_message" class="form-label">{{ t('f_message') }}</label>
                            <textarea id="c_message" name="message" rows="5" maxlength="3000" required class="form-control @if($errors->contact->has('message')) is-invalid @endif">{{ old('message') }}</textarea>
                            @if($errors->contact->has('message'))<div class="invalid-feedback">{{ $errors->contact->first('message') }}</div>@endif
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-accent btn-lg">
                                <span class="btn-label"><i class="fa-solid fa-paper-plane me-2"></i>{{ t('send_message') }}</span>
                                <span class="btn-busy d-none"><span class="spinner-border spinner-border-sm me-2"></span>{{ t('sending') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
