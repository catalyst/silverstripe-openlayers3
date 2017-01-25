window.addEventListener('load', function(){
    console.log('pass');

    ol3 = new OL3();
    map = ol3.render();

    ol3.layer.init();

});
