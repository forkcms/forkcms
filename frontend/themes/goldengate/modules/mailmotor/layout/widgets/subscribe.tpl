<section class="mod sideMod">
	<div class="inner">
		<header>
			<h4>{$lblNewsletter|ucfirst}</h4>
		</header>
		<div class="bd">
			<p>{$msgNewsletterSubscribe}</p>
			<form action="{$var|geturlforblock:'mailmotor':'subscribe'}" method="post">
				<input type="hidden" name="form" value="subscribe" />
				<p>
					<input type="text" value="" id="email" name="email" class="inputText" />
				</p>
				<p>
					<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
				</p>
			</form>
		</div>
	</div>
</section>