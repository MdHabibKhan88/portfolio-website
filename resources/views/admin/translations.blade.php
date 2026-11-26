@extends('admin.layout')
@section('page_title', t('a_language_content'))

@section('content')
<p class="muted">{{ t('a_translations_help') }}</p>
<input type="search" class="form-control mb-3" style="max-width: 360px;" placeholder="{{ t('a_search') }}" data-filter-table="#trTable">

<form method="POST" action="{{ route('admin.translations.update') }}" data-loading>
    @csrf @method('PUT')
    <div class="card-x">
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="trTable">
                <thead><tr><th>Key</th><th>English</th><th>বাংলা</th></tr></thead>
                <tbody>
                @foreach($en as $key => $defaultEn)
                    <tr>
                        <td class="small text-break"><code>{{ $key }}</code></td>
                        <td><input type="text" class="form-control form-control-sm" name="en[{{ $key }}]" maxlength="1000"
                                   value="{{ old('en.'.$key, $overrides[$key]->en ?? '') }}" placeholder="{{ $defaultEn }}"></td>
                        <td><input type="text" class="form-control form-control-sm" name="bn[{{ $key }}]" maxlength="1000"
                                   value="{{ old('bn.'.$key, $overrides[$key]->bn ?? '') }}" placeholder="{{ $bn[$key] ?? '' }}"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <button type="submit" class="btn btn-accent mt-3"><span class="btn-label"><i class="fa-solid fa-floppy-disk me-1"></i>{{ t('a_save') }}</span><span class="btn-busy d-none"><span class="spinner-border spinner-border-sm me-2"></span>{{ t('sending') }}</span></button>
</form>
@endsection
