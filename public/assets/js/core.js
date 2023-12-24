document.addEventListener('DOMContentLoaded', function () {
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    }
});
$(function () {
    $('.metismenu').find('li').addClass('hvr-forward');

    init_component();
    $('.toggle').on('click', function () {
        toggle_tree($(this));
        // console.log('clicked');
    });
    $("form").on("submit", function () {
        $(this).find('button[type="submit"]').attr('disabled', 'disabled').addClass('disabled');
    })
    $('[data-timeline]').on('click', function () {
        var value = $(this).data('timeline');
        console.log(value);
        $.post('../penjualan/API/content_timeline', { 'nomor': value }, function (data) {
            $('#timeline_content').html(data);
        });
        $('#md_timeline').modal();
    });

    var mark = function () {

        // Read the keyword
        var keyword = $("input[name='keyword']").val();

        // Determine selected options
        var options = {
            separateWordSearch: true,
            diacritics: false
        };


        // Remove previous marked elements and mark
        // the new keyword inside the context
        $("body,html").unmark({
            done: function () {
                $("body,html").mark(keyword, options);
            }
        });
    };

    $("input[name='keyword']").on("input", mark);

});
function init_component(withFormatted = false) {
    // $(document).on('input', '.select2-search__field', function (e) {
    //     e.target.value = e.target.value.toUpperCase()
    // });

    if (withFormatted == true) {

    } else {
        $('.select2').select2({
            placeholder: 'Pilih',
            width: '100%'
        });
    }
    $('.datePicker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        // minDate: periode,
        locale: {
            format: "YYYY-MM-DD"
        },
        autoUpdateInput: true,
        autoApply: true,

    });
    $('.datePicker-report').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD"
        },
        autoUpdateInput: true,
        autoApply: true,
    });
    $(".inputmask").inputmask({
        autoUnmask: "true",
        unmaskAsNumber: "true",
        'removeMaskOnSubmit': true,
        alias: 'decimal',
        groupSeparator: ',',
    });

    $("input.limited").maxlength({
        warningClass: "badge badge-info",
        limitReachedClass: "badge badge-warning"
    });
    $('[data-toggle="tooltip"]').tooltip();
    // $('[data-toggle="expand"]').on('mouseenter', function () {
    //     var text = $(this).html();
    //     var expand_text = $(this).data('expandtext');
    //     $(this).html(text + ' ' + expand_text);
    // });
    // $('[data-toggle="expand"]').on('mouseleave', function () {
    //     var text = $(this).html();
    //     var expand_text = $(this).data('expandtext');
    //     var splitted=text.split(expand_text)[0];
    //     $(this).html(splitted);
    // });
}
$('#signature').change(function () {
    const file = this.files[0];
    // console.log(file);
    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            // console.log(event.target.result);
            $('#img_preview').attr('src', event.target.result);
        }
        reader.readAsDataURL(file);
    }
});
$('#profile').change(function () {
    const file = this.files[0];
    // console.log(file);
    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            // console.log(event.target.result);
            $('#img_preview_profile').attr('src', event.target.result);
        }
        reader.readAsDataURL(file);
    }
});
function toggle_tree(e) {
    var elem = $(e).find('.treetable-expander');
    elem.click();
}
function add_spinner(elem) {
    $(elem).LoadingOverlay("show", {
        image: "",
        custom: '<div clas="row mt-5 mb-2"><div class="spinner-border spinner-border-custom-5 border-info" role="status"></div></div>',
        background: "rgba(255, 255, 255, 0.5)"
    });
}
function remove_spinner(elem) {
    $(elem).LoadingOverlay("hide");
}



$('#user_setting_theme').on('change', function () {
    var value = $(this).val();
    // console.log(value);
    $.post('../../app/user/API/user_setting', { 'field': 'theme', 'value': value }, function (result) {
        if (result) {
            $('body').attr('class', value);
        }
    })
});
$('#unlock_password').keypress(function (event) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13') {
        unlock_session();
    }
});
function unlock_session() {
    $('#pass_error').text('');
    var password = $('#unlock_password').val();
    $.post('../../app/user/API/unlock_session', { 'password': password }, function (data) {
        if (data) {
            $('#md_lock').modal('hide');
            window.location.reload();
        } else {
            $('#pass_error').text('Password Tidak Cocok');
            $('#unlock_password').focus();
        }
    });
}

