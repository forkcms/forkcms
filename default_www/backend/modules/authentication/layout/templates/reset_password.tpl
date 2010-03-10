{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body id="login">

	<!--[if lte IE 6]>
		<style type="text/css" media="screen">
			#debugnotify, #loginBox {
				display: none
			}
			#browserSupport {
				padding: 20px;
				border: 1px solid #DDD;
				width: 400px;
				margin: 40px auto
			}
			#browserSupport h2 {
				padding: 0 0 12px;
			}
			body {
				background: #FFF !important;
			}
		</style>
		<div id="browserSupport" class="content">
			<h2>{$lblBrowserNotSupported}</h2>
			{$errBrowserNotSupported}
		</div>
	<![endif]-->

	{option:debug}<div id="debugnotify"><img src="/backend/core/layout/images/monsters/{$var|rand:1:3}.png" />WARNING: This Fork is<br /> in debug mode</div>{/option:debug}

	<table border="0" cellspacing="0" cellpadding="0" id="loginHolder">
		<tr>
			<td>
				<div id="loginBox" {option:hasError}class="hasError"{/option:hasError}>

					{option:hasError}
					<div id="loginError">
						<div class="errorMessage singleMessage">
							<p>{$errInvalidUsernamePasswordCombination}</p>
						</div>
					</div>
					{/option:hasError}

					<div id="loginBoxTop">
						<h2>{$SITE_TITLE}</h2>
					</div>

					{form:authenticationResetPassword}
						<div class="horizontal">
							{option:error}
							<div class="errorMessage singleMessage">
								<p>{$error}</p>
							</div>{/option:error}
							<div id="loginFields">
								<p>
									<label for="backendPassword">{$lblNewPassword|ucfirst}</label>
									{$txtBackendNewPassword} {$txtBackendNewPasswordError}
								</p>

								<p>
									<label for="backendPasswordRepeated">{$lblRepeatPassword|ucfirst}</label>
									{$txtBackendNewPasswordRepeated} {$txtBackendNewPasswordRepeatedError}
								</p>
							</div>
							<p class="spacing">
								<input id="login" class="inputButton button mainButton" type="submit" name="login" value="{$msgResetPasswordAndSignIn|ucfirst}" />
							</p>
						</div>
					{/form:authenticationResetPassword}
				</div>
			</td>
		</tr>
	</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}