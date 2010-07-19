<div id="headerHolder">
	<h1><a href="/" title="{$lblVisitWebsite|ucfirst}">{$SITE_TITLE}</a></h1>
	<table cellspacing="0" cellpadding="0" id="header">
		<tr>
			<td id="navigation">
				{$var|getmainnavigation}
				<li class="settings">
					<a href="{$var|geturl:'index':'settings'}" class="icon iconSettings">
						{$lblSettings|ucfirst}
					</a>
				</li>
			</td>
			<td id="user">
				<ul>
					{option:debug}
						<li>
							<div id="debugnotify">{$lblDebugMode|ucfirst}</div>
						</li>
					{/option:debug}
					{option:workingLanguages}
						<li>
							{$lblWebsiteWorkingLanguage|ucfirst}:
							<select id="workingLanguage">
								{iteration:workingLanguages}
									<option{option:workingLanguages.selected} selected="selected"{/option:workingLanguages.selected} value="{$workingLanguages.abbr}">{$workingLanguages.label|ucfirst}</option>
								{/iteration:workingLanguages}
							</select>
						</li>
					{/option:workingLanguages}
					
					{* @todo move this to the real javascript *}
					<script type="text/javascript" charset="utf-8">
						$(document).ready(function() {
							$('#openAccountDropdown').click(function(e) {
								e.preventDefault();
								$('#ddAccount').slideToggle('fast');
							});
						});
						
					</script>
					
					<li id="account">
						<a href="#ddAccount" id="openAccountDropdown">Account <span class="arrow">&#x25BC;</span></a>
						<ul class="hidden" id="ddAccount">
							<li>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<div class="avatar av24">
												<a href="{$authenticatedUserEditUrl}">
													<img src="{$FRONTEND_FILES_URL}/backend_users/avatars/32x32/{$authenticatedUserAvatar}" width="24" height="24" alt="{$authenticatedUserNickname}" />
												</a>
											</div>
										</td>
										<td>{$authenticatedUserNickname}</td>
									</tr>
								</table>
							</li>
							<li>
								<a href="{$authenticatedUserEditUrl}">My profile</a>
							</li>
							<li>
								<a href="{$var|geturl:'logout':'authentication'}">{$lblSignOut|ucfirst}</a>
							</li>
						</ul>
					</li>
				</ul>
			</td>
		</tr>
	</table>
</div>