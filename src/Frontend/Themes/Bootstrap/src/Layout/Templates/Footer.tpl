<footer id="footer" role="contentinfo">
  <div class="container">
    <p class="pull-right"><a class="backToTop" data-scroll href="#">Back to top</a></p>
    <nav>
      <ul class="nav nav-pills" role="navigation">
        <li class="disabled"><a>© <span itemprop="copyrightYear">{$now|date:'Y'}</span> {$siteTitle}</a></li>
        {iteration:footerLinks}
        <li>
        <a href="{$footerLinks.url}" title="{$footerLinks.title}"{option:footerLinks.rel} rel="{$footerLinks.rel}"{/option:footerLinks.rel}>
          {$footerLinks.navigation_title}
        </a>
        </li>
        {/iteration:footerLinks}
        <li><a href="http://www.sumocoders.be/?utm_source={$siteTitle|urlencode}&amp;utm_medium=credits&amp;utm_campaign=client_sites" rel="external">SumoCoders</a></li>
      </ul>
    </nav>
  </div>
</footer>

{* Site wide HTML *}
{$siteHTMLFooter}

{* General Javascript *}
{iteration:jsFiles}
  <script src="{$jsFiles.file}"></script>
{/iteration:jsFiles}

<script src="{$THEME_URL}/Core/Js/bundle.js?t={$LAST_MODIFIED_TIME}"></script>

{* @todo Remove when needed *}
<div id="fb-root"></div>
<script>
(function(d, s, id) {
 var js, fjs = d.getElementsByTagName(s)[0];
 if (d.getElementById(id)) return;
 js = d.createElement(s); js.id = id;
 js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
 fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));
</script>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
