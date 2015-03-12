{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
{include:Core/Layout/Templates/Notifications.tpl}

{include:Core/Layout/Templates/Navbar.tpl}

<section id="main">
  <div class="container">

    {include:Core/Layout/Templates/Breadcrumb.tpl}

    <div class="row">
      <div class="col-xs-12">

        {option:!hideContentTitle}
        <header class="page-header" role="banner">
          <h1 itemprop="name">{$page.title}</h1>
        </header>
        {/option:!hideContentTitle}

        {iteration:positionMain}
          <div class="row">
            <div class="col-xs-12">
              {$positionMain.blockContent}
            </div>
          </div>
        {/iteration:positionMain}
      </div>
    </div>
  </div>

</section>

{include:Core/Layout/Templates/Footer.tpl}

</body>
</html>
