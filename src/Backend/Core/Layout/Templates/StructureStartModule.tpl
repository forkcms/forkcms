<body id="{$bodyID}" class="{$bodyClass}">
  <div id="root">
  {include:{$BACKEND_CORE_PATH}/Layout/Templates/Header.tpl}
  <div id="content" class="container">
    <div class="row">
      <div class="col-md-3">
        {include:{$BACKEND_CORE_PATH}/Layout/Templates/Subnavigation.tpl}
        {include:{$BACKEND_CORE_PATH}/Layout/Templates/Switch.tpl}
      </div>
      <div class="col-md-9">
        <div class="panel panel-default">
          <div class="panel-body">
