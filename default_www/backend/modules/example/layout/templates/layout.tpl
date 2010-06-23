{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div id="test">

	<h1>h1 Layout test</h1>
	<h2>h2 Layout test</h2>
	<h3>h3 Layout test</h3>

	<form class="forkForms"action="#">
		<p>
			<label>Naam</label>
			<input type="text" class="inputText" value="Johan Ronsse" />
		</p>
		<p>
			<label>Wachtwoord</label>
			<input type="password" class="inputPassword" value="adassdk" />
		</p>
		<p>
			<label>Code</label>
			<input type="text" class="inputText code" value="d41d8cd98f00b204e9800998ecf8427e" />
		</p>
		<p>
			<label>Bestand*</label>
			<input type="file" />
		</p>
		<p>
			<label>Combo box*</label>
			<select>
				<option>Optie 1</option>
				<option>Optie 2</option>
				<option>Optie 3</option>
			</select>
		</p>
		<div>
			<textarea class="inputEditor" rows="5" cols="62"></textarea>
		</div>
	</form>

</div>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}
