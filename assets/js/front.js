'use strict';

jQuery(function ($) {
    $(document).on('click', '.widz_tabbed__nav a', function (e) {
        var $this = $(this);
        var $container = $this.closest('.widz_tabbed');
        //Nav
        $this.closest('ul').find('.active').removeClass('active');
        $this.parent().addClass('active');
        //Contgent
        $container.find('.tab-pane').removeClass('active');
        var id = $this.attr('href');
        $(id).addClass('active');


        e.preventDefault();
    });
});