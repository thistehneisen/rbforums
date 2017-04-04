"use strict";

let $ = require("jquery");

$(function () {

    setTimeout(function () {
        let pathArray = window.location.pathname.split( '/' );
        if(pathArray.length > 2 && pathArray[2] !== '') {
            clicks_scrollTo(pathArray[2], $('#' + pathArray[2]), window.location.href);
        }

        let menuIds = [];
        $('.main-navigation a').each(function () {
            menuIds.push([
                $(this).data('id'),
                $('#' + $(this).data('id')).position().top,
                $(this)
            ]);
        });

        $(window).scroll(function () {
            let posTop = $('body').scrollTop() + 80;
            let active = menuIds[0];
            for(let i = 0; i < menuIds.length; i++) {
                if(posTop > menuIds[i][1]) {
                    active = menuIds[i];
                }
            }

            if(!active[2].hasClass('active')) {
                $('.main-navigation a').removeClass('active');
                active[2].addClass('active');
            }

        });

    }, 250);

    $(document).on('click', '.main-navigation a', function (event) {
        event.preventDefault();
        let el = $(this);
        $('.main-navigation a').removeClass('active');
        el.addClass('active');

        clicks_scrollTo(el.data('id'), $('#' + el.data('id')), el.prop('href'));
    });

    $(document).on('click', '.jump-button', function (event) {
        event.preventDefault();
        let el = $(this);
        $('.main-navigation a').removeClass('active');
        let regExp = new RegExp('(https?:)?' + BASE_URL);
        let cl = el.prop('href').replace(regExp, '');
        let navClass = '.' + cl;
        $('.main-navigation').find(navClass).addClass('active');
        clicks_scrollTo(el.data('id'), $('#' + el.data('id')), el.prop('href'));
    });


    let form1 = $('#form1');
    let form2 = $('#form2');
    let form3 = $('#form3');
    let form4 = $('#form4');
    $(document).on('click', '.registration .buttons a', function (event) {
        event.preventDefault();
        let el = $(this);
        $('.registration .buttons a').removeClass('active');
        el.addClass('active');
        if(el.data('form') === 'day1') {
            form2.slideUp();
            form1.slideDown();
        } else {
            form1.slideUp();
            form2.slideDown();
        }
    });

    $(".form-2-jump").click(function (event) {
        event.preventDefault();
        let b = $('.registration').find('.buttons a');
        b.eq(0).removeClass('active');
        b.eq(1).addClass('active');
        form1.slideUp();
        form2.slideDown();
        clicks_scrollTo("registration", $('#registration'));
    });

    /**
     * make nice selects
     */

    let select = $('select');
    select.each(function () {


        let $this = $(this), numberOfOptions = $(this).children('option').length;

        $this.addClass('select-hidden');
        $this.wrap('<div class="select"></div>');
        $this.after('<div class="select-styled"></div>');

        let $styledSelect = $this.next('div.select-styled');
        let text = $this.children('option').eq(0).text();
        $styledSelect.text(text);

        if (text === 'Choose') {
            $styledSelect.addClass('choose');
        }

        let $list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter($styledSelect);

        for (let i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list);
        }

        let $listItems = $list.children('li');

        $styledSelect.click(function (e) {
            e.stopPropagation();
            let el = $(this);
            if (!el.hasClass('disabled')) {
                $('label[for='+$this.attr('id')+']').removeClass('error');
                $('div.select-styled.active').not(this).each(function () {
                    el.removeClass('active').next('ul.select-options').hide();
                });
                el.toggleClass('active').next('ul.select-options').toggle();
            }
        });

        $listItems.click(function (e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active').removeClass('choose');
            $this.val($(this).attr('rel'));
            $list.hide();
            $this.trigger('change');
        });

        $(document).click(function () {
            $styledSelect.removeClass('active');
            $list.hide();
        });

    });

    $('.col.disabled').each(function () {
        let el = $(this);
        $('input, select', el).prop('disabled', 'disabled');
        $('.select-styled', el).addClass('disabled');
    });

    form1.submit(function (event) {
        event.preventDefault();
    });

    form2.submit(function (event) {
        event.preventDefault();
    });

    form3.submit(function (event) {
        event.preventDefault();
    });

    form4.submit(function (event) {
        event.preventDefault();
    });


    $('#validate').click(function (event) {
        event.preventDefault();
        let el = $(this);
        let code = $('#registration_code').val();
        if (code !== '') {
            el.addClass('loading').removeClass('error');
            $.post(BASE_URL + 'validate-code',
                {
                    code: code
                }, function (response) {
                    if (response.ok === 'ok') {
                        $('.col', form1).removeClass('disabled');
                        $('input[type=text], select', form1).prop('disabled', false);
                        $('#need_visa_invite, #potential_supplier, #register-form1', form1).prop('disabled', false);
                        $('.select-styled').removeClass('disabled');

                        el.addClass('ok');
                    } else {
                        el.addClass('error');
                    }
                    el.removeClass('loading');
                }, 'json'
            );
        }

    });

    $('#potential_supplier').change(function () {
        if($(this).prop('checked')) {
            $('#agree_to_supp_catalogue').prop('disabled', false);
        } else {
            $('#agree_to_supp_catalogue').prop({'disabled': 'disabled', 'checked': false});
        }
    });

    $('#register-form1').click(function (event) {
        event.preventDefault();
        if(validFormData(form1)) {
            $(this).addClass('loading');
            let data = form1.find('form').serializeArray();
            $.post(BASE_URL + 'register-form-1', data, function (response) {
                if(response.success === 'ok') {
                    $('#register-form1').slideUp();
                    $('#form1').find('.thanks').slideDown();
                }
            }, 'json');
        }
    });

    $('#register-form2').click(function (event) {
        event.preventDefault();
        if(validFormData(form2)) {
            $(this).addClass('loading');
            let data = form2.find('form').serializeArray();
            $.post(BASE_URL + 'register-form-2', data, function (response) {
                if(response.success === 'ok') {
                    $('#register-form2').slideUp();
                    $('#form2').find('.thanks').slideDown();
                }
            }, 'json');
        }
    });

    $('input[type=text]').focus(function () {
        $(this).prev('label').removeClass('error');
    });
    $('input[type=checkbox]').focus(function () {
        $(this).next('label').removeClass('error');
    });

    $('#register-form3').click(function (event) {
        event.preventDefault();
        if(validFormData(form3)) {
            $(this).addClass('loading');
            let data = form3.find('form').serializeArray();
            $.post(BASE_URL + 'register-form-3', data, function (response) {
                if(response.success === 'ok') {
                    $('#register-form3').slideUp();
                    $('#form3').find('.thanks').slideDown();
                }
            }, 'json');
        }
    });

    $('#register-form4').click(function (event) {
        event.preventDefault();
        if(validFormData(form4)) {
            $(this).addClass('loading');
            let data = form4.find('form').serializeArray();
            $.post(BASE_URL + 'register-form-4', data, function (response) {
                if(response.success === 'ok') {
                    form4.find('.thanks').slideDown();
                    form4.find('form').slideUp();
                }
            }, 'json');
        }
    });

    $('.speaker').click(function (event) {
        event.preventDefault();
        let el = $(this);
        $('.speaker').removeClass('active');
        el.addClass('active');
        let name = el.find('.name').text();
        let position = el.find('.position').text();
        let img = el.data('img');
        let about = $('.a-description.a' + el.data('id')).html();

        let main = $('.main-description');
        main.find('.image').html($('<img>').prop('src', img));
        main.find('h2').html(name);
        main.find('h3').html(position);
        main.find('.about').html(about);
        clicks_scrollTo('speakers', $('#speakers'));
    });

    AdobeEdge.loadComposition('/forum/assets/js/Railbaltic_logo', 'EDGE-267760406', {
        scaleToFit: "none",
        centerStage: "none",
        minW: "0px",
        maxW: "undefined",
        width: "680px",
        height: "280px"
    }, {"dom":{}}, {"dom":{}});


    /**
     * suppliers
     */

    $('#suppliers_search_type').change(function () {
        let type = $(this).val();
        let alphabet = $('.alphabet');
        let industry = $('.industry-selector');
        if(type === 'alphabet') {
            alphabet.show();
            industry.hide();
            getSuppliers({
                type: type,
                key: $('.suppliers_search_letter.active').data('letter')
            });
        } else {
            alphabet.hide();
            industry.show();
            getSuppliers({
                type: type,
                key: $('#suppliers_search_industry').val()
            });
        }
    });

    $('#suppliers_search_industry').change(function () {
        getSuppliers({
            type: 'industry',
            key: $(this).val()
        });
    });

    $('.suppliers_search_letter').click(function (event) {
        event.preventDefault();
        $('.suppliers_search_letter').removeClass('active');
        $(this).addClass('active');
        getSuppliers({
            type: 'alphabet',
            key: $(this).data('letter')
        });
    });
});

