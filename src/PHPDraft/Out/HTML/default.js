$(function () {
    $('[data-toggle="popover"]').popover();
});

$('.collapse.request-panel').on('shown.bs.collapse', function () {
    $(this).parent().find('h4.request .glyphicon.indicator').removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');
}).on('hidden.bs.collapse', function () {
    $(this).parent().find('h4.request .glyphicon.indicator').removeClass('glyphicon-menu-down').addClass('glyphicon-menu-up');
});

$('.collapse.response-panel').on('shown.bs.collapse', function () {
    $(this).parent().find('h4.response .glyphicon.indicator').removeClass('glyphicon-menu-up').addClass("glyphicon-menu-down");
}).on('hidden.bs.collapse', function () {
    $(this).parent().find('h4.response .glyphicon.indicator').removeClass('glyphicon-menu-down').addClass("glyphicon-menu-up");
});

$('pre.collapse.response-body').on('shown.bs.collapse', function () {
    $(this).parent().find('h5.response-body .glyphicon.indicator').removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');
}).on('hidden.bs.collapse', function () {
    $(this).parent().find('h5.response-body .glyphicon.indicator').removeClass('glyphicon-menu-down').addClass('glyphicon-menu-up');
});