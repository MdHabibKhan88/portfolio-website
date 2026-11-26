@extends('admin.layout')
@section('page_title', t($def['label']).' — '.($model->exists ? t('a_edit') : t('a_add_new')))

@section('content')
<form method="POST" enctype="multipart/form-data" data-loading class="card-x p-4" style="max-width: 900px;"
      action="{{ $model->exists ? route('admin.resource.update', [$resource, $model->id]) : route('admin.resource.store', $resource) }}">
    @csrf
    @if($model->exists) @method('PUT') @endif

    @include('admin._fields', ['fields' => $def['fields'], 'model' => $model, 'options' => $options])

    <input type="hidden" name="is_active" value="0">
    <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $model->exists ? $model->is_active : true))>
        <label class="form-check-label" for="is_active">{{ t('f_is_active') }}</label>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-accent"><span class="btn-label"><i class="fa-solid fa-floppy-disk me-1"></i>{{ t('a_save') }}</span><span class="btn-busy d-none"><span class="spinner-border spinner-border-sm me-2"></span>{{ t('sending') }}</span></button>
        <a href="{{ route('admin.resource.index', $resource) }}" class="btn btn-outline-secondary">{{ t('a_cancel') }}</a>
    </div>
</form>
@endsection
