{include:Core/Layout/Templates/Components/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
{include:Core/Layout/Templates/Components/Notifications.tpl}

{include:Core/Layout/Templates/Components/Navbar.tpl}

<section id="main">
  <div class="container">

    {include:Core/Layout/Templates/Components/Breadcrumb.tpl}

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

{include:Core/Layout/Templates/Components/Footer.tpl}

</body>
</html>
