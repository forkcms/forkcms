<section id="subscribeFormWidget" class="mod">
	<header class="hd">
		<h3>{$lblSubscribe|ucfirst}</h3>
	</header>
	<div class="inner">
		<div class="bd">
			<form action="{$var|geturlforblock:'mailmotor':'subscribe'}" method="post">
				<input type="hidden" name="form" value="subscribe" />
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<input type="text" value="" id="email" name="email" class="inputText" />
				</p>
				<p>
					<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
				</p>
			</form>
		</div>
	</div>
</section>