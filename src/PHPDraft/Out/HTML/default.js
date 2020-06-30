$(function () {
    $('[data-toggle="popover"]').popover({
        html: true,
        sanitize: false,
    });
    $('[data-toggle="tooltip"]').tooltip();
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

    function escapeRegExp(str)
    {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    };
    $('table:not(.table)').each(function () {
        $(this).addClass('table');});
});

$('.collapse.request-card').on('shown.bs.collapse', function () {
    $(this).parent().find('h6.request .fas.indicator').removeClass('fa-angle-up').addClass('fa-angle-down');
}).on('hidden.bs.collapse', function () {
    $(this).parent().find('h6.request .fas.indicator').removeClass('fa-angle-down').addClass('fa-angle-up');
});

$('.collapse.response-card').on('shown.bs.collapse', function () {
    $(this).parent().find('h6.response .fas.indicator').removeClass('fa-angle-up').addClass("fa-angle-down");
}).on('hidden.bs.collapse', function () {
    $(this).parent().find('h6.response .fas.indicator').removeClass('fa-angle-down').addClass("fa-angle-up");
});

$('pre.collapse.response-body').on('shown.bs.collapse', function () {
    $(this).parent().find('h6.response-body .fas.indicator').removeClass('fa-angle-up').addClass('fa-angle-down');
}).on('hidden.bs.collapse', function () {
    $(this).parent().find('h6.response-body .fas.indicator').removeClass('fa-angle-down').addClass('fa-angle-up');
});

anchors.options = {
    placement: 'left',
    visible: 'touch',
};
anchors.add('.main-content h1, .main-content h2, .main-content h3, .main-content .card-header a');