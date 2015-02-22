{option:warnings}
<div id="widgetSettingsAnalyse" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">{$lblAnalysis|ucfirst}</h2>
  </div>
  <div class="panel-body">
    <ul>
      {iteration:warnings}
      <li>{$warnings.message}</li>
      {/iteration:warnings}
    </ul>
  </div>
</div>
{/option:warnings}
