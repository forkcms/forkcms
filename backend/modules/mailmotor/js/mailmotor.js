/**
 * Interaction for the mailmotor
 *
 * @author	Dave Lens <dave@netlash.com>
 */
jsBackend.mailmotor =
{
	init: function()
	{
		jsBackend.mailmotor.charts.init();
		jsBackend.mailmotor.chartPieChart.init();
		jsBackend.mailmotor.changeGroup.init();
		jsBackend.mailmotor.linkAccount.init();
		jsBackend.mailmotor.resizing.init();
		jsBackend.mailmotor.step3.init();
		jsBackend.mailmotor.step4.init();
		jsBackend.mailmotor.templateSelection.init();

		// multiple text box for adding multiple emailaddresses
		if($('form#add #email').length > 0)
		{
			$('form#add #email').multipleTextbox(
			{
				emptyMessage: '',
				addLabel: '{$lblAdd|ucfirst}',
				removeLabel: '{$lblDelete|ucfirst}',
				canAddNew: true
			});
		}
	}
}

jsBackend.mailmotor.charts =
{
	init: function()
	{
		if($('#chartPieChart').length > 0 || $('#chartDoubleMetricPerDay').length > 0 || $('#chartSingleMetricPerDay').length > 0 || $('#chartWidget').length > 0)
		{
			Highcharts.setOptions(
			{
				colors: ['#058DC7', '#50b432', '#ED561B', '#EDEF00', '#24CBE5', '#64E572', '#FF9655'],
				title: { text: '' },
				legend:
				{
					layout: 'vertical',
					backgroundColor: '#FFF',
					borderWidth: 0,
					shadow: false,
					symbolPadding: 12,
					symbolWidth: 10,
					itemStyle: { cursor: 'pointer', color: '#000', lineHeight: '18px' },
					itemHoverStyle: { color: '#666' },
					style: { right: '0', top: '0', bottom: 'auto', left: 'auto' }
				}
			});
		}
	}
}

jsBackend.mailmotor.chartPieChart =
{
	init: function()
	{
		if($('#chartPieChart').length > 0) jsBackend.mailmotor.chartPieChart.create();
	},

	// add new chart
	create: function()
	{
		var pieChartValues = $('#dataChartPieChart ul.data li');
		var pieChartData = [];

		pieChartValues.each(function()
		{
			pieChartData.push(
			{
				'name': $(this).children('span.label').html(),
				'y': parseInt($(this).children('span.value').html()),
				'percentage': parseInt($(this).children('span.percentage').html())
			});
		});

		var chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartPieChart', height: 200, margin: [0, 160, 0, 0]	},
			credits: { enabled: false },
			plotArea: { shadow: null, borderWidth: null, backgroundColor: null },
			tooltip:
			{
				formatter: function()
				{
					var percentage = String(this.point.percentage);
					return '<b>'+ this.point.name +'</b>: '+ this.y + ' (' + percentage.substring(0, percentage.indexOf('.') + 4) + '%)';
				},
				borderWidth: 2,
				shadow: false
			},
			plotOptions:
			{
				pie:
				{
					allowPointSelect: true,
					dataLabels:
					{
						enabled: false
					}
				}
			},
			legend: { style: { right: '10px' } },
			series: [ { type: 'pie', data: pieChartData } ]
		});
	}
}

jsBackend.mailmotor.changeGroup =
{
	init: function()
	{
		// cache objects
		$dropdown = $('#subscriptions');

		// dropdown is changed
		$dropdown.on('change', function()
		{
			// redirect with the new group
			window.location = document.location.pathname +'?token=true&email='+ variables['email'] +'&group_id='+ $(this).val();
		});
	}
}

