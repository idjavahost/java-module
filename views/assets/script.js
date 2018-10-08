$(function() {

    $.fn.select2.defaults.reset();
    $.fn.select2.defaults.set("theme", "bootstrap");
    $.fn.select2.defaults.set("language", "id");
    $.fn.select2.defaults.set("width", "100%");
    $.fn.select2.defaults.set("minimumResultsForSearch", Infinity);

    $('html').on('mouseup', function(e) {
        if (!$(e.target).closest('.popover').length) {
            $('[data-original-title]').popover('hide');
        }
    });

    $(document).on('click', '.dm-uploader img.img-preview', function(e) {
        var src = $(e.target).attr('src'), modal = $('.modal-java-preview');
        if (src && modal.length) {
            modal.find('img').attr('src', src);
            modal.modal('show');
        }
    });

    $('div.java-tab-menu>div.list-group>a').on('click', function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass('active');
        $(this).addClass('active');
        var index = $(this).index(), id = $(this)[0].hash;
        $('div.java-tab>div.java-tab-content').removeClass('active');
        $('div.java-tab>div.java-tab-content').eq(index).addClass('active');
        if(history.pushState) history.pushState(null, null, id);
        else location.hash = id;
        resizeTabNav($(id).height() + 95);
        triggerPlugins(id);
    });

    window.javaForm = $('#java-options-form').validate({
        errorElement: 'small',
        errorClass: 'help-block',
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        submitHandler: function(form) {
            var errMsg = 'Ups! Terjadi kesalahan, gagal menyimpan konfigurasi.';
            $('.java-tab-container').addClass('is-loading');
            try {
                var fields = $(form).serializeObject();
                $.post(form.action, fields, function(response) {
                    if (response.success) {
                        showMessage('success', 'Pengaturan berhasil di simpan! <a href="/first" target="_blank">Lihat Website</a>');
                    } else {
                        var msg = response.message ? response.message : errMsg;
                        showMessage('danger', errMsg);
                    }
                    $('.java-tab-container').removeClass('is-loading');
                },'json');
            } catch (e) {
                $('.java-tab-container').removeClass('is-loading');
                showMessage('danger', errMsg);
                console.log(e);
            }
            return false;
        }
    });

    if (window.location.hash !== '' && $(window.location.hash).length) {
        $('div.java-tab-menu>div.list-group>a[href="'+window.location.hash+'"]').click();
    } else {
        $('div.java-tab-menu>div.list-group>a:first-child').click();
    }

    $(window).resize(function() {
        resizeTabNav();
    });

    resizeTabNav();
});
