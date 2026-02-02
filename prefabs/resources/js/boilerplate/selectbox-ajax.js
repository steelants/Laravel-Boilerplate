window.initSelectboxAjax = function($wire, property, options, multiple, pills, selected = null, required = false){
    if(multiple){
        selected = selected || [];
        return {
            search: '',
            selected: property ? $wire.entangle(property).live : selected,
            options: options,
            filteredOptions: options,
            pills: pills,
            async searchOptions(searchable) {
                if (!this.search.trim()){
                    this.resetOptions();
                    return;
                }

                if(typeof searchable == 'string'){
                    this.filteredOptions = await $wire.getOptions(this.search);

                    this.options = Array.from([...this.options, ...this.filteredOptions]
                        .reduce((m, o) => m.set(o.id, o), new Map)
                        .values()
                    );
                }else{
                    this.filteredOptions = this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
                }
			},
            get selectedOptionsText(){
                if(this.selected == null) return '';

                if(this.selected.length == 0){
                    return '';
                }else if(this.selected.length <= pills){
                    return this.selectedOptions.map(option => option.name).join(', ');
                }else{
                    return this.selectedOptions.slice(0, pills).map(option => option.name).join(', ') + ', +'+(this.selected.length - pills);
                }
            },
            get renderedOptions(){
                if(this.selected == null) return [];
                return this.selectedOptions.slice(0, pills);
            },
            get selectedOptions(){
                if(this.selected == null) return [];
                if(this.selected.length == 0){
                    return [];
                }else{
                    return this.options.filter(option => this.selected.includes(option.id));
                }
            },
            isSelectedOption(option) {
                if(this.selected == null) return false;
                return this.selected.includes(option.id);
            },
            selectOption(option) {
                if(!this.selected.includes(option.id)){
                    this.selected.push(option.id);
                }else{
                    this.selected.splice(this.selected.indexOf(option.id), 1);
                }
            },
            removeOption(optionId) {
                this.selected.splice(this.selected.indexOf(optionId), 1);
            },
            resetOptions(){
                this.filteredOptions = Array.from([...options, ...this.options]
                    .reduce((m, o) => m.set(o.id, o), new Map)
                    .values()
                );
            },
            toggleDropdown(){
                if(this.$refs.el.dataset.bsToggle == 'dropdown'){
                    (bootstrap.Dropdown.getOrCreateInstance(this.$refs.el)).hide();
                }else{
                    (bootstrap.Dropdown.getOrCreateInstance(this.$refs.el)).show();
                    this.resetOptions();
                    this.search = '';
                    this.$refs.el.dataset.bsToggle = 'dropdown';
                    scrollToSelectedChild(this.$refs.items);
                    this.$refs.search?.focus();
                }
            }
        }
    }else{
        return {
            search: '',
            selected: property ? $wire.entangle(property) : selected,
            options: options,
            filteredOptions: options,
			async searchOptions(searchable) {
                if (!this.search.trim()){
                    this.resetOptions();
                    return;
                }

                if(typeof searchable == 'string'){
                    this.filteredOptions = await $wire.getOptions(this.search);

                    this.options = Array.from([...this.options, ...this.filteredOptions]
                        .reduce((m, o) => m.set(o.id, o), new Map)
                        .values()
                    );
                }else{
                    this.filteredOptions = this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
                }
			},
            pils: pills,
            get selectedOptionsText(){
                let selected = this.selectedOptions;
                return selected == null ? '' : selected.name;
            },
            get renderedOptions(){
                let selected = this.selectedOptions;
                return selected == null ? [] : [selected];
            },
            get selectedOptions(){
                return this.selected == null ? null : this.options.find(option => option.id == this.selected);
            },
            isSelectedOption(option) {
                if(this.selected == null) return false;
                return this.selected === option.id;
            },
            selectOption(option) {
                if(this.selected !== option.id){
                    this.selected = option.id;
                }else if(!required){
                    this.selected = null;
                }
            },
            removeOption(optionId) {
                this.selected = null;
            },
            resetOptions(){
                this.filteredOptions = Array.from([...options, ...this.options]
                    .reduce((m, o) => m.set(o.id, o), new Map)
                    .values()
                );
            },
            toggleDropdown(){
                if(this.$refs.el.dataset.bsToggle == 'dropdown'){
                    (bootstrap.Dropdown.getOrCreateInstance(this.$refs.el)).hide();
                }else{
                    (bootstrap.Dropdown.getOrCreateInstance(this.$refs.el)).show();
                    this.resetOptions();
                    this.search = '';
                    this.$refs.el.dataset.bsToggle = 'dropdown';
                    scrollToSelectedChild(this.$refs.items);
                    this.$refs.search?.focus();
                }
            }
        }
    }
}
window.scrollToSelectedChild = function(parentElement) {
    if (!parentElement) return;

    const selectedChild = parentElement.querySelector(':scope .selected');

    if (selectedChild) {
        selectedChild.scrollIntoView({
            behavior: 'instant',
            block: 'center',
            inline: 'center'
        });
    }
}
