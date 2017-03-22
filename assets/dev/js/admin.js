"use strict";

/**
 * @typedef {string} BASE_URL
 */

const BASE_URL_ADMIN = BASE_URL + 'admin/';

let $ = require("jquery");

let swal = require("sweetalert");

$.setStatus = function (type, element, status, callback) {
    callback = callback || function () {};
    element.addClass('loading');
    $.post(BASE_URL_ADMIN + 'set-status', {
        type: type,
        id: element.data('id'),
        status: status
    }, function (response) {
        /**
         * @property response.mail string
         */
        if(response.success == 'ok') {
            let row = element.parent().parent();
            row.fadeOut();
            callback();
            if(response.mail == 'ok') {
                swal.close();
            } else {
                swal("Uzmanību!!!", "Neizdevās nosūtīt epastu!", "warning");
            }
        } else {
            element.removeClass('loading');
            swal("Kļūda sistēmā!", "Pārlādē lapu un mēģini vēlreiz", "error");
        }
    }, 'json');

};

$(function () {

    $(document).on('click', '.ok-day-1', function (event) {
        event.preventDefault();
        let el = $(this);
        swal({
            title: "Tiešām?",
            text: "Nospiežot OK, cilvēkam pienāks automātiski apstiprinājuma epasta vēstule!",
            type: "warning",
            showCancelButton: true,
            // confirmButtonColor: "DD6B55",
            confirmButtonText: "Sūtam!",
            closeOnConfirm: false,
            html: false
        }, function(){
            $.setStatus('day1', el, 1);
        });
    });

    $(document).on('click', '.ney-day-1', function (event) {
        event.preventDefault();
        let el = $(this);
        swal({
            title: "Tiešām?",
            text: "Nospiežot OK, cilvēkam pienāks automātiski apstiprinājuma epasta vēstule!",
            type: "error",
            showCancelButton: true,
            // confirmButtonColor: "DD6B55",
            confirmButtonText: "Sūtam!",
            closeOnConfirm: false,
            html: false
        }, function(){
            $.setStatus('day1', el, -1);
        });
    });

    $(document).on('click', '.ok-day-2', function (event) {
        event.preventDefault();
        let el = $(this);
        swal({
            title: "Tiešām?",
            text: "Nospiežot OK, cilvēkam pienāks automātiski apstiprinājuma epasta vēstule!",
            type: "warning",
            showCancelButton: true,
            // confirmButtonColor: "DD6B55",
            confirmButtonText: "Sūtam!",
            closeOnConfirm: false,
            html: false
        }, function(){
            $.setStatus('day2', el, 1);
        });
    });

    $(document).on('click', '.ney-day-2', function (event) {
        event.preventDefault();
        let el = $(this);
        swal({
            title: "Tiešām?",
            text: "Nospiežot OK, cilvēkam pienāks automātiski apstiprinājuma epasta vēstule!",
            type: "error",
            showCancelButton: true,
            // confirmButtonColor: "DD6B55",
            confirmButtonText: "Sūtam!",
            closeOnConfirm: false,
            html: false
        }, function(){
            $.setStatus('day2', el, -1);
        });
    });

    $(document).on('click', '.ok-media-2', function (event) {
        event.preventDefault();
        let el = $(this);
        swal({
            title: "Tiešām?",
            text: "Nospiežot OK, cilvēkam pienāks automātiski apstiprinājuma epasta vēstule!",
            type: "warning",
            showCancelButton: true,
            // confirmButtonColor: "DD6B55",
            confirmButtonText: "Sūtam!",
            closeOnConfirm: false,
            html: false
        }, function(){
            $.setStatus('media', el, 1);
        });
    });

    $(document).on('click', '.ney-media-2', function (event) {
        event.preventDefault();
        let el = $(this);
        $.setStatus('media', el, -1);
    });
});