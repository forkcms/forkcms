<section id="mailMotorSubscribeFormWidget" class="mod">
	<div class="inner">
		<div class="hd">
			<h3 class="title">{$lblSubscribeToNewsletter|ucfirst}</h3>
		</div>
		<div class="bd">
			<form action="{$var|geturlforblock:'MailMotor':'Subscribe'}" method="post">
				<input type="hidden" name="form" value="mailMotorSubscribeForm" />
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<input type="text" value="" id="email" name="email" class="inputText" placeholder="{$lblYourEmail|ucfirst}" />
				</p>
				<p>
					<input id="send" class="inputSubmit btn" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
				</p>
			</form>
		</div>
	</div>
</section>