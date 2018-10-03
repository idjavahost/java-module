$(function() {

    $.fn.select2.defaults.reset();
    $.fn.select2.defaults.set("theme", "bootstrap");
    $.fn.select2.defaults.set("language", "id");
    $.fn.select2.defaults.set("width", "100%");
    $.fn.select2.defaults.set("minimumResultsForSearch", Infinity);

    function triggerPlugins(id) {
        var wrap = (typeof id !== 'undefined') ? id : '#java-options-form';

        $(wrap+' .help-tip').popover({
            html: true,
            animation: true,
            container: wrap,
            placement: 'left',
            trigger: 'click'
        });

        $(wrap+' .colorpicker-component').each(function() {
            var config={}, configAttr = $(this).attr('data-config');
            if (configAttr) config = JSON.parse(configAttr);
            var defaults = {container:$(wrap), format:'hex'};
            $(this).colorpicker(config);
        });

        $(wrap+' textarea.wysihtml').each(function() {
            var config, configAttr = $(this).attr('data-config');
            if (configAttr) config = JSON.parse(configAttr);
            else config = {"font-styles":false,"image":false,"blockquote":false,"link":false,"lists":false};
            $(this).wysihtml5({toolbar:config});
        });

        $(wrap+' .form-group select').each(function() {
            if (!$(this).hasClass('inited') && $(this).is(':visible')) {
                $(this).addClass('inited');
                $(this).select2();
            }
        });

        $(wrap+' input.bootstrap-slider').each(function() {
            var id = $(this).data('slider-id') + '-value';
            $(this).bootstrapSlider();
            $(this).on('slide', function(slideEvt) {
                $('span#'+id).text(slideEvt.value);
            });
        });

        $(wrap+' .dm-uploader').each(function() {
    		var el = $(this), options = el.data('options'),
    		defaults = {
    			multiple: false,
    			allowedTypes: 'image/*',
    			onDragEnter: function() { el.addClass('active'); },
    			onDragLeave: function() { el.removeClass('active'); },
    			onNewFile: function(id, file) {
                    if (el.find('img').length) {
        				if (typeof FileReader !== "undefined") {
        					var reader = new FileReader(), img = el.find('img');
        					reader.onload = function (e) {
        						img.attr('src', e.target.result);
        					};
        					reader.readAsDataURL(file);
        				}
                    }
    			},
    			onBeforeUpload: function(id) {
    				el.find('.btn-sm').prop('disabled',true);
    				$('#java-options-form').find('button[type=submit]').prop('disabled',true);
    			},
    			onComplete: function() {
    				el.find('.btn-sm').prop('disabled',false);
                    $('#java-options-form').find('button[type=submit]').prop('disabled',false);
    			},
    			onUploadSuccess: function(id, response) {
    				if (response.status == 'fail') {
    					var msg = response.message?', '+response.message:'!';
    					showMessage('danger', 'Upload gagal'+msg);
    				} else if (response.data) {
                        var data = response.data;
    					el.find('input[type="text"]').val(data.rel_path+'/'+data.file_name);
                        showMessage('success', 'Upload berhasil, jangan lupa untuk menyimpan pengaturan!', 10);
    				}
    			}
    		};
    		$.extend(defaults, options);
    		el.dmUploader(defaults);
    	});

        window.javaForm.resetForm();
    }

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

    $('div.java-tab-menu>div.list-group>a').click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass('active');
        $(this).addClass('active');
        var index = $(this).index(), id = $(this)[0].hash;
        $('div.java-tab>div.java-tab-content').removeClass('active');
        $('div.java-tab>div.java-tab-content').eq(index).addClass('active');
        if(history.pushState) history.pushState(null, null, id);
        else location.hash = id;
        var myH = $('div.java-tab>div.java-tab-content').eq(index).height();
        resizeTabNav(myH + 80);
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

    var resizeTabNav = function(hei) {
        var fuH = $('.java-tab-container').height();
        var wH = (typeof hei === 'undefined') ? fuH : (hei<fuH?fuH:hei);
        $('.java-tab-menu,.java-tab').attr('style', '');
        $('.java-tab-menu,.java-tab').css({'min-height':wH+'px'});
    };

    $(window).resize(function() {
        resizeTabNav();
    });

    $('.java-tab-container').removeClass('is-loading');
    resizeTabNav();
    triggerPlugins();
});
