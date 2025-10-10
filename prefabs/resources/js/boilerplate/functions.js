window.setCookie = function(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

window.getCookie = function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

window.eraseCookie = function(name) {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

window.toggleDatkTheme = function(){
    if(document.getElementById('datk-theme').checked){
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        setCookie('theme', 'dark', 360);
    }else{
        document.documentElement.setAttribute('data-bs-theme', 'light');
        setCookie('theme', 'light', 360);
    }
}

window.toggleLayoutNav = function(){
    if(document.getElementById('layout-nav').classList.contains('nav-collapsed')){
        document.getElementById('layout-nav').classList.remove('nav-collapsed');
        setCookie('layout-nav', '', 360);
    }else{
        document.getElementById('layout-nav').classList.add('nav-collapsed');
        setCookie('layout-nav', 'nav-collapsed', 360);
    }
}

window.snackbar = function (message, details = false, type = false, icon = false){

    var template = `
        <div class="snackbar alert">
            <button type="button" class="btn-close btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">`;

    if(icon){
        template += `<i class="alert-ico ${icon} text-${type}"></i>`;
    }

    template += `
                <div>
                    <div class="alert-title">${message}</div>`
    if(details){
        template += `<div class="alert-text">${details}</div>`
    }

    template += `
                </div>
            </div>
        </div>`;

    document.querySelector('.snackbar-container').insertAdjacentHTML('beforeend', template);
}

window.addEventListener('snackbar', function(e){
    snackbar(e.detail[0].message, e.detail[0].details || false, e.detail[0].type || false, e.detail[0].icon || false);
});

window.copyToClipboard = function (text, el = false) {
    if (navigator.clipboard && window.isSecureContext) {
       navigator.clipboard.writeText(text).then(() => {})
            .catch(() => {snackbar('something went wrong');});
    } else {
        let textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        new Promise((res, rej) => {
            document.execCommand('copy') ? res() : rej();
            textArea.remove();
        });
    }
    snackbar('Copied to clipboard');
}

window.addEventListener('openDropdown', function(e){
    setTimeout(function(){
        openDropdown(e.detail[0].selector);
    }, 50);
});

window.openDropdown = function(selector){
    document.querySelector(selector + ' [data-bs-toggle="dropdown"]').click();
}

window.openDropdownAlpine = function (el){
    if(el.dataset.bsToggle == 'dropdown'){
        (bootstrap.Dropdown.getOrCreateInstance(el)).hide();
    }else{
        (bootstrap.Dropdown.getOrCreateInstance(el)).show();
        el.dataset.bsToggle = 'dropdown';
    }
}

window.addEventListener('hidden.bs.dropdown', event => {
    if(event.target.dataset.toggle == 'custom'){
        setTimeout(function(){
            delete event.target.dataset.bsToggle;
        }, 20);
    }
})

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
            pils: pills,
            get filteredOptions() {
                if (!this.search.trim()) return this.options;
                return this.options.filter(option => option.name.toLowerCase().includes(this.search.toLowerCase()));
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
