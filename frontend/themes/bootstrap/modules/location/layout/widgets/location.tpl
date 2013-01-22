{*
	variables that are available:
	- {$widgetLocationItem}: contains data about this location
	- {$widgetLocationSettings}: contains this module's settings
*}

{option:widgetLocationItem}
	<div class="locationLocationWidget well location">
		{* @remark: do not remove the parseMap-class, it is used by JS *}
		<div id="map{$widgetLocationItem.id}" class="parseMap well" style="height: {$widgetLocationSettings.height}px;">
			<span class="hideText">{$lblLoading|ucfirst}</span>
		</div>

		{option:widgetLocationSettings.full_url}
			<a href="{$widgetLocationSettings.maps_url}" class="btn btn-small pull-right" target="_blank">
				{$lblViewLargeMap|ucfirst}<span class="hideText"> {$lblFor} {$widgetLocationItem.title}</span>
			</a>
		{/option:widgetLocationSettings.full_url}

		{option:widgetLocationSettings.directions}
			<aside id="locationSearch{$widgetLocationItem.id}">
				<form method="get" action="#" class="form-horizontal">
					<div class="control-group">
						<label class="control-label" for="locationSearchAddress{$widgetLocationItem.id}">
							{$lblStart|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>&nbsp;
						</label>
						<div class="input-append">
							{* @remark: do not remove the id *}
							<input type="text" id="locationSearchAddress{$widgetLocationItem.id}" name="locationSearchAddress" class="inputText" />
							{* @remark: do not remove the id *}
							<span id="locationSearchError{$widgetLocationItem.id}" class="error" style="display: none;">
								<span class="help-inline">{$errFieldIsRequired|ucfirst}</span>
							</span>
							{* @remark: do not remove the id *}
							<input type="submit" id="locationSearchRequest{$widgetLocationItem.id}" name="locationSearchRequest" class="btn btn-primary" value="{$lblShowDirections|ucfirst}" />
						</div>
					</div>
				</form>
			</aside>
		{/option:widgetLocationSettings.directions}

		{* @remark: do not remove the id *}
		<div id="markerText{$widgetLocationItem.id}" class="hide" itemscope itemtype="http://schema.org/Place">
			<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
				<meta itemprop="latitude" content="{$widgetLocationItem.lat}" />
				<meta itemprop="longitude" content="{$widgetLocationItem.lng}" />
			</div>

			<div itemprop="name" class="hide"><strong>{$widgetLocationItem.title}</strong></div>
			<address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
				<span itemprop="streetAddress">{$widgetLocationItem.street} {$widgetLocationItem.number}</span><br>
				<span itemprop="postalCode">{$widgetLocationItem.zip}</span> <span itemprop="addressLocality">{$widgetLocationItem.city}</span><br>
				<span itemprop="addressCountry">{$widgetLocationItem.country}</span>
			</address>
		</div>
	</div>
{/option:widgetLocationItem}