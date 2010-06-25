{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}

<div id="test" style="padding: 20px;">

	<h1>h1 Layout test</h1>
	<h2>h2 Layout test</h1>
	<h3>h3 Layout test</h1>

	<form class="forkForms">
		<p>
			<label for="e1">Naam</label>
			<input type="text" class="inputText" value="Johan Ronsse" id="e1" />
		</p>
		<p>
			<label for="e2">Wachtwoord</label>
			<input type="password" class="inputPassword" value="adassdk" id="e2" />
		</p>
		<p>
			<label for="e3">Code</label>
			<input type="text" class="inputText code" value="d41d8cd98f00b204e9800998ecf8427e" id="e3" />
		</p>
		<p>
			<label for="e4">Bestand<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
			<input type="file" id="e4" />
		</p>
		<p>
			<label for="e5">Combo box<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
			<select id="e5">
				<option>Optie 1</option>
				<option>Optie 2</option>
				<option>Optie 3</option>
			</select>
		</p>
		<div>
			<textarea class="inputEditor"></textarea>
		</div>
	</form>

</div>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}