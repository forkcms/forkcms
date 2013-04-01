<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>API client</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="layout/css/screen.css" />

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<script type="text/javascript" src="/frontend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/client.js"></script>
</head>
<body class="en onsite">
	<div id="container">
		<ul id="modules">
			<li class="module">
				<h3>API</h3>
				<div class="configurationWrapper">
					<div class="configuration clearfix">
						<h4>Configuration</h4>
						<p>
							<label for="url">URL:</label>
							<input type="text" name="url" id="url" value="{$url}" class="input-text-big" disabled="disabled" />
						</p>
						<p>
							<label for="email">E-mail:</label>
							<input type="text" name="email" id="email" value="" class="input-text-big" />
						</p>
						<p>
							<label for="nonce">Nonce:</label>
							<input type="text" name="nonce" id="nonce" value="" class="input-text-big" />
						</p>
						<p>
							<label for="secret">Secret:</label>
							<input type="text" name="secret" id="secret" value="" class="input-text-big" />
						</p>
						<div class="col">
							Request method:
							<ul>
								<li>
									<label for="get">GET</label>
									<input type="radio" name="method" value="GET" id="get" checked="checked" />
								</li>
								<li>
									<label for="post">POST</label>
									<input type="radio" name="method" value="POST" id="post" />
								</li>
								<li>
									<label for="delete">DELETE</label>
									<input type="radio" name="method" value="DELETE" id="delete" />
								</li>
							</ul>
						</div>
						<div class="col">
							Output format:
							<ul>
								<li>
									<label for="json">JSON</label>
									<input type="radio" name="format" value="json" id="json" checked="checked" />
								</li>
							</ul>
						</div>
						<div class="col">
							Language:
							<ul>
								<li>
									<label for="nl">NL</label>
									<input type="radio" name="language" value="nl" id="nl" checked="checked" />
								</li>
							</ul>
						</div>
					</div>
				</div>
			</li>

			{iteration:modules}
			<li class="module">
				<a href="#{$modules.name}">[<span class="toggle">+</span>] {$modules.name}</a>

				{option:modules.methods}
				<ul class="methods hidden">
					{iteration:modules.methods}
					<li class="method">
						<a href="#{$modules.methods.name}" rel="{$modules.name}.{$modules.methods.name}">[<span class="toggle">+</span>] {$modules.methods.name}</a>

						<div class="methodForm hidden">
							<form action="" method="get">
								{option:modules.methods.parameters}
								{iteration:modules.methods.parameters}
								<p>
									<label for="{$modules.methods.parameters.label}">{$modules.methods.parameters.name}</label>

									<input class="input-text" type="text" id="{$modules.methods.parameters.label}" name="{$modules.methods.parameters.name}" value="" />
									{option:modules.methods.parameters.description}{$modules.methods.parameters.description}{/option:modules.methods.parameters.description}
								</p>
								{/iteration:modules.methods.parameters}
								{/option:modules.methods.parameters}

								<input class="submit" type="submit" value="Call" name="{$modules.methods.name}Submit" id="{$modules.methods.name}Submit" />
							</form>
						</div>
					</li>
					{/iteration:modules.methods}
				</ul>
				{/option:modules.methods}
			</li>
			{/iteration:modules}
		</ul>

		<div id="output" class="hidden">
			<h3>Output:</h3>
			<pre>

			</pre>
		</div>
	</div>
</body>
</html>
