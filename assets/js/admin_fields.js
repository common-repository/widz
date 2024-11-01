/*
 * Core Fields js functions  
 * 
 * @author vutuansw
 * @since 1.0.0
 */
jQuery(function ($) {


    /**
     * Field Color
     */
    if (document.getElementsByClassName('widz-widget').length) {
        $('.widz-widget').widzWidget();
    }


    if ($('.widz-icon_picker:not(.child-field)').length) {
        $('.widz-icon_picker:not(.child-field) select').fontIconPicker();
    }


    $(document).on('widget-updated', function (e, $widgetRoot) {


        var $icon_picker = $widgetRoot.find('.widz-icon_picker select');
        
        if ($icon_picker.length) {
            $icon_picker.fontIconPicker();
        }
        
    });
});