{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	{include:core/layout/templates/notifications.tpl}

    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/" class="navbar-brand">{$siteTitle}</a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            {$var|getnavigation:'page':0:1}
            {iteration:positionTop}
                {$positionTop.blockContent}
            {/iteration:positionTop}
            {include:core/layout/templates/languages.tpl}
        </div>
    </nav>

	<section id="main">
        <div class="container">

            {include:core/layout/templates/breadcrumb.tpl}

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
    {include:core/layout/templates/footer.tpl}
</body>
</html>
