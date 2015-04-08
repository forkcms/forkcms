<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">
      {$lblStatistics|ucfirst} {$lblFrom} {$startTimestamp|formatdate} {$lblTill} {$endTimestamp|formatdate}
    </h3>
  </div>
  {form:periodPickerForm}
    <div class="panel-body">
      <div class="row">
        <div class="col-md-4">
          {option:txtEndDateError}
          <p class="text-danger">{$txtEndDateError}</p>
          {/option:txtEndDateError}
          {option:txtStartDateError}
          <p class="text-danger">{$txtStartDateError}</p>
          {/option:txtStartDateError}
          <div class="form-group">
            <label for="startDate">{$lblStartDate|ucfirst}</label>
            {$txtStartDate}
          </div>
          <div class="form-group">
            <label for="endDate">{$lblEndDate|ucfirst}</label>
            {$txtEndDate}
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      <div class="btn-toolbar">
        <div class="btn-group pull-right">
          {option:liveDataURL}
          <a href="{$liveDataURL}" title="{$lblGetLiveData|ucfirst}" class="btn btn-default">
            <span class="glyphicon glyphicon-globe"></span>&nbsp;
            {$lblGetLiveData|ucfirst}
          </a>
          {/option:liveDataURL}
          <button id="update" class="btn btn-primary" type="submit" name="update">
            <span class="glyphicon glyphicon-download-alt"></span>&nbsp;
            {$lblChangePeriod|ucfirst}
          </button>
        </div>
      </div>
    </div>
  {/form:periodPickerForm}
</div>
