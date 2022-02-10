;(function ($) {

    // $(window).on('load', function(){
    $(document).ready(function () {


        // elementor.hooks.addAction('panel/open_editor/widget/grid-layout', function (panel, model, view) {}
/*
        elementor.hooks.addAction('panel/open_editor/widget/grid-layout', function (panel, model, view) {

            var servicesStyle = panel.$el.find('.tpg-image-select .elementor-choices input');

            $('body').on('click', '.tpg-image-select .elementor-choices label', function () {
                var data = $(this).attr('data-tooltip');
                if(data === 'Layout 2') {
                    $('.elementor-control-gird_column').hide();
                } else {
                    $('.elementor-control-gird_column').fadeIn();
                }
                console.log(data);
            });

            var el = $('.tpg-image-select .elementor-choices input');
            var value = el.val();

            console.log(el);
            console.log(value);

            if(el.is(':checked') && value === 'layout3'){
                $('.elementor-control-gird_column').hide();
                console.log('sssssssssss');
            } else {
                console.log('dddddddddddddddd');
                $('.elementor-control-gird_column').fadeIn();
            }



        });

 */



        elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {


            $('body').on('click', '.the-post-grid-field-hide', function(){
                $(this).toggleClass('is-pro');
                $(this).find('label', 'input').on('click', function(){
                    console.log($(this));
                })
                // return false;
            })


        })




    })


})(jQuery);