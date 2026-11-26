@extends('admin.layout')
@section('page_title', t($def['label']))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="muted">{{ $items->count() }}</span>
    <a href="{{ route('admin.resource.create', $resource) }}" class="btn btn-accent"><i class="fa-solid fa-plus me-1"></i>{{ t('a_add_new') }}</a>
</div>

<div class="card-x">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:70px">#</th>
                    <th>{{ t('f_title') }}</th>
                    <th>{{ t('a_status') }}</th>
                    <th class="text-end">{{ t('a_actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if(! empty($def['image']) && $item->{$def['image']})
                                <img src="{{ file_url($item->{$def['image']}) }}" alt="" width="44" height="44" style="object-fit:cover;border-radius:8px" loading="lazy">
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $item->{$def['title']} ?: '—' }}</div>
                                @if(! empty($def['sub']) && $item->{$def['sub']} !== null && $item->{$def['sub']} !== '')
                                    <div class="small muted text-break">{{ $item->{$def['sub']} instanceof \Carbon\CarbonInterface ? $item->{$def['sub']}->format('Y-m-d') : $item->{$def['sub']} }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.resource.toggle', [$resource, $item->id]) }}">
                            @csrf
                            <button class="btn btn-sm {{ $item->is_active ? 'btn-success' : 'btn-secondary' }}" type="submit" title="{{ t('a_toggle') }}">
                                {{ $item->is_active ? t('a_active') : t('a_inactive') }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end text-nowrap">
                        <form method="POST" action="{{ route('admin.resource.move', [$resource, $item->id, 'up']) }}" class="d-inline">@csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="{{ t('a_move_up') }}" @disabled($loop->first)><i class="fa-solid fa-arrow-up"></i></button></form>
                        <form method="POST" action="{{ route('admin.resource.move', [$resource, $item->id, 'down']) }}" class="d-inline">@csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit" title="{{ t('a_move_down') }}" @disabled($loop->last)><i class="fa-solid fa-arrow-down"></i></button></form>
                        <a href="{{ route('admin.resource.edit', [$resource, $item->id]) }}" class="btn btn-sm btn-outline-accent"><i class="fa-solid fa-pen"></i><span class="visually-hidden">{{ t('a_edit') }}</span></a>
                        <form method="POST" action="{{ route('admin.resource.destroy', [$resource, $item->id]) }}" class="d-inline" data-confirm="{{ t('a_confirm_delete') }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-trash"></i><span class="visually-hidden">{{ t('a_delete') }}</span></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center muted py-4">{{ t('a_no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
