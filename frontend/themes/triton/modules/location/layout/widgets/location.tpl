{*
	variables that are available:
	- {$widgetLocationItem}: contains data about this location
	- {$widgetLocationSettings}: contains this module's settings
*}

{option:widgetLocationItem}
	{* @remark: do not remove the parseMap-class, it is used by JS *}
	<div id="map{$widgetLocationItem.id}" class="parseMap" style="height: {$widgetLocationSettings.height}px; width: {$widgetLocationSettings.width}px;"></div>

	{option:widgetLocationSettings.directions}
		<aside id="locationSearch{$widgetLocationItem.id}" class="locationSearch">
			<form method="get" action="#">
				<p>
					<label for="locationSearchAddress{$widgetLocationItem.id}">{$lblStart|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<input type="text" id="locationSearchAddress{$widgetLocationItem.id}" name="locationSearchAddress" class="inputText" />
					<span id="locationSearchError{$widgetLocationItem.id}" class="formError inlineError" style="display: none;">{$errFieldIsRequired|ucfirst}</span>
				</p>
				<p>
					<input type="submit" id="locationSearchRequest{$widgetLocationItem.id}" name="locationSearchRequest" class="inputSubmit" value="{$lblShowDirections|ucfirst}" />
				</p>
			</form>
		</aside>
	{/option:widgetLocationSettings.directions}

	{option:widgetLocationSettings.full_url}
		<p><a href="{$widgetLocationSettings.maps_url}" title="{$lblViewLargeMap}">{$lblViewLargeMap|ucfirst}</a></p>
	{/option:widgetLocationSettings.full_url}

	<div id="markerText{$widgetLocationItem.id}" style="display: none;">
		<address>
			{$widgetLocationItem.street} {$widgetLocationItem.number}<br />
			{$widgetLocationItem.zip} {$widgetLocationItem.city}
		</address>
	</div>
{/option:widgetLocationItem}