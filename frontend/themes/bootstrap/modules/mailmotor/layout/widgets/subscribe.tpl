<div id="mailmotorSubscribeWidget" class="well">
	<form action="{$var|geturlforblock:'mailmotor':'subscribe'}" method="post">
		<div class="control-group">
			<label class="control-label" for="email">
				{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>
			</label>
			<div class="controls">
				<input type="hidden" name="form" value="subscribe" />
				<input type="email" value="" id="email" name="email" class="inputText" />
				<input id="send" class="btn" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
			</div>
		</div>
	</form>
</div>