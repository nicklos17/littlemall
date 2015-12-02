define(function(require, exports){

    var run = function(all, items){
        all.on('click', function (){
            if(all.is(':checked')){
                items.each(function(){
                    this.checked = true;
                });
            }else{
                items.each(function (){
                    this.checked = false;
                });
            }
        });

        items.on('click', function(){
            if(! $(this).is(':checked')){
                all.prop('checked', false);
            }else{
                if(items.length == items.filter(':checked').length){
                    all.prop('checked', true);
                }
            }
        });
    };

    exports.run = run;
});