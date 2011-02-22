if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.crm =
{
	/**
	 * Kind of a constructor
	 */
	init: function()
	{
		jsBackend.crm.companies.init();
	},


	// end
	eoo: true
}


jsBackend.crm.companies =
{
	/**
	 * Kind of a constructor
	 */
	init: function()
	{
		// telephone box
		if($('input.telephoneBox').length > 0) 
		{ 
			$('input.telephoneBox').multipleTextbox({
				showIconOnly: true,
				removeLabel: '{$lblDelete|ucfirst}' 
			}); 
		}
		
		// mobile box
		if($('input.mobileBox').length > 0) 
		{ 
			$('input.mobileBox').multipleTextbox({ 
				showIconOnly: true,
				removeLabel: '{$lblDelete|ucfirst}' 
			}); 
		}
		
		// fax box
		if($('input.faxBox').length > 0) 
		{ 
			$('input.faxBox').multipleTextbox({ 
				showIconOnly: true,
				removeLabel: '{$lblDelete|ucfirst}' 
			}); 
		}
		
		// email box
		if($('input.emailBox').length > 0) 
		{ 
			$('input.emailBox').multipleTextbox({ 
				showIconOnly: true,
				removeLabel: '{$lblDelete|ucfirst}' 
			}); 
		}
		
		// website box
		if($('input.websiteBox').length > 0) 
		{ 
			$('input.websiteBox').multipleTextbox({ 
				showIconOnly: true,
				removeLabel: '{$lblDelete|ucfirst}' 
			}); 
		}
	},

	// end
	eoo: true
}


$(document).ready(function() { jsBackend.crm.init(); });