function getSuppliers(options) {
    let postSettings = {
        type: options.type || 'alphabet',
        key: options.key || 'A'
    };
    let loader = $('#suppliers-loader');
    loader.show();
    $.post(BASE_URL + 'get-suppliers', postSettings, function (response) {
        if(response.success === 'ok') {
            $('#suppliers-companies').html(response.html);
        }
        loader.hide();
    }, 'json');

}

function validFormData(form) {
    let validated = true;
    $('input, select, textarea', form).each(function () {
        if(!elIsValid($(this))) {
            validated = false;
        }
    });
    return validated;
}

function elIsValid(el) {
    let validated = true;
    if(el.data('validate')) {
        let validate = el.data('validate').split('|');
        let val = $.trim(el.val());
        for(let i = 0; i < validate.length; i++) {
            switch (validate[i]) {
                case 'require':
                    if(val === '') {
                        validated = false;
                        if(el.attr('type') === 'checkbox') {
                            el.next('label').addClass('error');
                        } else {
                            el.prev('label').addClass('error');
                        }
                    } else if(el.is('select') && val === 'hide') {
                        $('label[for=' + el.attr('id') + ']').addClass('error');
                    }
                    break;
                case 'email':
                    if(!isValidEmailAddress(val)) {
                        validated = false;
                        el.prev('label').addClass('error');
                    }
                    break;
            }
        }
    }

    return validated;
}



function clicks_scrollTo(id, element, url) {
    element = element || (function () {
            let newId = false;
            let tempId = id;
            if (id === 'index') tempId = '';
            $('.menu').each(function () {
                if ($(this).data('id') === tempId) newId = $(this);
            });
            return newId;
        })() || false;

    // $('.menu').removeClass('active');

    if (element) {
        element.addClass('active');
    }

    if (id === '' || id === 'home') {
        $('html, body').animate({
            scrollTop: '0px'
        });
    } else {
        let container = $('#' + id);
        if (container.length > 0) {
            $('html, body').animate({
                scrollTop: Math.round(container.position().top - 65) + 'px'
            });
        }
    }
    id = (id === 'home' ? '' : id);
    url = url || BASE_URL + id;
    history.pushState('', '', url);
}

function isValidEmailAddress(emailAddress) {
    let pattern;
    pattern = /^(([^<>()\[\].,;:\s@"]+(\.[^<>()\[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/i;
    return pattern.test(emailAddress);
}