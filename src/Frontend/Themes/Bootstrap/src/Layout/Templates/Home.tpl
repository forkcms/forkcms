{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">

{include:Core/Layout/Templates/Notifications.tpl}

{include:Core/Layout/Templates/Navbar.tpl}

{option:positionSlideshow}
<div id="myCarousel" class="carousel slide">
  <div class="carousel-inner">
    {* Slideshow position *}
    {iteration:positionSlideshow}
      <div class="item{option:positionSlideshow.first} active{/option:positionSlideshow.first}">
        {$positionSlideshow.blockContent}
      </div>
    {/iteration:positionSlideshow}
  </div>

  <a class="left carousel-control" data-no-scroll rel="previous" href="#myCarousel" data-slide="prev"><span class="icon-prev"></span><span class="sr-only"> {$lblPrevious}</span></a>
  <a class="right carousel-control" data-no-scroll rel="next" href="#myCarousel" data-slide="next"><span class="sr-only">{$lblNext} </span><span class="icon-next"></span></a>
</div>
{/option:positionSlideshow}

<section id="main">
  <div class="container">

    {option:positionFeatures}
    <div class="row marketing">
      {iteration:positionFeatures}
        {$positionFeatures.blockContent}
      {/iteration:positionFeatures}
    </div>
    {/option:positionFeatures}

    {iteration:positionMain}
      {option:positionMain.blockIsHTML}
        <hr class="featurette-divider">
        {$positionMain.blockContent}
      {/option:positionMain.blockIsHTML}
      {option:!positionMain.blockIsHTML}
        {$positionMain.blockContent}
      {/option:!positionMain.blockIsHTML}
    {/iteration:positionMain}

  </div>
</section>

{include:Core/Layout/Templates/Footer.tpl}

</body>
</html>
