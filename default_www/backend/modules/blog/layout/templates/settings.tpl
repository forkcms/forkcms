{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblSettings|ucfirst} &gt; {$lblBlog|ucfirst}</p>
			</div>

			<div class="inner">
				
				<!-- @todo @johan add label -->
				<h3>Module-instellingen: blog</h3>
				
				{form:settings}
					<div class="box">
						<div class="heading">
							<h3>{$lblSpamFilter|ucfirst}</h3>
						</div>

						<div class="options">
							<p>{$msgHelpSpamFilter}:</p>
							<ul class="inputList p0">
								<li>{$chkSpamfilter} <label for="spamfilter">{$lblCheckCommentsForSpam|ucfirst}</label></li>
							</ul>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblComments|ucfirst}</h3>
						</div>

						<div class="options">
							<ul class="inputList p0">
								<li>{$chkAllowComments} <label for="allowComments">{$lblAllowComments|ucfirst}</label></li>
							</ul>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblModeration|ucfirst}</h3>
						</div>

						<div class="options">
							<ul class="inputList p0">
								<li>{$chkModeration} <label for="moderation">{$lblAllowModeration|ucfirst}</label></li>
							</ul>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblSEO}</h3>
						</div>

						<div class="options">
							<p>{$msgHelpPingServices}:</p>
							<ul class="inputList p0">
								<li>{$chkPingServices}<label for="pingServices">{$lblPingBlogServices|ucfirst}</label></li>
							</ul>
						</div>
					</div>

					<div class="box">
						<div class="horizontal">
							<div class="heading">
								<h3>{$lblRSSFeed}</h3>
							</div>
							<div class="options">
								<label>{$lblTitle|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
								{$txtRssTitle} {$txtRssTitleError}
								<span class="helpTxt">{$msgHelpRSSTitle}</span>
							</div>
							<div class="options">
								<label>{$lblDescription|ucfirst}</label>
								{$txtRssDescription} {$txtRssDescriptionError}
								<span class="helpTxt">{$msgHelpRSSDescription}</span>
							</div>
							<div class="options">
								<label>{$lblFeedburnerURL}</label>
								{$txtFeedburnerUrl} {$txtFeedburnerUrlError}
								<span class="helpTxt">{$msgHelpFeedburnerURL}</span>
							</div>
						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
						</div>
					</div>
				{/form:settings}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}