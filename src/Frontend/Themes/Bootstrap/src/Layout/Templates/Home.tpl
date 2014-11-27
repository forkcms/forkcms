{* Write your section-titles likes this when you are making a one-pager: <h2><a name="usefulName" href="#" class="nonVisibleAnchor"></a></h2> *}

{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
    {include:Core/Layout/Templates/Notifications.tpl}

    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <h1>
                <a href="/" class="navbar-brand">{$siteTitle}</a>
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
                        {option:positionFeatures.blockIsHTML}
                            {$positionFeatures.blockContent}
                        {/option:positionFeatures.blockIsHTML}
                        {option:!positionFeatures.blockIsHTML}
                            {$positionFeatures.blockContent}
                        {/option:!positionFeatures.blockIsHTML}
                    {/iteration:positionFeatures}
                </div>
            {/option:positionFeatures}

            {* Main position *}
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
