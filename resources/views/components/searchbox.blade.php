<div {{ $attributes }} class="dropdown" x-data="{
    search: '',
    selectedId: @entangle($property).live,
    options: @js(array_values($options)),
    get filteredOptions() {
        if (!this.search.trim()) return this.options;
        return this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
    },
    get selectedOption(){
        if(this.selectedId.length == 0){
            return null;
        }else if(this.selectedId.length <= 1){
            return this.options.filter(option => this.selectedId.includes(option.id)).map(option => option.name).join(', ');
        }else{
            return this.options.filter(option => this.selectedId.includes(option.id))[0].name + ', +'+(this.selectedId.length-1);
        }
    },
    isSelectedOption(option) {
        return this.selectedId.includes(option.id);
    },
    selectOption(option) {
        if(!this.selectedId.includes(option.id)){
            this.selectedId.push(option.id);
        }else{
            this.selectedId.splice(this.selectedId.indexOf(option.id), 1);
        }
    }
}">
    <div data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="{{ $autoclose }}" wire:ignore.self>
        @if(isset($trigger) && !$trigger->isEmpty())
            {{ $trigger }}
        @else
            <button class="btn border" type="button">
                <span>{{ $label }}</span>
                <span class="badge text-bg-light" x-text="selectedOption"></span>
                <i class="fa fa-chevron-down ms-2"></i>
            </button>
        @endif
    </div>
    <div class="dropdown-menu" wire:ignore.self>
        <input class="dropdown-input" type="text" placeholder="{{ __('Search...') }}" x-model="search">
        <template x-for="option in filteredOptions" :key="option.id">
            <label class="dropdown-item">
                <input class="form-check-input" type="checkbox" x-bind:checked="isSelectedOption(option)" @change="selectOption(option)">
                <span x-text="option.name"></span>
            </label>
        </template>
    </div>
</div>
