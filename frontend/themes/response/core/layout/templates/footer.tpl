<div class="holder" id="doormatHolder">
	<div id="doormat" class="row clearfix">
		<section  class="col-8">
			{option:positionBottomleft}
				{iteration:positionBottomleft}
				{option:!positionBottomleft.blockIsHTML}
					{$positionBottomleft.blockContent}
				{/option:!positionBottomleft.blockIsHTML}
				{option:positionBottomleft.blockIsHTML}
					{$positionBottomleft.blockContent}
				{/option:positionBottomleft.blockIsHTML}
				{/iteration:positionBottomleft}
			{/option:positionBottomleft}
		</section>
		<section class="col-4">
			{option:positionBottomright}
				{iteration:positionBottomright}
				{option:!positionBottomright.blockIsHTML}
					{$positionBottomright.blockContent}
				{/option:!positionBottomright.blockIsHTML}
				{option:positionBottomright.blockIsHTML}
					{$positionBottomright.blockContent}
				{/option:positionBottomright.blockIsHTML}
				{/iteration:positionBottomright}
			{/option:positionBottomright}
		</section>
	</div>
</div>
<div id="footerHolder" class="holder">
	<footer id="footer" class="row clearfix">
		<div class="col-12">
			<p class="copyright">Copyright &copy; {$now|date:'Y'} {$siteTitle}. Based on the <a href="http://fork-cms.be/extensions/themes">Response theme</a> by <a href="https://twitter.com/#!/simoncoudeville">Simon Coudeville</a>. Built with <a href="http://fork-cms.be">Fork CMS</a></p>
		</div>
	</footer>
</div>