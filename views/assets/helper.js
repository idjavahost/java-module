var javaForm = null;

var resizeTabNav = function(hei) {
    var fuH = $('.java-tab-content.active').height() + 95,
        mH = $('div.java-tab-menu div.list-group').height(),
        wH = (typeof hei === 'undefined') ? fuH : (hei<fuH?fuH:hei);
    if(wH<mH) wH=mH;
    $('.java-tab-menu,.java-tab').css({'min-height':wH+'px'});
};

var triggerPlugins = function(id) {
    var wrap = (typeof id !== 'undefined') ? id : '#java-options-form';

    $(wrap+' .help-tip').popover({
        html: true,
        animation: true,
        container: wrap,
        placement: 'top',
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

    $(wrap+' select.select-control').each(function(i,select) {
        if (!$(select).hasClass('select2-hidden-accessible')) {
            setTimeout(function(){ $(select).select2(); }, 0);
        }
    });

    if ($(wrap+' .sortable-component').length) {
        $(wrap+' .sortable-component').each(function() {
            var myel = $(this), id = this.id, input = $(this).find('input');
            var unSort = Sortable.create($('#'+id+'-av')[0], {
                group: { name: id, pull: true, put: true },
                sort: false,
                onStart: function(evt) {
                    myel.addClass('on-drag');
                },
                onEnd: function(evt) {
                    myel.removeClass('on-drag');
                },
                onAdd: function(evt) {
                    $(evt.item).find('.item-tools,.details').remove();
                }
            });
            var actSort = Sortable.create($('#'+id+'-ac')[0], {
                group: id,
                sort: true,
                animation: 200,
                dataIdAttr: 'data-id',
                handle: '.item-block',
                filter: '.item-remove, .item-detail, .details',
                onFilter: function (evt) {
                    var ctrl = $(evt.target);
                    if (ctrl.hasClass('item-remove')) {
                        var newitem = $(evt.item).clone();
                        newitem.find('.item-tools,.details').remove();
                        $('#'+id+'-av').append(newitem);
                        $(evt.item).remove();
                        input.val(actSort.toArray().toString());
                    }
                    else if (ctrl.hasClass('item-detail')) {
                        $(evt.item).find('.details').slideToggle('fast');
                    }
                },
                onAdd: function (evt) {
                    var itemEl = $(evt.item);
                    if (!itemEl.find('.item-tools').length) {
                        itemEl.append('<span class="item-tools"><i class="fa fa-times item-remove"></i><i class="fa fa-caret-down item-detail"></i></span>');
                    }
                    if (itemEl.attr('data-desc')) {
                        itemEl.append('<div class="details">'+itemEl.attr('data-desc')+'</div>');
                    }
                },
                onSort: function (evt) {
                    input.val(actSort.toArray().toString());
                }
            });
        });
    }

    $('.java-tab-container').removeClass('is-loading');
    if(javaForm) javaForm.resetForm();
}

var messageTimeout, showMessage = function(type, msg, tout) {
    clearTimeout(messageTimeout);
    var msgWrap = $('div.java-tab-container .message-wrap');
    var timeout = (typeof tout === 'number') ? (tout*1000) : 5000;
    if (msgWrap.find('.alert').length) {
        msgWrap.find('.alert').slideUp();
    }
    if (typeof type === 'undefined') type = 'success';
    var cont = '<div class="alert alert-dismissible alert-'+type+'">';
    cont += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    cont += '<span aria-hidden="true">&times;</span></button>';
    cont += '<p>'+msg+'</p></div>';
    msgWrap.html(cont);
    msgWrap.find('.alert').slideDown();
    messageTimeout = setTimeout(function() {
        msgWrap.find('.alert').slideUp();
    },timeout);
};

$.fn.serializeObject = function() {
    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push": /^$/,
            "fixed": /^\d+$/,
            "named": /^[a-zA-Z0-9_]+$/
        };
    this.build = function(base, key, value) {
        base[key] = value;
        return base;
    };
    this.push_counter = function(key) {
        if (push_counters[key] === undefined) {
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };
    $.each($(this).serializeArray(), function() {
        if (!patterns.validate.test(this.name)) {
            return;
        }
        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;

        while ((k = keys.pop()) !== undefined) {
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
            if (k.match(patterns.push)) {
                merge = self.build([], self.push_counter(reverse_key), merge);
            } else if (k.match(patterns.fixed)) {
                merge = self.build([], k, merge);
            } else if (k.match(patterns.named)) {
                merge = self.build({}, k, merge);
            }
        }
        json = $.extend(true, json, merge);
    });
    return json;
};

$.fn.sortablejs = function(options) {
    var self = this,
        opts = (typeof options === 'object') ? options : {},
        sort = new Sortable(this.id, opts);
    $(this).data('sortable', sort);
    return this;
};