jsBackend.mailmotor.linkAccount =
{
	init: function()
	{
		// cache objects
		$confirm = $('#linkAccount');
		$url = $('#url');
		$username = $('#username');
		$password = $('#password');

		// prevent submit on keyup
		$('#accountBox input').on('keypress', function(e)
		{
			if(e.keyCode == 13)
			{
				// prevent the default action
				e.preventDefault();

				// if all fields are set
				if($url.val() != '' && $username.val() != '' && $password.val() != '')
				{
					// do the call to link the account
					jsBackend.mailmotor.linkAccount.doCall();
				}
			}
		});

		// link account button clicked
		$(document).on('click', '#linkAccount', function(e)
		{
			// prevent default
			e.preventDefault();

			// do the call to link the account
			jsBackend.mailmotor.linkAccount.doCall();
		});

		// create client is checked
		$('#clientId').on('change', function(e)
		{
			var clientId = $(this).val();
			$companyName = $('#companyName');
			$contactName = $('#contactName');
			$contactEmail = $('#contactEmail');

			// '0' is the 'create new client' option, so we have to reset the input
			if(clientId == '0')
			{
				$companyName.val('');
				$contactName.val('');
				$contactEmail.val('');
			}

			// an existing client was chosen, so we have to update the info fields with the current details of the client
			else
			{
				$.ajax(
				{
					data:
					{
						fork: { action: 'load_client_info' },
						client_id: clientId
					},
					success: function(data, textStatus)
					{
						$.each($('#countries').find('option'), function(index, item)
						{
							if($(this).text() == data.data.country)
							{
								$(this).prop('selected', true);
							}
						});

						$.each($('#timezones').find('option'), function(index, item)
						{
							if($(this).text() == data.data.timezone)
							{
								$(this).prop('selected', true);
							}
						});

						$companyName.val(data.data.company);
						$contactName.val(data.data.contact_name);
						$contactEmail.val(data.data.email);
					}
				});
			}
		});
	},

	doCall: function()
	{
		$url = $('#url');
		$username = $('#username');
		$password = $('#password');

		// make the call
		$.ajax(
		{
			data:
			{
				fork: { action: 'link_account' },
				url: $url.val(),
				username: $username.val(),
				password: $password.val()
			},
			success: function(data, textStatus)
			{
				// remove all previous errors
				$('.formError').remove();

				// success!
				if(data.code == 200)
				{
					// client_id field is set
					window.location = window.location.pathname + '?token=true&report=' + data.data.message + '#tabSettingsClient';
				}
				else
				{
					// field was set
					if(data.data.field)
					{
						// add error to the field respective field
						$('#'+ data.data.field).after('<span class="formError">'+ data.message +'</span>');
					}
				}
			}
		});
	}
}

jsBackend.mailmotor.resizing =
{
	init: function()
	{
		$iframe = $('#contentBox');
		$iframeBox = $('#iframeBox');

		// make the plain content textarea resizable
		$('#contentPlain').resizable({ handles: 's' });

		// make the iframe resizable
		$iframeBox.resizable(
		{
			handles: 's',

			/*
				This is a hack to fix sloppy default resizing in jqueryui. The default behaviour stops resizing as soon as your mouse
				enters the content viewport of an iframe, meaning quick resizing is not possible. What we do here is adding an overlay
				div to the iframe to "block" the mouse from ever entering the iframe contents while resizing.
			*/
			start: function()
			{
				// create an overlay
				var overlay = $('<div></div>');

				// append the overlay to the iframe box and give it an ID
				$iframeBox.append(overlay);
				overlay[0].id = 'iframeOverlay';

				// the overlay should be absolutely positioned with the top value aligned to the top of the iframe
				overlay.css(
				{
					left: 0,
					position:'absolute',
					top: $iframe.position().top
				});

				// height should be the height of the iframe
				overlay.height($iframe.height());
				overlay.width('100%');
			},
			stop: function()
			{
				// remove the overlay
				$('#iframeOverlay').remove();
			}
		});
	}
}

jsBackend.mailmotor.step3 =
{
	init: function()
	{
		// cache objects
		var $iframe = $('#contentBox');
		var $iframeBox = $('#iframeBox');
		var $form = $('#step3');

		// only continue if the iframe is ready
		$iframe.load(function()
		{
			// cache objects from inside the iframe
			var body = $iframe.contents().find('body');

			// give the iframebox the height of the body contents
			$iframeBox.height(body.height());

			$form.on('submit', function(e)
			{
				// prevent the form from submitting
				e.preventDefault();

				// set variables
				var subject = $('#subject').val();
				var plainText = ($('#contentPlain').length > 0) ? $('#contentPlain').val() : '';
				var textareaValue = $iframe[0].contentWindow.getEditorContent();

				// make the call
				$.ajax(
				{
					data:
					{
						fork: { action: 'save_content' },
						mailing_id: variables.mailingId,
						subject: subject,
						content_plain: plainText,
						content_html: textareaValue
					},
					success: function(data, textStatus)
					{
						if(data.code == 200)
						{
							// direct the user to step 4
							window.location = document.location.pathname +'?token=true&id='+ variables.mailingId +'&step=4';
						}
						else
						{
							jsBackend.messages.add('error', data.message);
						}
					}
				});
			});
		});
	}
}

