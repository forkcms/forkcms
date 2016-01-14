{* This is the HTML content, hidden *}
<div class="modal fade" id="editContent" tabindex="-1" role="dialog" aria-labelledby="{$lblEditor|ucfirst}"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="modal-title h4">{$lblEditor|ucfirst}</span>
            </div>
            <div class="modal-body">
                {iteration:positions}
                {iteration:positions.blocks}
                <div class="alert alert-warning">
                    {$msgContentSaveWarning}
                </div>
                <div class="box contentBlock" style="margin: 0;">
                    <div class="blockContentHTML optionsRTE">
                        <fieldset>
                            {$positions.blocks.txtHTML}
                            {$positions.blocks.txtHTMLError}
                        </fieldset>
                    </div>

                    {* this will store the selected extra *}
                    {$positions.blocks.hidExtraId}

                    {* this will store the selected position *}
                    {$positions.blocks.hidPosition}

                    {* this will store the visible/hidden state *}
                    <div style="display: none">{$positions.blocks.chkVisible}</div>
                </div>
                {/iteration:positions.blocks}
                {/iteration:positions}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="editContentSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog to select the content (editor, module or widget). Do not change the ID! *}
<div class="modal fade" id="addBlock" tabindex="-1" role="dialog" aria-labelledby="{$lblChooseContent|ucfirst}"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="modal-title h4">{$lblChooseContent|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="extraForBlock" name="extraForBlock" value=""/>

                <p>{$msgHelpBlockContent}</p>

                <div id="extraWarningAlreadyBlock" class="alert alert-warning">{$msgModuleBlockAlreadyLinked}</div>
                <div id="extraWarningHomeNoBlock" class="alert alert-warning">{$msgHomeNoBlock}</div>
                <div class="form-group">
                    <label for="extraType" class="control-label">{$lblType|ucfirst}</label>
                    {$ddmExtraType}
                </div>
                <div id="extraModuleHolder" class="form-group" style="display: none;">
                    <label for="extraModule" class="control-label">{$lblWhichModule|ucfirst}</label>
                    <select id="extraModule" class="form-control">
                        <option value="-1">-</option>
                    </select>
                </div>
                <div id="extraExtraIdHolder" class="form-group" style="display: none;">
                    <label for="extraExtraId" class="control-label">{$lblWhichWidget|ucfirst}</label>
                    <select id="extraExtraId" class="form-control">
                        <option value="-1">-</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="addBlockSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog to select another template. Do not change the ID! *}
<div class="modal fade" id="changeTemplate" tabindex="-1" role="dialog"
     aria-labelledby="{$lblChooseATemplate|ucfirst}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="modal-title h4">{$lblChooseATemplate|ucfirst}</span>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">{$msgTemplateChangeWarning}</div>
                <div id="templateList">
                    <div class="row">
                        {iteration:templates}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="radio">
                                        <label for="template{$templates.id}" class="control-label">
                                            <input type="radio" id="template{$templates.id}" value="{$templates.id}"
                                              name="template_id_chooser"
                                              class="inputRadio"{option:templates.checked} checked="checked"{/option:templates.checked}{option:templates.disabled} disabled="disabled"{/option:templates.disabled} />{$templates.label}
                                        </label>
                                    </div>

                                    <div class="templateVisual current">
                                        {$templates.html}
                                    </div>
                                </div>
                            </div>
                        {cycle:'':'</div><div class="row">'}
                        {/iteration:templates}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="changeTemplateSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog to confirm block removal. Do not change the ID! *}
<div class="modal fade" id="confirmDeleteBlock" tabindex="-1" role="dialog" aria-labelledby="{$lblDeleteBlock|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblDeleteBlock|ucfirst}</span>
            </div>
            <div class="modal-body">
                <p>{$msgConfirmDeleteBlock}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="confirmDeleteBlockSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Text editor block *}
<div class="modal fade" id="blockHtml" role="dialog" aria-labelledby="{$lblEditor|ucfirst}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <span class="modal-title h4">{$lblEditor|ucfirst}</span>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    {$msgContentSaveWarning}
                </div>
                <div class="form-group{option:txtHtmlError} has-error{/option:txtHtmlError}">
                    {$txtHtml}
                    {$txtHtmlError}
                </div>
            </div>
            <div class="modal-footer">
                <button id="blockHtmlCancel" type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="blockHtmlSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Page delete confirm block *}
{option:item}
<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
                <p>{$msgConfirmDelete|sprintf:{$item.title}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <a href="{$var|geturl:'delete'}&amp;id={$item.id}" class="btn btn-primary">
                    {$lblOK|ucfirst}
                </a>
            </div>
        </div>
    </div>
</div>
{/option:item}
