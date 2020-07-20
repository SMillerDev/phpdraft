/**
 * Created by smillernl on 18-5-17.
 */

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

anchors.options = {
    placement: 'left',
    visible: 'touch',
};
anchors.add('.main-content h1, .main-content h2, .main-content h3, .main-content .card-header a');

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    let contentDom = $('body>div>div.row');

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
});