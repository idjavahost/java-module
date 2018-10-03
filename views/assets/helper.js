var javaForm = null;
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
