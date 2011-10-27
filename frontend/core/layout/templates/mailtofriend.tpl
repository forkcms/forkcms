<div class="mailToFriend">
	<a href="#" title="{$lblMailToFriend|ucfirst}" class="mailToFriend">{$lblMailToFriend|ucfirst}</a>

	<div class="mailToFriendForm"{option:!mailToFriendSubmitted} style="display:none;"{/option:!mailToFriendSubmitted} title="{$lblMailToFriend|ucfirst}">
	{option:mailToFriendSend}
		{$msgMailToFriendSend}
	{/option:mailToFriendSend}

	{option:!mailToFriendSend}
		{form:mailtofriend}
			<p>
				<label for="friendName">{$lblFriendName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>:</label>
				{$txtFriendName} {$txtFriendNameError}
			</p>
			<p>
				<label for="friendEmail">{$lblFriendEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>:</label>
				{$txtFriendEmail} {$txtFriendEmailError}
			</p>
			<p>
				<label for="ownName">{$lblOwnName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>:</label>
				{$txtOwnName} {$txtOwnNameError}
			</p>
			<p>
				<label for="ownEmail">{$lblOwnEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>:</label>
				{$txtOwnEmail} {$txtOwnEmailError}
			</p>
			<p>
				<label for="mailMessage">{$lblMessage|ucfirst}:</label>
				{$txtMailMessage} {$txtMailMessageError}
			</p>
			<p>
				<input class="inputSubmit" type="submit" name="send" value="{$lblSend|ucfirst}" />
			</p>
		{/form:mailtofriend}
	{/option:!mailToFriendSend}
	</div>
</div>