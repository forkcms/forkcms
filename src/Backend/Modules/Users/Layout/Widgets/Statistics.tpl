<div id="widgetUsersStatistics" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      <a href="{$authenticatedUserEditUrl}">{$lblUsers|ucfirst}: {$lblStatistics|ucfirst}</a>
    </h2>
  </div>
  <table class="table table-striped">
    <tr>
      <th>{$lblLastLogin|ucfirst}:</th>
      <td>
        {option:authenticatedUserLastLogin}{$authenticatedUserLastLogin|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}{/option:authenticatedUserLastLogin}
        {option:!authenticatedUserLastLogin}{$lblNoPreviousLogin}{/option:!authenticatedUserLastLogin}
      </td>
    </tr>
    {option:authenticatedUserLastFailedLoginAttempt}
    <tr>
      <th>{$lblLastFailedLoginAttempt|ucfirst}:</th>
      <td>{$authenticatedUserLastFailedLoginAttempt|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}</td>
    </tr>
    {/option:authenticatedUserLastFailedLoginAttempt}
    <tr>
      <th>{$lblLastPasswordChange|ucfirst}:</th>
      <td>
        {option:authenticatedUserLastPasswordChange}{$authenticatedUserLastPasswordChange|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}{/option:authenticatedUserLastPasswordChange}
        {option:!authenticatedUserLastPasswordChange}{$lblNever}{/option:!authenticatedUserLastPasswordChange}
      </td>
    </tr>
    {option:showPasswordStrength}
    <tr>
      <th>{$lblPasswordStrength|ucfirst}:</th>
      <td>{$passwordStrengthLabel}</td>
    </tr>
    {/option:showPasswordStrength}
  </table>
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$authenticatedUserEditUrl}" class="btn">
          <span>{$lblEditProfile|ucfirst}</span>
        </a>
      </div>
    </div>
  </div>
</div>