jsBackend.mailmotor.step4 =
{
	init: function()
	{
		// cache objects
		$form = $('#step4');
		$confirmBox = $('#sendMailingConfirmationModal');
		$sendMailing = $('#sendMailing');
		oSendDate = $('#sendOnDate');
		oSendTime = $('#sendOnTime');

		// store data
		var sendDate = oSendDate.val();
		var sendTime = oSendTime.val();

		// initalize the confirmation modal
		$confirmBox.dialog(
		{
			autoOpen: false,
			draggable: false,
			width: 500,
			modal: true,
			resizable: false,
			buttons:
			{
				'{$lblSendMailing|ucfirst}': function()
				{
					// send the mailing
					jsBackend.mailmotor.step4.sendMail();
				},
				'{$lblCancel|ucfirst}': function()
				{
					// close the dialog
					$(this).dialog('close');
				}
			}
		});

		// value of date/time has changed
		$(oSendDate.selector +', '+ oSendTime.selector).on('change', function(e)
		{
			// check if the send date/time is empty. if they are, reset the dates to the old values
			if(oSendDate.val() == '') oSendDate.val(sendDate);
			if(oSendTime.val() == '') oSendTime.val(sendTime);

			// save the send date
			jsBackend.mailmotor.step4.saveSendDate();
		});

		// enter was pressed
		$(oSendDate.selector +', '+ oSendTime.selector).on('keypress', function(e)
		{
			if(e.keyCode == 13)
			{
				// check if the send time is empty. if they are, reset the time to the old value
				if(oSendDate.val() == '') oSendDate.val(sendDate);
				if(oSendTime.val() == '') oSendTime.val(sendTime);

				// save the send date
				jsBackend.mailmotor.step4.saveSendDate();

				// lose focus
				$(this).blur();

				// cancel form submit
				e.preventDefault();
			}
		});

		// sendMailing is clicked
		$sendMailing.on('click', function(e)
		{
			// prevent the form from submitting
			e.preventDefault();

			// open the dialog
			$confirmBox.dialog('open');
		});
	},

	saveSendDate: function()
	{
		// cache date/time values
		var sendOnDate = $('#sendOnDate').val();
		var sendOnTime = $('#sendOnTime').val();

		// make the call
		$.ajax(
		{
			data:
			{
				fork: { action: 'save_send_date' },
				mailing_id: variables.mailingId,
				send_on_date: sendOnDate,
				send_on_time: sendOnTime
			},
			success: function(data, textStatus)
			{
				if(data.code != 200)
				{
					// unload spinner
					buttonPane.removeClass('loading');

					// destroy the dialog
					$confirmBox.dialog('close');

					// show message
					jsBackend.messages.add('error', data.message);
				}
				else
				{
					// cache sendDate text block and create some date objects
					var modalSendInfo = $('#sendOn');
					var now = new Date();
					var sendDate = new Date(data.data.timestamp * 1000);

					// if the send date is in the past (or now), we hide the modalSendInfo text
					if(sendDate <= now) modalSendInfo.hide();

					// send date did not take place yet
					else
					{
						// show the additional sending information
						modalSendInfo.show();

						// replace the modal values
						modalSendInfo.text('{$msgSendOn}'.replace('%1$s', sendOnDate).replace('%2$s', sendOnTime));
					}
				}
			}
		});
	},

	sendMail: function()
	{
		// save the send date
		jsBackend.mailmotor.step4.saveSendDate();

		// fetch dom nodes
		var confirmBox = $('#sendMailingConfirmationModal');
		var buttonPane = $('.ui-dialog-buttonpane');

		// load spinner
		buttonPane.addClass('loading');

		// make the call
		$.ajax(
		{
			data:
			{
				fork: { action: 'send_mailing' },
				id: variables.mailingId
			},
			success: function(data, textStatus)
			{
				if(data.code == 200)
				{
					// redirect to index with a proper message
					window.location = '/private/'+ jsBackend.current.language +'/'+ jsBackend.current.module +'/index?report=mailing-sent';
				}
				else
				{
					// unload spinner
					buttonPane.removeClass('loading');

					// destroy the dialog
					$confirmBox.dialog('close');

					// show message
					jsBackend.messages.add('error', data.message);
				}
			}
		});
	}
}

jsBackend.mailmotor.templateSelection =
{
	init: function()
	{
		// store the list items
		$listItems = $('#templateSelection li');

		// one of the templates (ie. hidden radiobuttons) in the templateSelection <ul> are clicked
		$listItems.on('click', function(e)
		{
			// prevent default
			e.preventDefault();

			// store the object
			var radiobutton = $(this).find('input:radio:first');

			// set checked
			radiobutton.prop('checked', true);

			// if the radiobutton is checked
			if(radiobutton.is(':checked'))
			{
				// remove the selected state from all other templates
				$listItems.removeClass('selected');

				// add a selected state to the parent
				radiobutton.parent('li').addClass('selected');
			}
		});
	}
}

$(jsBackend.mailmotor.init);
