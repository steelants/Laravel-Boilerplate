<div class="dropdown selectbox {{ $groupClass }}"
    x-data="initSelectboxAjax($wire, @js($property ?? null), @js($options), @js($multiple), @js($pills), null, @js($required))"
	wire:key="{{ md5(json_encode($options)) }}"
>hello
    @isset($label)
        <label class="form-label">{{ $label }}</label>
    @endisset
    <div class="{{ $class }}" {{ $attributes }}
        @click="toggleDropdown"
        data-toggle="custom"
        x-ref="el"
        data-bs-auto-close="{{ $autoclose }}"
        wire:ignore.self
    >
        @if(!empty($prefix))
            {{ $prefix }}
        @endif

        @isset($trigger)
            {{ $trigger }}
        @else
            @isset($innerLabel)
                <span class="form-label text-nowrap my-0">{{ $innerLabel }}</span>
            @else
                <span class="text-nowrap text-body-secondary" x-show="selected == null || selected.length == 0">{{ $placeholder }}&shy;</span>
            @endif

            <div class="{{ $selectedGroupClass }}">
                @if($variant == 'select' || isset($customSelected))
                    <template x-for="(option, index) in renderedOptions" :key="option.id">
                        @isset($customSelected)
                            {{ $customSelected }}
                        @else
                            <span x-text="option.name"></span>
                        @endif
                    </template>
                    @if($multiple)
                        <template x-if="selectedOptions.length > pills">
                            <span x-text="'+' + (selectedOptions.length - pills)"></span>
                        </template>
                    @endif
                @elseif($variant == 'tags')
                    <template x-for="option in selectedOptions" :key="option.id">
                        <span class="badge text-bg-light">
                            <span x-text="option.name"></span>
                            <div class="badge-addon" @click.stop="removeOption(option.id)" data-bs-toggle="collapse">
                                <i class="fas fa-times"></i>
                            </div>
                        </span>
                    </template>
                @endif
            </div>
        @endif

        @if(!empty($suffix))
            {{ $suffix }}
        @endif
    </div>
    <div class="dropdown-menu" tabindex="-1" wire:ignore.self>
        @if($searchable)
            <input class="dropdown-input" type="text" placeholder="{{ __('Search') }}..." x-model="search" x-ref="search" x-on:input.debounce="searchOptions(@js($searchable))">
        @endif
		<div class="dropdown-items" x-ref="items">
			<template x-for="(option, index) in filteredOptions" :key="option.id">
				@isset($optionTemplate)
					{{ $optionTemplate }}
				@else
					<div class="dropdown-item"
                        :class="isSelectedOption(option) ? 'selected' : ''"
						:tabindex="index"
						@click="selectOption(option)"
						@keyup.enter="selectOption(option)"
					>
						<input class="form-check-input pe-none" tabindex="-1" type="checkbox" id="{{$id}}" :checked="isSelectedOption(option)"/>

						@isset($customOption)
							{{ $customOption }}
						@else
							<span x-text="option.name"></span>
						@endif
					</div>
				@endisset
			</template>
		</div>
    </div>
</div>
