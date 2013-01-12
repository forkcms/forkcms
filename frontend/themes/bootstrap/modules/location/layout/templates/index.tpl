{*
	variables that are available:
	- {$locationItems}: contains data about all locations
	- {$locationSettings}: contains this module's settings
*}

{option:locationItems}
	<div id="locationIndex" class="row-fluid">
		{* @remark: do not remove the parseMap-class, it is used by JS *}
		<div id="map" class="parseMap span12 well" style="height: {$locationSettings.height}px;">

		</div>

		{iteration:locationItems}
			{* @remark: do not remove the id *}
			<div id="markerText{$locationItems.id}" class="hide" itemscope itemtype="http://schema.org/Place">
				<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
					<meta itemprop="latitude" content="{$locationItems.lat}" />
					<meta itemprop="longitude" content="{$locationItems.lng}" />
				</div>

				<div itemprop="name" class="hide"><strong>{$locationItems.title}</strong></div>
				<address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
					<span itemprop="streetAddress">{$locationItems.street} {$locationItems.number}</span><br>
					<span itemprop="postalCode">{$locationItems.zip}</span> <span itemprop="addressLocality">{$locationItems.city}</span><br>
					<span itemprop="addressCountry">{$locationItems.country}</span>
				</address>
			</div>
		{/iteration:locationItems}
	</div>
{/option:locationItems}
