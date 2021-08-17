(function ($) {
    'use strict';

    window.tpgFixLazyLoad = function () {
        $('.rt-tpg-container').each(function () {
            // jetpack Lazy load
            $(this).find('img.jetpack-lazy-image:not(.jetpack-lazy-image--handled)').each(function () {
                $(this).addClass('jetpack-lazy-image--handled').removeAttr('srcset').removeAttr('data-lazy-src').attr('data-lazy-loaded', 1);
            });
            //
            $(this).find('img.lazyload').each(function () {
                var src = $(this).attr('data-src') || '';
                if (src) {
                    $(this).attr('src', src).removeClass('lazyload').addClass('lazyloaded');
                }
            });
        });
    };

    window.initTpg = function () {

        $('.rt-tpg-container').each(function () {
            var container = $(this),
                str = $(this).attr("data-layout"),
                IsotopeWrap = '',
                isIsotop = $(".rt-tpg-isotope", container),
                IsoButton = $(".rt-tpg-isotope-buttons", container),
                IsoDropdownFilter = $("select.isotope-dropdown-filter", container);

            if (str) {
                var qsRegex,
                    buttonFilter;

                if (isIsotop.length) {
                    var IsoURL = IsoButton.attr('data-url'),
                        IsoCount = IsoButton.attr('data-count');
                    if (!buttonFilter) {
                        if (IsoButton.length) {
                            buttonFilter = IsoButton.find('button.selected').data('filter');
                        } else if (IsoDropdownFilter.length) {
                            buttonFilter = IsoDropdownFilter.val();
                        }
                    }
                    container.trigger('tpg_item_before_load');
                    tpgFixLazyLoad();
                    IsotopeWrap = isIsotop.imagesLoaded(function () {
                        preFunction();
                        IsotopeWrap.isotope({
                            itemSelector: '.isotope-item',
                            masonry: {columnWidth: '.isotope-item'},
                            filter: function () {
                                var $this = $(this);
                                var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
                                var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
                                return searchResult && buttonResult;
                            }
                        });
                        setTimeout(function () {
                            IsotopeWrap.isotope();
                            container.trigger('tpg_item_after_load');
                        }, 100);
                    });
                    // use value of search field to filter
                    var $quicksearch = container.find('.iso-search-input').keyup(debounce(function () {
                        qsRegex = new RegExp($quicksearch.val(), 'gi');
                        IsotopeWrap.isotope();
                    }));

                    IsoButton.on('click touchstart', 'button', function (e) {
                        e.preventDefault();
                        buttonFilter = $(this).attr('data-filter');
                        if (IsoURL) {
                            location.hash = "filter=" + encodeURIComponent(buttonFilter);
                        } else {
                            IsotopeWrap.isotope();
                            $(this).parent().find('.selected').removeClass('selected');
                            $(this).addClass('selected');
                        }
                    });
                    if (IsoURL) {
                        windowHashChange(IsotopeWrap, IsoButton);
                        $(window).on("hashchange", function () {
                            windowHashChange(IsotopeWrap, IsoButton);
                        });
                    }
                    if (IsoCount) {
                        isoFilterCounter(container, IsotopeWrap);
                    }
                    IsoDropdownFilter.on('change', function (e) {
                        e.preventDefault();
                        buttonFilter = $(this).val();
                        IsotopeWrap.isotope();
                    });
                }
            }
            container.trigger("tpg_loaded");
        });
    };
    initTpg();

    $(window).on('load resize', function () {
        HeightResize();
        overlayIconResizeTpg();
    });

    function windowHashChange(isotope, IsoButton) {
        var $hashFilter = decodeHash() || '';
        if (!$hashFilter) {
            $hashFilter = IsoButton.find('button.selected').attr('data-filter') || '';
            $hashFilter = $hashFilter ? $hashFilter : '*';
        }
        $hashFilter = $hashFilter || '*';
        isotope.isotope({
            filter: $hashFilter
        });
        IsoButton.find("button").removeClass("selected");
        IsoButton.find('button[data-filter="' + $hashFilter + '"]').addClass("selected");
    }

    function decodeHash() {
        var $matches = location.hash.match(/filter=([^&]+)/i);
        var $hashFilter = $matches && $matches[1];
        return $hashFilter && decodeURIComponent($hashFilter);
    }

    function isoFilterCounter(container, isotope) {
        var total = 0;
        container.find('.rt-tpg-isotope-buttons button').each(function () {
            var self = $(this),
                filter = self.attr("data-filter"),
                itemTotal = isotope.find(filter).length;
            if (filter != "*") {
                self.find('span').remove();
                self.append("<span> (" + itemTotal + ") </span>");
                total = total + itemTotal;
            }
        });
        container.find('.rt-tpg-isotope-buttons button[data-filter="*"]').find('span').remove();
        container.find('.rt-tpg-isotope-buttons button[data-filter="*"]').append("<span> (" + total + ") </span>");
    }

    // debounce so filtering doesn't happen every millisecond
    function debounce(fn, threshold) {
        var timeout;
        return function debounced() {
            if (timeout) {
                clearTimeout(timeout);
            }

            function delayed() {
                fn();
                timeout = null;
            }

            setTimeout(delayed, threshold || 100);
        };
    }

    function preFunction() {
        HeightResize();
        overlayIconResizeTpg();
    }

    $(".rt-tpg-container a.disabled").each(function () {
        $(this).prop("disabled", true);
        $(this).removeAttr("href");
    });

    function HeightResize() {
        var wWidth = $(window).width();
        tpgFixLazyLoad();
        $(".rt-tpg-container").each(function () {
            var self = $(this),
                dCol = self.data('desktop-col'),
                tCol = self.data('tab-col'),
                mCol = self.data('mobile-col'),
                target = $(this).find('.rt-row.rt-content-loader.tpg-even');
            if ((wWidth >= 992 && dCol > 1) || (wWidth >= 768 && tCol > 1) || (wWidth < 768 && mCol > 1)) {
                target.imagesLoaded(function () {
                    var tlpMaxH = 0;
                    target.find('.even-grid-item').height('auto');
                    target.find('.even-grid-item').each(function () {
                        var $thisH = $(this).outerHeight();
                        if ($thisH > tlpMaxH) {
                            tlpMaxH = $thisH;
                        }
                    });
                    target.find('.even-grid-item').height(tlpMaxH + "px");
                });
            } else {
                target.find('.even-grid-item').height('auto');
            }

        });
        if ($(".rt-row.rt-content-loader.layout4").length) {
            equalHeight4Layout4();
        }

        function equalHeight4Layout4() {
            var $maxH = $(".rt-row.rt-content-loader.layout4 .layout4item").height();
            $(".rt-row.rt-content-loader.layout4 .layout4item .layoutInner .rt-img-holder img,.rt-row.rt-content-loader.layout4 .layout4item .layoutInner.layoutInner-content").height($maxH + "px");
        }
    }

    function overlayIconResizeTpg() {
        $('.overlay').each(function () {
            var holder_height = jQuery(this).height();
            var target = $(this).children('.link-holder');
            var targetd = $(this).children('.view-details');
            var a_height = target.height();
            var ad_height = targetd.height();
            var h = (holder_height - a_height) / 2;
            var hd = (holder_height - ad_height) / 2;
            target.css('top', h + 'px');
            targetd.css('margin-top', hd + 'px');
        });
    }

})(jQuery);