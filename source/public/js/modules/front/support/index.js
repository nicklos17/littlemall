define(function(require){

    $('tbody').hover(
        function(){
            $(this).addClass('active')
        },
        function(){
            $(this).removeClass('active')
        }
    )

})