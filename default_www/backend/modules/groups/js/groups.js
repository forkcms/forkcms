if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the groups module
 *
 * @author	Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 */
jsBackend.groups = 
{
		// init, constructor-alike
		init: function()
		{	
			$('.hide').each(jsBackend.groups.hide);
			$('.container').click(jsBackend.groups.clickHandler);
			$('.container span label').each(jsBackend.groups.mouseHandler);
			$('.module .dataGridHolder .dataGrid tbody').each(jsBackend.groups.selectionPermissions);
			$('.groupHolder .dataGrid tbody').each(jsBackend.groups.selectionWidgets)
			$('.dataGrid tbody tr td').click(jsBackend.groups.selectHandler);
			$('.selectAll').click(jsBackend.groups.selectAll);
		},

		// hide an item
		hide: function()
		{
			// hide them
			$(this).hide();
		},

		// clickhandler
		clickHandler: function(event) 
		{
			// prevent default
			event.preventDefault();

			// init vars
			var $this = $(this);

			// the action is currently closed, open it
			if($this.hasClass('iconCollapsed'))
			{
				// slidedown
				$this.next('.dataGridHolder').show();

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
				$this.next('.dataGridHolder').hide();

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
			var $this = $(this);

			// no checkbox involved?
			if(!$this.hasClass('checkbox'))
			{
				// already checked?
				if($this.parent().children('td').children('input').attr('checked'))
				{
					// remove checkstates
					$this.parent('tr').children('td').children('input').removeAttr('checked');
					$this.parent('tr').removeClass('selected');
				}

				// not yet checked?
				else 
				{
					// add checkstates
					$this.parent('tr').children('td').children('input').attr('checked', 'checked');
					$this.parent('tr').addClass('selected');
				}
			}

			// editing permissions? check permissions
			if($this.parent('tr').parent('tbody').parent('.dataGrid').parent('.dataGridHolder').parent('.module').html() !== null) $this.parent('tr').parent('tbody').each(jsBackend.groups.selectionPermissions);	

			// editing widgets? check widgets
			else $this.parent('tr').parent('tbody').each(jsBackend.groups.selectionWidgets);
		},

		// selection
		selectionPermissions: function()
		{
			// init vars
			var allChecked = true;
			var noneChecked = true;
			var $this = $(this);

			// loop all actions and check if they're checked
			$this.find('tr td input').each(function() 
			{
				// if not checked set false
				if(!$(this).attr('checked')) allChecked = false;

				// is checked?
				else noneChecked = false;
			});

			// some are checked? indeterminate!
			if(!allChecked && !noneChecked)
			{
				// unset checked and set indeterminate
				$this.parent('table').parent('ul').parent('li').find('input').get(0).checked = false;
				$this.parent('table').parent('ul').parent('li').find('input').get(0).indeterminate = true;
			}

			// if all actions are checked, check massaction checkbox
			if(allChecked)
			{
				// unset indeterminate and set checked
				$this.parent('table').parent('ul').parent('li').find('input').get(0).indeterminate = false;
				$this.parent('table').parent('ul').parent('li').find('input').get(0).checked = true;
			}

			// nothing is checked?
			if(noneChecked)
			{
				// unset indeterminate and checked
				$this.parent('table').parent('ul').parent('li').find('input').get(0).indeterminate = false;
				$this.parent('table').parent('ul').parent('li').find('input').get(0).checked = false;
			}
		},

		// selection widgets
		selectionWidgets : function()
		{
			// init vars
			var allChecked = true;
			var $this = $(this);

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
			// assign mouseovers
			$(this).mouseover(function()
			{
				// change cursors
				$(this).css('cursor', 'pointer');
				$(this).css('cursor', 'hand');
			});
		},

		// select all
		selectAll : function()
		{
			// init vars
			var $this = $(this);

			// check all?
			if($this.attr('checked'))
			{
				// loop through rows
				$this.next('a').next('ul').find('table tbody tr td input').each(function()
				{
					// check boxes
					$(this).attr('checked', 'checked');
					$(this).parent('td').parent('tr').addClass('selected');
				});
			}

			// uncheck all?
			else
			{
				// loop through rows
				$this.next('a').next('ul').find('table tbody tr td input').each(function()
				{
					// uncheck boxes
					$(this).removeAttr('checked');
					$(this).parent('td').parent('tr').removeClass('selected');
				});
			}
		},

		// end
		eoo: true
}

// ready or not?
$(document).ready(jsBackend.groups.init);