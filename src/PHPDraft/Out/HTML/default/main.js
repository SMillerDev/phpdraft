function getParameters() {
    let result = {};
    let tmp = [];

    if (location.search === '') { return result; }

    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {tmp = item.split("="); result[tmp[0]] = decodeURIComponent(tmp[1]); });
    return result;
};

function trigger_popover() {
    $('[data-toggle="popover"]').popover({
        html: true,
        sanitize: false,
    });
}

function escapeRegExp(str) { return str.replace(/[-\[\]/{}()*+?.\\^$|]/g, "\\$&"); };

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
    let contentDom = $('body>div>div.row');

    let formControlDom = $('h1.media-heading select.form-control');
    let selectedhost = formControlDom.val();
    formControlDom.on('change', function () {
        let html = contentDom.html();
        let re = new RegExp(escapeRegExp(selectedhost), 'g');
        let new_html = html.replace(re, formControlDom.val());
        selectedhost = formControlDom.val();
        contentDom.html(new_html);
        trigger_popover();
    });

    $('table:not(.table)').each(function () {
        $(this).addClass('table');
    });

    let parameters = getParameters();
    Object.keys(parameters).forEach(function(key) {
        let html = contentDom.html();

        const regex = `<span class="attr">${key}</span>: <span class="value">[a-zA-Z0-9\ \\\-\/]*</span>`;
        let list_re = new RegExp(regex, 'g');

        const curl_regex = `-H '${key}: [a-zA-Z0-9\ \\\-\/]*'`;
        let curl_re = new RegExp(curl_regex, 'g');

        let new_html = html.replace(list_re, `<span class="attr">${key}</span>: <span class="value">${parameters[key]}</span>`)
                           .replace(curl_re, `-H '${key}: ${parameters[key]}'`);
        contentDom.html(new_html);
    });
    trigger_popover();
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