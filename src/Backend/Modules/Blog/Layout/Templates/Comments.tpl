{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblComments|ucfirst}</h2>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <div role="tabpanel">
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
          <a href="#tabPublished" aria-controls="published" role="tab" data-toggle="tab">{$lblPublished|ucfirst}</a>
        </li>
        <li role="presentation">
          <a href="#tabModeration" aria-controls="moderation" role="tab" data-toggle="tab">{$lblWaitingForModeration|ucfirst}</a>
        </li>
        <li role="presentation">
          <a href="#tabSpam" aria-controls="spam" role="tab" data-toggle="tab">{$lblSpam|ucfirst}</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tabPublished">
          <div class="row">
            <div class="col-md-12">
              <h3>{$lblPublished|ucfirst}</h3>
            </div>
          </div>
          {option:dgPublished}
          <form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms" id="commentsPublished">
            <input type="hidden" name="from" value="published" />
            {$dgPublished}
            <div class="modal fade" id="confirmPublishedToSpam" tabindex="-1" role="dialog" aria-labelledby="{$lblSpam|ucfirst}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <span class="modal-title h4">{$lblSpam|ucfirst}</span>
                  </div>
                  <div class="modal-body">
                    <p>{$msgConfirmMassSpam}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="confirmDeletePublished" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <span class="modal-title h4">{$lblDelete|ucfirst}</span>
                  </div>
                  <div class="modal-body">
                    <p>{$msgConfirmMassDelete}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
          {/option:dgPublished}
          {option:!dgPublished}
          <p>{$msgNoComments}</p>
          {/option:!dgPublished}
        </div>
        <div role="tabpanel" class="tab-pane" id="tabModeration">
          <div class="row">
            <div class="col-md-12">
              <h3>{$lblWaitingForModeration|ucfirst}</h3>
            </div>
          </div>
          {option:dgModeration}
          <form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms" id="commentsModeration">
            <input type="hidden" name="from" value="moderation" />
            {$dgModeration}
            <div class="modal fade" id="confirmModerationToSpam" tabindex="-1" role="dialog" aria-labelledby="{$lblSpam|ucfirst}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <span class="modal-title h4">{$lblSpam|ucfirst}</span>
                  </div>
                  <div class="modal-body">
                    <p>{$msgConfirmMassSpam}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="confirmDeleteModeration" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <span class="modal-title h4">{$lblDelete|ucfirst}</span>
                  </div>
                  <div class="modal-body">
                    <p>{$msgConfirmMassDelete}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
          {/option:dgModeration}
          {option:!dgModeration}
          <p>{$msgNoComments}</p>
          {/option:!dgModeration}
        </div>
        <div role="tabpanel" class="tab-pane" id="tabSpam">
          <div class="row">
            <div class="col-md-12">
              <h3>{$lblSpam|ucfirst}</h3>
            </div>
          </div>
          {option:dgSpam}
          <form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms" id="commentsSpam">
            <input type="hidden" name="from" value="spam" />
            <div class="alert alert-info">
              {$msgDeleteAllSpam}
              <a href="{$var|geturl:'delete_spam'}">{$lblDelete|ucfirst}</a>
            </div>
            {$dgSpam}
            <div class="modal fade" id="confirmDeleteSpam" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <span class="modal-title h4">{$lblDelete|ucfirst}</span>
                  </div>
                  <div class="modal-body">
                    <p>{$msgConfirmMassDelete}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
          {/option:dgSpam}
          {option:!dgSpam}
          <p>{$msgNoComments}</p>
          {/option:!dgSpam}
        </div>
      </div>
    </div>
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
