/*
 * Widz library
 * 
 * @license: GPLv3
 * @author: vutuansw
 */


jQuery(function ($) {

    'use strict';

    $.fn.widzWidget = function () {

        /**
         * Widget Save Trigger
         */
        $(document).on('click', '.widget-control-save', function (e) {

            var $this = $(this);

            if (!$this.hasClass('trigger')) {

                var id = $this.closest('.widget').attr('id');

                var id_base = $('#' + id + ' input.id_base').val();

                if (id_base == 'widz_tabbed_widget') {

                    var tab_items = {};
                    var titles = [];

                    $('#' + id + ' .widz-widget-selected .widz-widget-item').each(function () {

                        var $this = $(this);

                        var item_id = $this.attr('id');

                        var heading_text = $("#" + item_id + ' .js-heading-text').text();

                        var data_params = $('#' + item_id + ' input, #' + item_id + ' select, #' + item_id + ' textarea').serializeArray();

                        var params = {};

                        $.each(data_params, function (key, val) {

                            var name = val.name.split('][');

                            if (name.length == 2) {
                                name = name[1].replace(']', '');

                                params[name] = val.value == 'on' ? true : val.value;
                            }

                        });

                        if (typeof params.title == 'undefined' || $.trim(params.title) == '') {
                            params.title = heading_text;
                        }

                        titles.push(params.title);

                        var item = {
                            widget_id: $this.attr('data-widget_id'),
                            number: $this.attr('data-number'),
                            widget: $this.attr('data-widget'),
                            params: params
                        };

                        tab_items[item.widget_id + '-' + item.number] = item;

                    });

                    $('#' + id + ' .widz-widget input.widz_value').val(JSON.stringify(tab_items));

                    $this.addClass('trigger');

                    setTimeout(function () {

                        $this.trigger('click');

                    }, 200);

                    e.stopPropagation();

                    return false;
                }
            }

            //console.log('Saved');
            $this.removeClass('trigger');
            e.preventDefault();

        });

        /**
         *Edit
         */

        $(document).on('click', '.widz-widget .cmd.edit, .widz-widget .widz-widget-title h4', function (e) {

            var $parent = $(this).closest(".widz-widget");

            if ($parent.hasClass('open')) {

                $parent.find('.widz-widget-inside').slideUp({
                    complete: function () {
                        $parent.removeClass('open');
                    }
                });
            } else {

                $parent.find('.widz-widget-inside').slideDown({
                    complete: function () {
                        $parent.addClass('open');
                    }
                });
            }

            e.preventDefault();
        });

        /**
         * Delete
         */

        $(document).on('click', '.widz-widget .cmd.remove', function (e) {
            $(this).closest('li').addClass('removing').fadeOut('normal', function () {
                $(this).remove();
            });
            e.preventDefault();
        });


        /**
         * Push Widget To List
         */
        $(document).on('change', '.widz-widget-controls select', function (e) {

            var $select = $(this);
            if ($select.val() != '') {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'widz_get_widget_control',
                        widget_id: $select.val(),
                        widget_name: $select.find('option:selected').text(),
                        widget: $select.find('option:selected').attr('data-widget'),
                    },
                    beforeSend: function () {
                        $select.next('.spinner').show();
                    },
                    success: function (form) {
                        $select.closest('.widz-widget').find('ul').append(form);
                        $select.next('.spinner').hide();
                        $select.val('');

                        var $widget = $select.closest('.widz-widget').find('ul li.widz-widget-item:last-child');
                        var $icon_picker = $widget.find('.widz-icon_picker select');
                        $icon_picker.fontIconPicker();
                        $widget.find('.widz-widget-inside').slideDown({
                            complete: function () {
                                $widget.addClass('open');
                            }
                        });
                    }
                });
            }
            e.preventDefault();

        });


        if ($.fn.sortable) {
            $(".widz-widget-selected").sortable({
                items: '.widz-widget-item',
                handle: ".widz-widget-title h4",
                cancel: ".widz-widget-title-action"
            });
        }

    }
});