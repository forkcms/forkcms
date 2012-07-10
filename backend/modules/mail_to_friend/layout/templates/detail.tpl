{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblMailToFriend|ucfirst}</h2>
</div>

<div class="tabs">
	<ul>
		<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
	</ul>

	<div id="tabContent">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td id="leftColumn">

					{* From *}
					<div class="box">
						<div class="heading">
							<h3>{$lblFrom|ucfirst}</h3>
						</div>
						<div class="options">
							<p>
								<label for="ownName">{$lblName|ucfirst}:</label> <span class="ownName">{$item.own.name}</span>
							</p>
							<p>
								<label for="ownEmail">{$lblEmail|ucfirst}:</label> <span class="ownEmail">{$item.own.email}</span>
							</p>
						</div>
					</div>

					{* To *}
					<div class="box">
						<div class="heading">
							<h3>{$lblTo|ucfirst}</h3>
						</div>
						<div class="options">
							<p>
								<label for="friendName">{$lblName|ucfirst}:</label> <span class="friendName">{$item.friend.name}</span>
							</p>
							<p>
								<label for="friendEmail">{$lblEmail|ucfirst}:</label> <span class="friendEmail">{$item.friend.email}</span>
							</p>
						</div>
					</div>

					{* Extra info *}
					<div class="box">
						<div class="heading">
							<h3>{$lblInfo|ucfirst}</h3>
						</div>
						<div class="options">
							<p>
								<label for="sendOn">{$lblSendOn|ucfirst}:</label> <span class="sendOn">{$item.created_on|date:'long':'{$LANGUAGE}'}</span>
							</p>
							<p>
								<label for="page">{$lblPage|ucfirst}:</label> <span class="page"><a href="{$item.page}" title="{$lblPage|ucfirst}">{$item.page}</a></span>
							</p>
						</div>
					</div>

					{* The message *}
					{option:item.message}
					<div class="box">
						<div class="heading">
							<h3>{$lblMessage|ucfirst}</h3>
						</div>
						<div class="options">
							{$item.message}
						</div>
					</div>
					{/option:item.message}

				</td>
			</tr>
		</table>

	</div>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}