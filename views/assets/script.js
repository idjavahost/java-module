$(function() {
    $('div.java-tab-menu>div.list-group>a').click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass('active');
        $(this).addClass('active');
        var index = $(this).index();
        $('div.java-tab>div.java-tab-content').removeClass('active');
        $('div.java-tab>div.java-tab-content').eq(index).addClass('active');
    });
});
