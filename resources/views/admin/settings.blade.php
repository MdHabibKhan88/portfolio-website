@extends('admin.layout')
@section('page_title', t('a_settings'))

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" data-loading class="card-x p-4" style="max-width: 900px;">
    @csrf @method('PUT')
    @include('admin._fields', ['fields' => $fields, 'model' => $model])
    <button type="submit" class="btn btn-accent"><span class="btn-label"><i class="fa-solid fa-floppy-disk me-1"></i>{{ t('a_save') }}</span><span class="btn-busy d-none"><span class="spinner-border spinner-border-sm me-2"></span>{{ t('sending') }}</span></button>
</form>
@endsection
