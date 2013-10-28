/**
 * Interaction for the faq categories
 *
 * @author	Lester Lievens <lester.lievens@netlash.com>
 * @author	Thomas Deceuninck <thomas@fronto.be>
 * @author	Annelies Van Extergem <annelies@netlash.com>
 * @author	Davy Van Vooren <davy.vanvooren@netlash.com>
 */
jsBackend.faq =
{
	// init, something like a constructor
	init: function()
	{
		// index stuff
		if($('#dataGridQuestionsHolder').length > 0)
		{
			// destroy default drag and drop
			$('.sequenceByDragAndDrop tbody').sortable('destroy');

			// drag and drop
			jsBackend.faq.bindDragAndDropCategoryFaq();
			jsBackend.faq.checkForEmptyCategories();
		}

		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},

	/**
	 * Check for empty categories and make it still possible to drop questions
	 */
	checkForEmptyCategories: function()
	{
		// reset initial empty grids
		$('table.emptyGrid').each(function(){
			$(this).find('td').parent().remove();
			$(this).append('<tr class="noQuestions"><td colspan="' + $(this).find('th').length + '">' + jsBackend.locale.msg('NoQuestionInCategory') +'</td></tr>');
			$(this).removeClass('emptyGrid');
		});

		// when there are empty categories
		if($('tr.noQuestions').length > 0)
		{
			// make dataGrid droppable
			$('table.dataGrid').droppable(
			{
				// only accept table rows
				accept: 'table.dataGrid tr',
				drop: function(e, ui)
				{
					// remove the no questions in category message
					$(this).find('tr.noQuestions').remove();
				}
			});

			// cleanup remaining no questions
			$('table.dataGrid').each(function(){
				if($(this).find('tr').length > 2) $(this).find('tr.noQuestions').remove();
			});
		}
	},

	/**
	 * Bind drag and dropping of a category
	 */
	bindDragAndDropCategoryFaq: function()
	{
		// go over every dataGrid
		$.each($('div.dataGridHolder'), function()
		{
			// make them sortable
			$('div.dataGridHolder').sortable(
			{
				items: 'table.dataGrid tbody tr',		// set the elements that user can sort
				handle: 'td.dragAndDropHandle',			// set the element that user can grab
				tolerance: 'pointer',					// give a more natural feeling
				connectWith: 'div.dataGridHolder',		// this is what makes dragging between categories possible
				stop: function(e, ui)				// on stop sorting
				{
					// vars we will need
					var questionId = ui.item.attr('id');
					var fromCategoryId = $(this).attr('id').substring(9);
					var toCategoryId = ui.item.parents('.dataGridHolder').attr('id').substring(9);
					var fromCategorySequence = $(this).sortable('toArray').join(',');
					var toCategorySequence = $('#dataGrid-' + toCategoryId).sortable('toArray').join(',');

					// make ajax call
					$.ajax(
					{
						data:
						{
							fork: { action: 'sequence_questions' },
							questionId: questionId,
							fromCategoryId: fromCategoryId,
							toCategoryId: toCategoryId,
							fromCategorySequence: fromCategorySequence,
							toCategorySequence: toCategorySequence
						},
						success: function(data, textStatus)
						{
							// not a success so revert the changes
							if(data.code == 200)
							{
								// change count in title (if any)
								$('div#dataGrid-' + fromCategoryId + ' h3').html($('div#dataGrid-' + fromCategoryId + ' h3').html().replace(/\(([0-9]*)\)$/, '(' + ( $('div#dataGrid-' + fromCategoryId + ' table.dataGrid tr').length - 1 ) + ')'));

								// if there are no records -> show message
								if($('div#dataGrid-' + fromCategoryId + ' table.dataGrid tr').length == 1)
								{
									$('div#dataGrid-' + fromCategoryId + ' table.dataGrid').append('<tr class="noQuestions"><td colspan="3">' + jsBackend.locale.msg('NoQuestionInCategory') + '</td></tr>');
								}

								// check empty categories
								jsBackend.faq.checkForEmptyCategories();

								// redo odd-even
								var table = $('table.dataGrid');
								table.find('tr').removeClass('odd').removeClass('even');
								table.find('tr:even').addClass('even');
								table.find('tr:odd').addClass('odd');

								// change count in title (if any)
								$('div#dataGrid-' + toCategoryId + ' h3').html($('div#dataGrid-' + toCategoryId + ' h3').html().replace(/\(([0-9]*)\)$/, '(' + ( $('div#dataGrid-' + toCategoryId + ' table.dataGrid tr').length - 1 ) + ')'));
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
	}
}

$(jsBackend.faq.init);