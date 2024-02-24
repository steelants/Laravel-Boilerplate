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

window.loadQuill = function(){
    Quill.register({
        'modules/tableUI': quillTableUI.default
    }, true);

    // Quill.register(Quill.import('attributors/attribute/direction'), true);
    // Quill.register(Quill.import('attributors/class/align'), true);
    // Quill.register(Quill.import('attributors/class/background'), true);
    // Quill.register(Quill.import('attributors/class/color'), true);
    // Quill.register(Quill.import('attributors/class/direction'), true);
    // Quill.register(Quill.import('attributors/class/font'), true);
    // Quill.register(Quill.import('attributors/class/size'), true);
    Quill.register(Quill.import('attributors/style/align'), true);
    Quill.register(Quill.import('attributors/style/background'), true);
    Quill.register(Quill.import('attributors/style/color'), true);
    Quill.register(Quill.import('attributors/style/direction'), true);
    Quill.register(Quill.import('attributors/style/font'), true);
    Quill.register(Quill.import('attributors/style/size'), true);
    

    document.querySelectorAll('.quill-editor:not(.ready)').forEach(function(element){
        let container = element.closest('.quill-container');
        let textarea = container.querySelector('.quill-textarea');

        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            ['link', 'image', 'video', 'formula'],
          
            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction
          
            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, ]}],
          
            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'font': [] }],
            [{ 'align': [] }],

            [{ header: [1, 2, 3, false] }],
          
            ['table'],

            ['clean'],                                         // remove formatting button

            // {handlers: {
            //     link: function (value) {
            //         if (value) {
            //           const href = prompt('Enter the URL');
            //           this.quill.format('link', href);
            //         } else {
            //           this.quill.format('link', false);
            //         }
            //       }
            //     }
            // }
        ];

        let quill = new Quill(element, {
            theme: 'snow',
            modules: {
                table: true,
                tableUI: true,
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        table: function() {
                            this.quill.getModule('table').insertTable(3, 3);
                        }
                    }
                },
                clipboard: {
                    matchVisual: false
                }
            }
        });

        // let table = quill.getModule('table')
        // container.querySelector('.ql-insert-table').addEventListener('click', function(){
        //     console.log('click');
        //     table.insertTable(2, 2);
        // });

        quill.root.innerHTML = textarea.value;

        quill.on('text-change', function () {
            let value = quill.root.innerHTML;
            textarea.value = value;
            textarea.dispatchEvent(new Event('input'));
        });

        element.classList.add('ready');
        container.querySelector('.quill-loading').remove();
    });
}
window.loadQuill();