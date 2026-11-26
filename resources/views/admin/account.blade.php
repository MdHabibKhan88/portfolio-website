@extends('admin.layout')
@section('page_title', t('a_account'))

@section('content')
<div class="row g-4" style="max-width: 1000px;">
    <div class="col-md-6">
        <form method="POST" action="{{ route('admin.account.update') }}" class="card-x p-4" data-loading>
            @csrf @method('PUT')
            <h2 class="h5 mb-3">{{ t('a_account_details') }}</h2>
            <div class="mb-3"><label class="form-label" for="name">{{ t('f_name') }}</label>
                <input id="name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required maxlength="100"></div>
            <div class="mb-3"><label class="form-label" for="email">{{ t('f_email') }}</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required maxlength="150"></div>
            <button class="btn btn-accent" type="submit">{{ t('a_save') }}</button>
        </form>
    </div>
    <div class="col-md-6">
        <form method="POST" action="{{ route('admin.account.password') }}" class="card-x p-4" data-loading>
            @csrf @method('PUT')
            <h2 class="h5 mb-3">{{ t('a_change_password') }}</h2>
            <div class="mb-3"><label class="form-label" for="current_password">{{ t('a_current_password') }}</label>
                <input id="current_password" type="password" name="current_password" class="form-control" required autocomplete="current-password"></div>
            <div class="mb-3"><label class="form-label" for="password">{{ t('a_new_password') }}</label>
                <input id="password" type="password" name="password" class="form-control" required minlength="10" autocomplete="new-password"></div>
            <div class="mb-3"><label class="form-label" for="password_confirmation">{{ t('a_confirm_password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
            <button class="btn btn-accent" type="submit">{{ t('a_update_password') }}</button>
        </form>
    </div>
</div>
@endsection
