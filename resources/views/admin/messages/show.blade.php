@extends('admin.layout')
@section('page_title', t('a_messages'))

@section('content')
<div class="card-x p-4" style="max-width: 800px;">
    <h2 class="h5">{{ $message->subject }}</h2>
    <p class="muted small mb-3">{{ $message->name }} &lt;<a href="mailto:{{ $message->email }}">{{ $message->email }}</a>&gt; &middot; {{ $message->created_at->format('Y-m-d H:i') }}@if($message->ip_address) &middot; {{ $message->ip_address }}@endif</p>
    <div style="white-space: pre-line;">{{ $message->message }}</div>
    <div class="d-flex gap-2 mt-4">
        <a class="btn btn-accent" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}"><i class="fa-solid fa-reply me-1"></i>{{ t('a_reply') }}</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.messages.index') }}">{{ t('a_back') }}</a>
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="{{ t('a_confirm_delete') }}" class="ms-auto">@csrf @method('DELETE')
            <button class="btn btn-outline-danger" type="submit"><i class="fa-solid fa-trash me-1"></i>{{ t('a_delete') }}</button></form>
    </div>
</div>
@endsection
