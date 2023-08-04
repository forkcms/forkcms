export class ModalSelection {
  constructor () {
    $('button[data-direct-url]').on('click', this.selectItemAndSendToParent)
  }

  selectItemAndSendToParent () {
    const $this = $(this)
    const directUrl = $this.data('directUrl')
    window.opener.postMessage({'media-url': directUrl, 'id': $this.closest('tr').attr('id').replace('row-', '')}, '*')
    window.close()
  }

  sendToParent (e) {
    const $this = $(e.currentTarget)
    window.opener.postMessage({'media-url': $this.data('directUrl'), 'id': $this.closest('[data-media-id]').data('mediaId')}, '*')
    window.close()
  }
}
