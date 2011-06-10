if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the faq categories
 *
 * @author	Lester Lievens <lester@netlash.com>
 */
jsBackend.faq =
{
	/**
	 * Kind of constructor
	 */
	init: function()
	{	
		// destroy default drag and drop
		$('.sequenceByDragAndDrop tbody').sortable('destroy');
		
		// drag and drop
		jsBackend.faq.bindDragAndDropCategoryFaq();
		jsBackend.faq.checkForEmptyCategories();
	},


	/**
	 * Check for empty categories and make it still possible to drop questions
	 */
	checkForEmptyCategories: function()
	{
		// when there are empty categories
		if($('tr.noQuestions').length > 0)
		{
			// make datagrid droppable
			$('table.datagrid').droppable(
			{
				// only accept table rows
				accept: 'table.datagrid tr',
				drop: function(event, ui)
				{	
					// remove the no questions in category message
					$(this).find('tr.noQuestions').remove();
				}
			});
		}
	},


	/**
	 * Bind drag and dropping of a category
	 */
	bindDragAndDropCategoryFaq: function()
	{	
		// go over every datagrid
		$.each($('div.datagridHolder'), function()
		{
			// make them sortable
			$('div.datagridHolder').sortable(
			{
				items: 'table.datagrid tbody tr',		// set the elements that user can sort
				handle: 'td.dragAndDropHandle',			// set the element that user can grab
				tolerance: 'pointer',					// give a more natural feeling
				connectWith: 'div.datagridHolder',		// this is what makes dragging between categories possible
				stop: function(event, ui)				// on stop sorting
				{
					// vars we will need
					var questionId = ui.item.attr('id');
					var fromCategoryId = $(this).attr('id').substring(9);
					var toCategoryId = ui.item.parents('.datagridHolder').attr('id').substring(9);
					var fromCategorySequence = $(this).sortable('toArray').join(',');
					var toCategorySequence = $('#datagrid-' + toCategoryId).sortable('toArray').join(',');

					// make ajax call
					$.ajax(
					{
						cache: false, type: 'POST', dataType: 'json', 
						url: '/backend/ajax.php?module=' + jsBackend.current.module + '&action=sequence_questions&language=' + jsBackend.current.language,
						data: 'questionId=' + questionId + '&fromCategoryId=' + fromCategoryId + '&toCategoryId=' + toCategoryId + '&fromCategorySequence=' + fromCategorySequence + '&toCategorySequence=' + toCategorySequence,
						success: function(data, textStatus)
						{ 
							// not a succes so revert the changes
							if(data.code == 200)
							{ 
								// if there are no records -> show message					
								if($('div#datagrid-' + fromCategoryId + ' table.datagrid tr').length == 1)
								{
									$('div#datagrid-' + fromCategoryId + ' table.datagrid').append('<tr class="noQuestions"><td colspan="3">{$msgNoQuestionInCategory}</td></tr>');
								}
								
								// redo odd-even
								var table = $('table.datagrid');
								table.find('tr').removeClass('odd').removeClass('even');
								table.find('tr:even').addClass('even');
								table.find('tr:odd').addClass('odd');
							}
							else
							{
								// revert
								$(this).sortable('cancel');
								
								// show message
								jsBackend.messages.add('error', 'alter sequence failed.');
							}
							
							// alert the user
							if(data.code != 200 && jsBackend.debug){ alert(data.message); }
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							// revert
							$(this).sortable('cancel');
							
							// show message
							jsBackend.messages.add('error', 'alter sequence failed.');

							// alert the user
							if(jsBackend.debug){ alert(textStatus); }
						}
					});
				}
			});
			
		});
	},


	eoo: true
}


$(document).ready(jsBackend.faq.init);