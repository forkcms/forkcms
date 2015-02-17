{form:search}
	<input type="hidden" value="search" id="formSearch" name="form" />
	<div class="form-group">
		<input value="" id="qWidget" name="q_widget" maxlength="255" type="text" class="inputText autoSuggest form-control" />
	</div>
	<input type="submit" class="btn btn-success" name="submit" value="{$lblSearch|ucfirst}">
{/form:search}