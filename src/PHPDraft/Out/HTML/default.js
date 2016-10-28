$(function () {
    $('[data-toggle="popover"]').popover();
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
    var selectedhost = $('h1.media-heading select.form-control').val();
    $('h1.media-heading select.form-control').on('change', function () {
        var html = $('body>div>div.row').html();
        var re = new RegExp(escapeRegExp(selectedhost), 'g');
        html = html.replace(re, $('h1.media-heading select.form-control').val());
        selectedhost = $('h1.media-heading select.form-control').val();
        $('body>div>div.row').html(html);
        $('[data-toggle="popover"]').popover();
    });

    function escapeRegExp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    };
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