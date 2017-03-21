"use strict";

/**
 * @typedef {string} BASE_URL
 */

$.extend({
    fbloader: function(container) {
        $(container).html(
            $('<img>').prop('src', BASE_URL + 'assets/img/admin/loading-fb.gif')
        );
    }
});

$(function() {
    $(document).on('click', '.ban-fe', function (event) {
        var el = $(this);
        var status = 1;
        if(el.is(':checked')) {
            status = 0;
        }

        if(status == 1) {
            $('#ban-card-' + el.data('id')).prop('checked', false);
        }
        el.after('<span> ...</span>');
        $.post(BASE_URL +'admin/ban', {
            id: el.data('id'),
            status: status
        }, function () {
            $('span', el.parent()).empty().remove();
        });

    });

    $(document).on('click', '.ban-card', function (event) {
        var el = $(this);
        var status = 0;
        if(el.is(':checked')) {
            status = -1;
        }

        if(status == -1) {
            $('#ban-fe-' + el.data('id')).prop('checked', true);
        }

        el.after('<span> ...</span>');
        $.post(BASE_URL +'admin/ban', {
            id: el.data('id'),
            status: status
        }, function () {
            $('span', el.parent()).empty().remove();
        });
    });

    $(document).on('click', '.failed-card', function (event) {
        var el = $(this);
        var status = 0;
        if(el.is(':checked')) {
            status = 1;
        }

        el.after('<span> ...</span>');
        $.post(BASE_URL +'admin/failed', {
            id: el.data('id'),
            status: status
        }, function () {
            $('span', el.parent()).empty().remove();
        });
    });
});