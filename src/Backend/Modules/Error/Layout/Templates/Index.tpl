{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
<body id="{$bodyID}" class="{$bodyClass}">
  <div class="page-header text-center">
    <h1>{$SITE_TITLE}</h1>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title">
              {$lblError|ucfirst}
            </h2>
          </div>
          <div class="panel-body">
            <p>{$message}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
