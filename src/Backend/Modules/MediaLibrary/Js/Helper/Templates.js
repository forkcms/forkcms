import { MediaLibraryHelper } from '../MediaLibraryHelper'

export class Templates {
  /**
   * Get HTML for empty table row
   *
   * @returns {string}
   */
  static getHTMLForEmptyTableRow () {
    return '<tr><td>' + jsBackend.locale.msg('MediaNoItemsInFolder') + '</td></tr>'
  }

  /**
   * Get HTML for MediaFolders to show in dropdown
   *
   * @param {array} mediaFolders The mediaFolderCacheItem entity array.
   * @returns {string}
   */
  static getHTMLForMediaFolders (mediaFolders) {
    let html = ''

    $(mediaFolders).each((i, mediaFolder) => {
      html += this.getHTMLForMediaFolder(mediaFolder)
    })

    return html
  }

  /**
   * Get HTML for MediaFolder to show in dropdown
   *
   * @param {array} mediaFolder The mediaFolderCacheItem entity array.
   * @returns {string}
   */
  static getHTMLForMediaFolder (mediaFolder) {
    let html = ''
    let count = 0

    // redefine count
    if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count && MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count[MediaLibraryHelper.mediaFolder.id]) {
      count = MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count[MediaLibraryHelper.mediaFolder.id]
    }

    // add to html
    html += '<option value="' + mediaFolder.id + '">'
    html += '   ' + mediaFolder.slug + ' (' + count + '/' + mediaFolder.numberOfMediaItems + ')'
    html += '</option>'

    if (mediaFolder.numberOfChildren > 0) {
      html += Templates.getHTMLForMediaFolders(mediaFolder.children)
    }

    return html
  }

  /**
   * Get HTML for MediaItem to connect
   *
   * @param {array} mediaItem The mediaItem entity array.
   * @returns {string}
   */
  static getHTMLForMediaItemToConnect (mediaItem) {
    let html = '<li id="media-' + mediaItem.id + '" class="ui-state-default">'
    html += '<div class="mediaHolder mediaHolder' + utils.string.ucfirst(mediaItem.type) + '" data-fork="mediaItem" data-folder-id="' + mediaItem.folder.id + '" data-media-id="' + mediaItem.id + '">'

    if (mediaItem.type === 'image') {
      html += '<img src="' + mediaItem.preview_source + '" alt="' + mediaItem.title + '" title="' + mediaItem.title + '"/>'
    } else if (mediaItem.type === 'movie') {
      html += '<img src="" alt="' + mediaItem.title + '" title="' + mediaItem.title + '" data-media-item-movie data-video-id="' + mediaItem.url + '"/>'
      html += '<div class="icon"><span class="fas fa-play-circle"></span></div>'
      html += '<div class="url">' + mediaItem.url + '</div>'
    } else {
      html += '<div class="icon"></div>'
      html += '<div class="url">' + mediaItem.url + '</div>'
    }
    html += '<button type="button" class="deleteMediaItem btn btn-danger btn-sm btn-icon-only" data-fork="disconnect" '
    html += 'title="' + utils.string.ucfirst(jsBackend.locale.lbl('MediaDisconnect')) + '">'
    html += '<span class="sr-only">' + utils.string.ucfirst(jsBackend.locale.lbl('MediaDisconnect')) + '</span>'
    html += '<i class="fas fa-times"></i>'
    html += '</button>'
    html += '</div>'
    html += '</li>'

    return html
  }

  /**
   * Get HTML for MediaItem table row
   *
   * @param {array} mediaItem The mediaItem entity array.
   * @param {bool} connected
   * @returns {string}
   */
  static getHTMLForMediaItemTableRow (mediaItem, connected) {
    let html = '<tr id="media-' + mediaItem.id + '" class="row' + utils.string.ucfirst(mediaItem.type) + '">'
    html += '<td class="check">'
    html += '<input type="checkbox" autocomplete="off" class="toggleConnectedCheckbox" id="media-' + mediaItem.id + '-checkbox"'

    if (connected) {
      html += ' checked="checked"'
    }

    html += '/></td>'

    if (mediaItem.type === 'image') {
      html += '<td class="fullUrl">'
      html += '<label for="media-' + mediaItem.id + '-checkbox">'
      html += '<img src="' + mediaItem.preview_source + '" alt="' + mediaItem.title + '" height="50" />'
      html += '</label>'
      html += '</td>'
    }

    html += '<td class="url"><label for="media-' + mediaItem.id + '-checkbox">' + mediaItem.url + '</label></td>'
    html += '<td class="title"><label for="media-' + mediaItem.id + '-checkbox">' + mediaItem.title + '</label></td>'
    if (mediaItem.type === 'image') {
      html += '<td class="duplicate">'
      html += '<button type="button" data-media-item-id="' + mediaItem.id + '" data-role="media-library-duplicate-and-crop" class="btn btn-primary btn-icon-only" title="' + utils.string.ucfirst(jsBackend.locale.lbl('MediaItemDuplicate')) + '">'
      html += '<span class="fa fa-copy" aria-hidden="true"></span>'
      html += '</button>'
      html += '</td>'
    }
    html += '</tr>'

    return html
  }

  /**
   * Get HTML for uploaded MediaItem
   *
   * @param {array} mediaItem - This is the media-item that ajax returned for us.
   * @return {string}
   */
  static getHTMLForUploadedMediaItem (mediaItem) {
    jsBackend.mediaLibraryHelper.movieThumbUrl.set(mediaItem)

    // init html
    let html = ''

    // create element
    html += '<li id="media-' + mediaItem.id + '" class="ui-state-default">'
    html += '<div class="mediaHolder mediaHolder' + utils.string.ucfirst(mediaItem.type) + '" data-fork="mediaItem" data-folder-id="' + mediaItem.folder.id + '" data-media-id="' + mediaItem.id + '">'

    // is image
    if (mediaItem.type === 'image') {
      html += '<img src="' + mediaItem.preview_source + '" alt="' + mediaItem.title + '" title="' + mediaItem.title + '"/>'
      // is file, movie or audio
    } else if (mediaItem.type === 'movie') {
      html += '<img src="" alt="' + mediaItem.title + '" title="' + mediaItem.title + '" data-media-item-movie data-video-id="' + mediaItem.url + '"/>'
      html += '<div class="icon"><span class="fas fa-play-circle"></span></div>'
      html += '<div class="url">' + mediaItem.url + '</div>'
    } else {
      html += '<div class="icon"><span class="fas fa-play-circle"></span></div>'
      html += '<div class="url">' + mediaItem.url + '</div>'
    }

    html += '<button type="button" class="deleteMediaItem btn btn-danger btn-sm btn-icon-only" data-fork="disconnect" '
    html += 'title="' + utils.string.ucfirst(jsBackend.locale.lbl('MediaDisconnect')) + '">'
    html += '<i class="fas fa-times"></i>'
    html += '<span class="sr-only">' + utils.string.ucfirst(jsBackend.locale.lbl('MediaDisconnect')) + '</span>'
    html += '</button>'
    html += '</div>'
    html += '</li>'

    return html
  }
}
