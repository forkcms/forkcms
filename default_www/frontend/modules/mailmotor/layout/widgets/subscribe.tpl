<div id="subscribeForm">
	<form action="{$var|geturlforblock:'mailmotor':'subscribe'}" method="post">
	<input type="hidden" name="form" value="subscribe" />
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<input type="text" value="" id="email" name="email" class="inputText" />
				</p>
			</div>
			<div class="options">
				<p>
					<input id="send" class="inputButton button mainButton" type="submit" name="send" value="{$lblSend|ucfirst}" />
				</p>
			</div>
		</div>
	</form>
</div>