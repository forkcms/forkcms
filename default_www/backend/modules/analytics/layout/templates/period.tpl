<div class="heading">
	<h3>{$lblStatistics|ucfirst} {$lblFrom} {$startTimestamp|formatdate} {$lblTill} {$endTimestamp|formatdate}</h3>
</div>

<div class="footer oneLiner">
	{form:periodPickerForm}
		<p>
			<label for="startDate">{$lblStartDate|ucfirst}</label>
			{$txtStartDate}
		</p>
		<p>
			<label for="endDate">{$lblEndDate|ucfirst}</label>
			{$txtEndDate}
		</p>
		<p>
			<input id="update" type="submit" name="update" value="{$lblChangePeriod|ucfirst}" />
		</p>
		{$txtStartDateError}
		{$txtEndDateError}
	{/form:periodPickerForm}

	{option:liveDataURL}
		<div class="buttonHolderRight">
			<a href="{$liveDataURL}" title="{$lblGetLiveData|ucfirst}" class="submitButton button inputButton mainButton icon iconRefresh"><span>{$lblGetLiveData|ucfirst}</span></a>
		</div>
	{/option:liveDataURL}
</div>