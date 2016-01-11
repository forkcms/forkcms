<header id="header">
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header text-center">
        <a class="navbar-brand" href="/{option:SITE_MULTILANGUAGE}{$LANGUAGE}{/option:SITE_MULTILANGUAGE}" title="{$lblVisitWebsite|ucfirst}">
          {$SITE_TITLE}
        </a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-collapse">
        <div class="navbar-left text-center" id="header-navigation">
          {$var|getmainnavigation:'nav navbar-nav'}
        </div>
        <div class="navbar-right text-center">
          <ul class="nav navbar-nav list-inline">
            {option:SITE_MULTILANGUAGE}
            {option:workingLanguages}
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="{$LANGUAGE|uppercase}">
                {$LANGUAGE|uppercase}
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                {iteration:workingLanguages}
                <li{option:workingLanguages.selected} class="active"{/option:workingLanguages.selected}>
                  <a href="{$var|geturl:null:null:null:{$workingLanguages.abbr}}" title="{$workingLanguages.label|ucfirst}">{$workingLanguages.abbr|uppercase}</a>
                </li>
                {/iteration:workingLanguages}
              </ul>
            </li>
            {/option:workingLanguages}
            {/option:SITE_MULTILANGUAGE}
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="{$authenticatedUserNickname}">
                <img src="{$FRONTEND_FILES_URL}/backend_users/avatars/32x32/{$authenticatedUserAvatar}" width="18" height="18" class="img-circle" alt="{$authenticatedUserNickname}" />
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                {option:authenticatedUserEditUrl}
                <li><a href="{$authenticatedUserEditUrl}">{$lblEditProfile|ucfirst}</a></li>
                {/option:authenticatedUserEditUrl}
                <li><a href="{$var|geturl:'logout':'authentication'}">{$lblSignOut|ucfirst}</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</header>
