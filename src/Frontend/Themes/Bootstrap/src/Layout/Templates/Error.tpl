{include:Core/Layout/Templates/Components/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">

{include:Core/Layout/Templates/Components/Notifications.tpl}

{include:Core/Layout/Templates/Components/Navbar.tpl}

<section id="main" class="container">
  <div class="row">
    <div class="col-xs-12">
      {* Main position *}
      <div class="row">
        <div id="errorIcon">
          <img src="{$THEME_URL}/apple-touch-icon-precomposed.png" class="img-circle">
        </div>
      </div>
      {iteration:positionMain}
        <div class="row">
          <div class="col-xs-12">
            {$positionMain.blockContent}
          </div>
        </div>
      {/iteration:positionMain}
    </div>
  </div>
</section>

{include:Core/Layout/Templates/Components/Footer.tpl}

</body>
</html>
