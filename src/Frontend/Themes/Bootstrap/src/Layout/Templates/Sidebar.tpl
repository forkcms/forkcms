{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">

{include:Core/Layout/Templates/Components/Notifications.tpl}
{include:Core/Layout/Templates/Components/Navbar.tpl}

<section id="main">
  <div class="container">

    {include:Core/Layout/Templates/Components/Breadcrumb.tpl}

    <div class="row">
      <div class="col-md-9">
        {* Page title *}
        {option:!hideContentTitle}
        <header class="page-header" role="banner">
          <h1 itemprop="name">{$page.title}</h1>
        </header>
        {/option:!hideContentTitle}

        {* Main position *}
        {iteration:positionMain}
        {option:positionMain.blockIsHTML}
        {$positionMain.blockContent}
        {/option:positionMain.blockIsHTML}
        {option:!positionMain.blockIsHTML}
        {$positionMain.blockContent}
        {/option:!positionMain.blockIsHTML}
        {/iteration:positionMain}
      </div>
      <div class="col-md-3">
        <div class="row">
          {* Right position *}
          {iteration:positionRight}
          <div class="col-md-12">
            {option:positionRight.blockIsHTML}
            {$positionRight.blockContent}
            {/option:positionRight.blockIsHTML}
            {option:!positionRight.blockIsHTML}
            {$positionRight.blockContent}
            {/option:!positionRight.blockIsHTML}
          </div>
          {/iteration:positionRight}
        </div>
      </div>
    </div>
  </div>
</section>
{include:Core/Layout/Templates/Components/Footer.tpl}
</body>
</html>
