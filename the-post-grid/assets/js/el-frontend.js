(function ($) {
    "use strict";

    $(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction("frontend/element_ready/widget", function (scope, $) {


            /*
                // Run 1st time
                var $isotopeContainer = scope.find('#inner-isotope');
                var $container = $isotopeContainer.find('.featuredContainer');
                var filter = $isotopeContainer.find('.isotope-classes-tab a.current').data('filter');
                runIsotope($container,filter);



                // Run on click event

                $isotopeContainer.find('.isotope-classes-tab').on('click', 'a', function(){
                    var $this = $(this);
                    $this.closest('.isotope-classes-tab').find('.current').removeClass('current');
                    $this.addClass('current');
                    var $container = $this.closest('.isotope-wrap').find('.featuredContainer');
                    var filter = $this.attr('data-filter');
                    runIsotope($container,filter);
                    return false;
                });
             */



        });

    });

})(jQuery);