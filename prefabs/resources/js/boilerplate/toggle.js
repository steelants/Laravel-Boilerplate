window.initToggler = function(){
    window.toggleState = JSON.parse(getCookie('toggleState') ?? '{}');
}

initToggler();

window.updateToggler = function(el, state){
    if(el.classList.contains('remember') && typeof el.id != 'undefined' && el.id != ''){
        var name = el.id;
        window.toggleState[name] = state;
        console.log(window.toggleState);
        setCookie('toggleState', JSON.stringify(window.toggleState), 30);
    }
}

window.addEventListener('hide.bs.collapse', function(e){
    updateToggler(e.target, 'closed');
});
window.addEventListener('show.bs.collapse', function(e){
    updateToggler(e.target, 'open');
});
