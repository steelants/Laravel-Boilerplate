@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.users') }}</h1>
            <button class="btn btn-primary" onclick="Livewire.emit('openModal', 'user.form', '{{ __('user.create') }}')">
                <i class="nav-ico fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
            </button>
        </div>
        @livewire('user.data-table', [], key('data-table'))
    </div>
@endsection
