{$head}
<body id="installer">
	<div id="installHolder" class="step1">
		<h2>Install Fork CMS</h2>
		<form action="index.php" method="get" id="step1" class="forkForms submitWithLink">
			<div>
				<input type="hidden" name="step" value="1" />
				<div class="horizontal">
					{$content}
				</div>
			</div>
		</form>
	</div>
</body>
</html>