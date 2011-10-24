<div id="doormat" class="row">
	<section id="about" class="col-4">
		{* Doormat left position *}
		{iteration:positionDoormatLeft}
			{option:positionDoormatLeft.blockIsHTML}
				{$positionDoormatLeft.blockContent}
			{/option:positionDoormatLeft.blockIsHTML}
			{option:!positionDoormatLeft.blockIsHTML}
				{$positionDoormatLeft.blockContent}
			{/option:!positionDoormatLeft.blockIsHTML}
		{/iteration:positionDoormatLeft}
	</section>

	<section id="about" class="col-4">
		{* Doormat middle position *}
		{iteration:positionDoormatMiddle}
			{option:positionDoormatMiddle.blockIsHTML}
				{$positionDoormatMiddle.blockContent}
			{/option:positionDoormatMiddle.blockIsHTML}
			{option:!positionDoormatMiddle.blockIsHTML}
				{$positionDoormatMiddle.blockContent}
			{/option:!positionDoormatMiddle.blockIsHTML}
		{/iteration:positionDoormatMiddle}
	</section>

	<section id="recentComments" class="col-4">
		{* Doormat right position *}
		{iteration:positionDoormatRight}
			{option:positionDoormatRight.blockIsHTML}
				{$positionDoormatRight.blockContent}
			{/option:positionDoormatRight.blockIsHTML}
			{option:!positionDoormatRight.blockIsHTML}
				{$positionDoormatRight.blockContent}
			{/option:!positionDoormatRight.blockIsHTML}
		{/iteration:positionDoormatRight}
	</section>
</div>