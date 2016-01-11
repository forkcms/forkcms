<div id="widgetMailmotorClassic" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      <a href="{$var|geturl:'index':'mailmotor'}">{$lblMailmotor|ucfirst}</a>
    </h2>
  </div>
  <div class="panel-body">
    <div class="fork-tabs" role="tabpanel">
      <ul class="nav nav-tabs nav-tabs-xs" role="tablist">
        <li role="presentation" class="active">
          <a href="#tabMailmotorSubscriptions" aria-controls="home" role="tab" data-toggle="tab">{$lblSubscriptions|ucfirst}</a>
        </li>
        <li role="presentation">
          <a href="#tabMailmotorUnsubscriptions" aria-controls="profile" role="tab" data-toggle="tab">{$lblUnsubscriptions|ucfirst}</a>
        </li>
        <li role="presentation">
          <a href="#tabMailmotorStatistics" aria-controls="messages" role="tab" data-toggle="tab">{$lblStatistics|ucfirst}</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tabMailmotorSubscriptions">
          <div id="dataGridSubscriptions">
            {option:dgMailmotorSubscriptions}
            <div class="table table-striped dataGridHolder">
              {$dgMailmotorSubscriptions}
            </div>
            {/option:dgMailmotorSubscriptions}
            {option:!dgMailmotorSubscriptions}
            <p>
              {$msgNoSubscriptions|ucfirst}
            </p>
            {/option:!dgMailmotorSubscriptions}
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="tabMailmotorUnsubscriptions">
          <div id="dataGridUnsubscriptions">
            {option:dgMailmotorUnsubscriptions}
            <div class="table table-striped dataGridHolder" >
              {$dgMailmotorUnsubscriptions}
            </div>
            {/option:dgMailmotorUnsubscriptions}
            {option:!dgMailmotorUnsubscriptions}
            <p>
              {$msgNoUnsubscriptions|ucfirst}
            </p>
            {/option:!dgMailmotorUnsubscriptions}
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="tabMailmotorStatistics">
          <div id="dataGridStatistics">
            {option:dgMailmotorStatistics}
            <div class="table table-striped dataGridHolder">
              {$dgMailmotorStatistics}
            </div>
            {/option:dgMailmotorStatistics}
            {option:!dgMailmotorStatistics}
            <p>
              {$msgNoSentMailings|ucfirst}
            </p>
            {/option:!dgMailmotorStatistics}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$var|geturl:'addresses':'mailmotor'}" class="btn"><span>{$msgAllAddresses|ucfirst}</span></a>
      </div>
    </div>
  </div>
</div>
