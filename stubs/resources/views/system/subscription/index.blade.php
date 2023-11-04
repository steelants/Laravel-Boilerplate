@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::subscriptions.title') }}</h1>

            <button class="btn btn-primary" onclick="Livewire.emit('openModal', 'subscription.form', '{{ __('boilerplate::subscriptions.create') }}')">
                <i class="me-2 fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
            </button>
        </div>

        @livewire('subscription.data-table', [], key('data-table'))
    </div>
@endsection
