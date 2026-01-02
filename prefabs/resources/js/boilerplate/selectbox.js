window.initSelectbox = function($wire, property, options, multiple, pills, selected = null, required = false){
    if(multiple){
        selected = selected || [];
        return {
            search: '',
            selected: property ? $wire.entangle(property).live : selected,
            options: options,
            pills: pills,
            get filteredOptions() {
                if (!this.search.trim()) return this.options;
                return this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
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
            }
        }
    }else{
        return {
            search: '',
            selected: property ? $wire.entangle(property).live : selected,
            options: options,
			async searchOptions() {
				this.options = await $wire.getOptions(this.search);
				console.log(this.selectedOptions, this.options);
			},
            pils: pills,
            filteredOptions() {
				return this.options;
                // if (!this.search.trim()) return this.options;
                // return this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
            },
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
            }
        }
    }
}
