<nav class="navbar navbar-default navbar-static-top" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>

    <h1>
      <a href="/" class="navbar-brand">
        {$siteTitle}
      </a>
    </h1>
  </div>

  <div class="collapse navbar-collapse navbar-ex1-collapse">
    {$var|getnavigation:'page':0:1}

    {iteration:positionTop}
      {$positionTop.blockContent}
    {/iteration:positionTop}

    {include:Core/Layout/Templates/Languages.tpl}
  </div>
</nav>

