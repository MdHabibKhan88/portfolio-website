@extends('admin.layout')
@section('page_title', t('a_messages'))

@section('content')
<div class="card-x">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th></th><th>{{ t('f_name') }}</th><th>{{ t('f_subject') }}</th><th>{{ t('a_date') }}</th><th class="text-end">{{ t('a_actions') }}</th></tr></thead>
            <tbody>
            @forelse($messages as $m)
                <tr class="{{ $m->is_read ? '' : 'fw-bold' }}">
                    <td>@unless($m->is_read)<span class="badge bg-info text-dark">{{ t('a_new') }}</span>@endunless</td>
                    <td>{{ $m->name }}<div class="small muted fw-normal">{{ $m->email }}</div></td>
                    <td><a href="{{ route('admin.messages.show', $m) }}">{{ \Illuminate\Support\Str::limit($m->subject, 60) }}</a></td>
                    <td class="text-nowrap small">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                    <td class="text-end text-nowrap">
                        <form method="POST" action="{{ route('admin.messages.read', $m) }}" class="d-inline">@csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="{{ t('a_mark_read') }}"><i class="fa-solid {{ $m->is_read ? 'fa-envelope' : 'fa-envelope-open' }}"></i></button></form>
                        <a class="btn btn-sm btn-outline-accent" href="{{ route('admin.messages.show', $m) }}"><i class="fa-solid fa-eye"></i><span class="visually-hidden">{{ t('view_details') }}</span></a>
                        <form method="POST" action="{{ route('admin.messages.destroy', $m) }}" class="d-inline" data-confirm="{{ t('a_confirm_delete') }}">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-trash"></i><span class="visually-hidden">{{ t('a_delete') }}</span></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center muted py-4">{{ t('a_no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $messages->links() }}</div>
@endsection
