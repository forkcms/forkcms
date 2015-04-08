<div class="modal fade jsConfirmation" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title h4">{$lblDelete|ucfirst}</span>
      </div>
      <div class="modal-body">
        <p class="jsConfirmationMessage">{$msgConfirmation}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
        <a href="#" class="btn btn-primary jsConfirmationSubmit">
          {$lblOK|ucfirst}
        </a>
      </div>
    </div>
  </div>
</div>
