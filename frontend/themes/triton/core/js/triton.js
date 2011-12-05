$(document).ready(function()
{
	// a-img problem
	$('.content a > img').parent().addClass('linkedImage');

	// p-img problem
	$('.content p img').each(function(i)
	{
		// get parent (p)
		var parent = $(this).parents('p').get(0);

		// copy of parent
		parentCopy = $(parent).clone();

		// get all images inside parent
		parentCopy.find('img').each(function()
		{
			if($(this).hasClass('alignLeft') || $(this).hasClass('alignRight'))
			{
				if($(this).parent('a').length) $(this).parent('a').remove();
				else $(this).remove();
			}
		});

		// no more content left = only images so we'll add a class to the container
		if(!parentCopy.html().replace(/\s*/g, '')) parent.addClass('floatedImage');
	});

	// enable the share-menu
	if($('.share').length > 0) $('.share').shareMenu();
});