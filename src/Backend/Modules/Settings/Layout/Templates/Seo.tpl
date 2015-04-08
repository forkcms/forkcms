{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblSEOSettings|ucfirst}</h2>
  </div>
</div>
{form:settingsSeo}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblSEO|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="seoNoodp">{$chkSeoNoodp} NOODP</label>
                <p class="text-info">{$msgHelpSEONoodp}</p>
              </li>
              <li class="checkbox">
                <label for="seoNoydir">{$chkSeoNoydir} NOYDIR</label>
                <p class="text-info">{$msgHelpSEONoydir}</p>
              </li>
              <li class="checkbox">
                <label for="seoNofollowInComments">{$chkSeoNofollowInComments} {$msgSEONoFollowInComments}</label>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settingsSeo}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
