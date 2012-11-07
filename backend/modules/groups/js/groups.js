/**
 * Interaction for the groups module
 *
 * @author	Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 * @author	Thomas Deceuninck <thomas@fronto.be>
 */
jsBackend.groups =
{
	// init, constructor-alike
	init: function()
	{
		// variables
		$hide = $('.hide');
		$container = $('.container');
		$containerLabel = $('.container span label');
		$moduleDataGridBody = $('.module .datagridHolder .dataGrid tbody');
		$groupHolderDataGridBody = $('.groupHolder .dataGrid tbody');
		$dataGridTd = $('.dataGrid tbody tr td');
		$selectAll = $('.selectAll');

		$hide.each(jsBackend.groups.hide);
		$container.click(jsBackend.groups.clickHandler);
		$containerLabel.each(jsBackend.groups.mouseHandler);
		$moduleDataGridBody.each(jsBackend.groups.selectionPermissions);
		$groupHolderDataGridBody.each(jsBackend.groups.selectionWidgets)
		$dataGridTd.click(jsBackend.groups.selectHandler);
		$selectAll.click(jsBackend.groups.selectAll);
	},

	// hide an item
	hide: function()
	{
		// variables
		$this = $(this);

		// hide them
		$this.hide();
	},

	// clickhandler
	clickHandler: function(e)
	{
		// prevent default
		e.preventDefault();

		// init vars
		$this = $(this);

		// the action is currently closed, open it
		if($this.hasClass('iconCollapsed'))
		{
			// slidedown
			$this.next('.datagridHolder').show();

			// change title
			$this.attr('title', 'close');

			// change css
			$this.addClass('iconExpanded');
			$this.removeClass('iconCollapsed');
		}

		// the action is currently open, close it
		else
		{
			// close this thing
			$this.next('.datagridHolder').hide();

			// change title
			$this.attr('title', 'open');

			// change css
			$this.addClass('iconCollapsed');
			$this.removeClass('iconExpanded');
		}
	},

	// selectHandler
	selectHandler: function()
	{
		// init vars
		$this = $(this);

		// editing permissions? check permissions
		if($this.parent('tr').parent('tbody').parent('.dataGrid').parent('.datagridHolder').parent('.module').html() !== null) $this.parent('tr').parent('tbody').each(jsBackend.groups.selectionPermissions);

		// editing widgets? check widgets
		else $this.parent('tr').parent('tbody').each(jsBackend.groups.selectionWidgets);
	},

	// selection
	selectionPermissions: function()
	{
		// init vars
		var allChecked = true;
		var noneChecked = true;
		$this = $(this);

		// loop all actions and check if they're checked
		$this.find('tr td input').each(function()
		{
			// if not checked set false
			if(!$(this).prop('checked')) allChecked = false;

			// is checked?
			else noneChecked = false;
		});

		// some are checked? indeterminate!
		if(!allChecked && !noneChecked)
		{
			// unset checked and set indeterminate
			$this.parent('table').parent('div').parent('li').find('input').get(0).checked = false;
			$this.parent('table').parent('div').parent('li').find('input').get(0).indeterminate = true;
		}

		// if all actions are checked, check massaction checkbox
		if(allChecked)
		{
			// unset indeterminate and set checked
			$this.parent('table').parent('div').parent('li').find('input').get(0).indeterminate = false;
			$this.parent('table').parent('div').parent('li').find('input').get(0).checked = true;
		}

		// nothing is checked?
		if(noneChecked)
		{
			// unset indeterminate and checked
			$this.parent('table').parent('div').parent('li').find('input').get(0).indeterminate = false;
			$this.parent('table').parent('div').parent('li').find('input').get(0).checked = false;
		}
	},

	// selection widgets
	selectionWidgets : function()
	{
		// init vars
		var allChecked = true;
		$this = $(this);

		// loop all actions and check if they're checked
		$this.find('tr td input').each(function()
		{
			// if not checked set false
			if(!$(this).attr('checked')) allChecked = false;
		});

		// set checked if all is checked
		if(allChecked) $this.parent('table').find('thead tr th span span input').attr('checked', 'checked');

		// uncheck if not all items are checked
		else $this.parent('table').find('thead tr th span span input').removeAttr('checked');
	},

	// mousehandler
	mouseHandler: function()
	{
		// variables
		$this = $(this);

		// assign mouseovers
		$this.mouseover(function()
		{
			// change cursors
			$this.css('cursor', 'pointer');
			$this.css('cursor', 'hand');
		});
	},

	// select all
	selectAll : function()
	{
		// init vars
		$this = $(this);

		// check all?
		if($this.prop('checked'))
		{
			// loop through rows
			$this.next('a').next('div').find('table tbody tr td input').each(function()
			{
				// check boxes
				$(this).attr('checked', 'checked').parents('tr').addClass('selected');
			});
		}

		// uncheck all?
		else
		{
			// loop through rows
			$this.next('a').next('div').find('table tbody tr td input').each(function()
			{
				// uncheck boxes
				$(this).removeAttr('checked').parents('tr').removeClass('selected');
			});
		}
	}
}

$(jsBackend.groups.init);