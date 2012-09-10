{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblFaq}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblPagination|ucfirst}</h3>
		</div>
		<div class="options">
			<label for="overviewNumberOfItemsPerCategory">{$lblItemsPerCategory|ucfirst}</label>
			{$ddmOverviewNumberOfItemsPerCategory} {$ddmOverviewNumberOfItemsPerCategoryError}
		</div>
		<div class="options">
			<label for="mostReadNumberOfItems">{$msgNumMostReadItems}</label>
			{$ddmMostReadNumberOfItems} {$ddmMostReadNumberOfItemsError}
		</div>
		<div class="options">
			<label for="relatedNumberOfItems">{$msgNumRelatedItems}</label>
			{$ddmRelatedNumberOfItems} {$ddmRelatedNumberOfItemsError}
		</div>
	</div>
    
    <div class="box">
		<div class="heading">
			<h3>{$lblCategories|ucfirst}</h3>
		</div>
		<div class="options">
			<label for="allowMultipleCategories">{$chkAllowMultipleCategories} {$lblAllowMultipleCategories|ucfirst}</label>
		</div>
    </div>

	<div class="box">
		<div class="heading">
			<h3>{$lblFeedback|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList">
				<li><label for="allowFeedback">{$chkAllowFeedback} {$lblAllowFeedback|ucfirst}</label></li>
				<li><label for="allowOwnQuestion">{$chkAllowOwnQuestion} {$lblAllowOwnQuestion|ucfirst}</label></li>
				<li><label for="sendEmailOnNewFeedback">{$chkSendEmailOnNewFeedback} {$lblSendEmailOnNewFeedback|ucfirst}</label>
				<li>
					<label for="spamfilter">{$chkSpamfilter} {$lblFilterCommentsForSpam|ucfirst}</label>
					<span class="helpTxt">
						{$msgHelpSpamFilter}
						{option:noAkismetKey}<span class="infoMessage"><br />{$msgNoAkismetKey|sprintf:{$var|geturl:'index':'settings'}}</span>{/option:noAkismetKey}
					</span>
				</li>
			</ul>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}