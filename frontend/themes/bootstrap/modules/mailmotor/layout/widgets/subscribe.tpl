<div id="mailmotorSubscribeWidget" class="well">
	<form action="{$var|geturlforblock:'mailmotor':'subscribe'}" method="post" class="form-inline">
	  <input type="hidden" name="form" value="subscribe" />
	  <label class="control-label" for="email">
			{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>
		</label>
		<div class="input-group">
			<input type="email" value="" id="email" name="email" class="form-control" />
			<span class="input-group-btn">
			  <input id="send" class="btn btn-default" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
			</span>
		</div>
	</form>
</div>