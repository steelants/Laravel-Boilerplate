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
    snackbar(e.detail.message, e.detail.details || false, e.detail.type || false, e.detail.icon || false);
});
