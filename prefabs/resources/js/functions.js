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

window.toggleLayoutNav = function(){
    if(document.getElementById('layout-nav').classList.contains('nav-collapsed')){
        document.getElementById('layout-nav').classList.remove('nav-collapsed');
        setCookie('layout-nav', '', 360);
    }else{
        document.getElementById('layout-nav').classList.add('nav-collapsed');
        setCookie('layout-nav', 'nav-collapsed', 360);
    }
}


window.toggleSystemNav = function(){
    if(document.getElementById('nav-system').classList.contains('nav-collapsed')){
        document.getElementById('nav-system').classList.remove('nav-collapsed');
        setCookie('nav-system', '', 360);
    }else{
        document.getElementById('nav-system').classList.add('nav-collapsed');
        setCookie('nav-system', 'open', 360);
    }
}

window.snackbar = function (message, details = false, type = false, icon = false){

    var template = `
        <div class="snackbar alert border-0">
            <button type="button" class="btn-close btn-close-white close ${details ? '' : 'mt-2'}" data-bs-dismiss="alert"></button>

            <div class="alert-content">`;

    if(icon){
        template += `<i class="alert-ico ${icon} text-${type}"></i>`;
    }

    template += `
                <div>
                    <div class="alert-title ${details ? '' : 'mt-2'}">${message}</div>`
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
