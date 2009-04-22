<?php

/**
 * LanguagesIndex
 *
 * This is the index-action (default), it will display a datagrid depending on the given parameters
 *
 * @package		backend
 * @subpackage	languages
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class LanguagesIndex extends BackendBaseActionIndex
{
	/**
	 * Application (for the search criteria)
	 *
	 * @var	string
	 */
	private $filterApplication, $filterLanguage, $filterModule, $filterName, $filterValue;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// sets the search criteria for displaying language labels
		$this->setFilter();

		// load datagrid

		// parse datagrid

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Parse the correct messages into the template
	 *
	 * @return	void
	 */
	public function parse()
	{




		// dump de datagrid met de talen!
		$datagrid = new BackendDataGridDB('SELECT * FROM languages_labels');
		$datagrid->setColumnHidden('id');
		$this->tpl->assign('datagrid', ($datagrid->getNumResults() > 0) ? $datagrid->getContent() : false);
	}


	private function setFilter()
	{
		// table languages bijmaken en die gaat puur en alleen de afkorting, taal en opties van die taal bijhouden.
		$this->language = null;
		$this->application = null;
		$this->type = null;
		$this->module = null;
		$this->name = null;
		$this->value = null;
//		Spoon::dump($this);


/*
 * <div id="languages-filter">
	{form:filter}
		<dl>
			<dt><label for="language">{$lblLanguage|ucfirst}</label></dt>
				<dd>{$ddmLanguage}</dd>
			<dt><label for="applicatioin">{$lblApplication|ucfirst}</label></dt>
				<dd>{$ddmApplication}</dd>
			<dt><label for="type">{$lblType|ucfirst}</label></dt>
				<dd>{$ddmType}</dd>
			<dt><label for="module">{$lblModule|ucfirst}</label></dt>
				<dd>{$ddmModule}</dd>
			<dt><label for="name">{$lblName|ucfirst}</label></dt>
				<dd>{$txtName}</dd>
			<dt><label for="value">{$lblValue|ucfirst}</label></dt>
				<dd>{$txtValue}</dd>
			<dt>&nbsp;</dt>
				<dd><input type="submit" name="submit" value="{$lblFilter|ucfirst}" /></dd>
		</dl>
	{/form:filter}
</div>
 */
	}
}

?>