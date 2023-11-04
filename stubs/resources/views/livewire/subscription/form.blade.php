<div>
    <form wire:submit.prevent="{{$action}}" method="post">
        
        <div class="mb-3">
            <label class="form-label">{{ __('boilerplate::subscriptions.tier') }}</label>
            <select class="form-select" wire:model="tier" required>
                <option value="" hidden>{{ __('boilerplate::ui.select-value') }}</option>
                @foreach ($tiers as $value => $name)
                    <option value="{{$value}}">{{$name}}</option>
                @endforeach
            </select>
        </div>

        <x-form-input type="date" id="subscription-valid_to" name="valid_to" livewireModel="valid_to" label="{{ __('boilerplate::ui.valid_to') }}" required="true"/>

        @if ($action == 'update')
            <x-form-submit text="{{ __('boilerplate::ui.update') }}" />
        @else
            <x-form-submit text="{{ __('boilerplate::ui.add') }}" />
        @endif
    </form>
</div>