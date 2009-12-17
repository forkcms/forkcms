{include:file="{$BACKEND_CORE_PATH}/layout/templates/head.tpl"}
<body>
	{option:debug}<div id="debugnotify">Debug mode</div>{/option:debug}
	<table border="0" cellspacing="0" cellpadding="0" id="encloser" width="100%" height="100%">
		<tr>
			<td>
				<div id="headerHolder">
					<table cellspacing="0" cellpadding="0" id="header">
						<tr>
							<td id="siteTitle" width="266">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<h1>
												<a href="/">{$SITE_TITLE|truncate:20}</a>
											</h1>
										</td>
										<td>
											<select id="workingLanguage">
												{iteration:workingLanguages}
												<option{option:workingLanguages.selected} selected="selected"{/option:workingLanguages.selected} value="{$workingLanguages.abbr}">{$workingLanguages.label}</option>
												{/iteration:workingLanguages}
											</select>
										</td>
									</tr>
								</table>
							</td>
							<td id="navigation">
								{$var|getmainnavigation}
							</td>
							<td id="user">
								<ul>
									<li class="settings"><a href="{$var|geturl:'index':'settings'}">{$lblSettings|ucfirst}</a></li>
									<li>
										<table border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td><a class="user" href="{$authenticatedUserEditUrl}">{$authenticatedUserNickname}</a></td>
												<td><a href="{$authenticatedUserEditUrl}"><img src="{$FRONTEND_FILES_URL}/backend_users/avatars/32x32/{$authenticatedUserAvatar}" width="24" height="24" alt="{$authenticatedUserNickname}" class="avatar"></a></td>
												<td><a href="{$var|geturl:'logout':'authentication'}">{$lblSignOut|ucfirst}</a>
											</tr>
										</table>
									</li>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td id="container">
				<div id="main">
					{option:errorMessage}<div class="error">{$errorMessage}</div>{/option:errorMessage}
					{option:formError}<div class="error">{$errGeneralFormError}</div>{/option:formError}