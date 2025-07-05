import { translator } from '../../../../services/translator';

export function contactRenderer(params) {
    if (params.value === undefined) {
        return '<div class="community-contact-loader"><i class="fal fa-circle-notch fa-spin"></i></div>';
    }

    let pictureUrl = 'https://www.gravatar.com/avatar/' + params.data.hash + '?d=mp&s=64';
    if (params.data.picture) {
        pictureUrl = params.data.picture;
    }

    return (
        '<div class="row align-items-center no-gutters community-contact">' +
        '   <div class="col-auto">' +
        '       <img class="community-contact-image" height="32" width="32" src="' +
        pictureUrl +
        '" />' +
        '   </div>' +
        '   <div class="col">' +
        '       <a class="stretched-link community-contact-email" href="' +
        params.data.url +
        '">' +
        '           <strong>' +
        params.value +
        '</strong>' +
        '       </a>' +
        '   <div>' +
        '   <span class="mr-2 world-badge world-badge-sm ' +
        (params.data.type === 'm' ? 'world-badge-success' : '') +
        '">' +
        translator.trans('community.list.type.' + (params.data.type === 'm' ? 'member' : 'contact')) +
        '   </span>' +
        '   <span class="world-badge world-badge-white world-badge-sm text-' +
        (params.data.subscribed ? 'muted' : 'warning') +
        '">' +
        '<i class="fal fa-bell mr-1"></i>' +
        translator.trans('community.list.settings.' + (params.data.subscribed ? 'subscribed' : 'unsubscribed')) +
        '</span>' +
        (!params.data.location
            ? ''
            : '<span class="world-badge world-badge-white text-muted world-badge-sm">' +
              '<i class="fal fa-map-marker-alt mr-1"></i>' +
              (params.data.location.length > 21 ? params.data.location.substr(0, 20) + '…' : params.data.location) +
              '</span>') +
        '</div>' +
        '</div>' +
        '</div>'
    );
}
