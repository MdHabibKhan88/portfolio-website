@extends('admin.layout')
@section('page_title', t('a_overview'))

@section('content')
<div class="row g-3 mb-4">
    @foreach($stats as $s)
        <div class="col-6 col-lg-4 col-xl">
            <div class="card-x p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="small muted">{{ t($s['key']) }}</div><div class="h2 mb-0">{{ $s['count'] }}</div></div>
                    <i class="fa-solid {{ $s['icon'] }} fa-2x accent opacity-75"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($unread > 0)
    <div class="alert alert-info">{{ t('a_unread') }}: <strong>{{ $unread }}</strong> &mdash; <a href="{{ route('admin.messages.index') }}">{{ t('a_messages') }}</a></div>
@endif

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-x p-3 h-100">
            <h2 class="h6 mb-3">{{ t('a_messages_chart') }}</h2>
            <div style="position: relative; height: 260px;"><canvas id="msgChart" aria-label="{{ t('a_messages_chart') }}" role="img"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-x p-3 h-100">
            <h2 class="h6 mb-3">{{ t('a_recent_activities') }}</h2>
            @forelse($activities as $a)
                <div class="d-flex justify-content-between border-bottom py-2 small">
                    <span>{{ $a->description }}</span>
                    <span class="muted text-nowrap ms-2">{{ $a->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="muted mb-0">{{ t('a_no_data') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        if (!window.Chart) return;
        var cs = getComputedStyle(document.documentElement);
        new Chart(document.getElementById('msgChart'), {
            type: 'bar',
            data: {
                labels: @json($chart['labels']),
                datasets: [{ label: @json(t('a_total_messages')), data: @json($chart['data']), backgroundColor: cs.getPropertyValue('--accent').trim() || '#22d3ee', borderRadius: 6 }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    })();
</script>
@endpush
