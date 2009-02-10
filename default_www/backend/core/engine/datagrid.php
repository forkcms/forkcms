<?php
// @todo rewrite docs
class BackendDataGrid extends SpoonDataGrid
{
	public function __construct(SpoonDataGridSource $source)
	{
		// execute parent constructor
		parent::__construct($source);

		$this->setDebug(SPOON_DEBUG); // zorgt ervoor dat forceCompile -= true wordt

		// paginering object
		$this->setAttributes(array('border' => 1));

		// instellen van default template, als die niet meegegeven is
		$this->setTemplate(BACKEND_CORE_PATH .'/layout/templates/datagrid.tpl');

		// url instellen
		$this->setURL(BackendModel::createURLForAction());

	}
}

class BackendDataGridDB extends BackendDataGrid
{
	public function __construct($query, $parameters = array())
	{
		$source = new SpoonDataGridSourceDB(BackendModel::getDB(), array($query, (array) $parameters));
		parent::__construct($source);
	}
}

class BackendDataGridArray extends BackendDataGrid
{
	public function __construct(array $array)
	{
		parent::__construct(new SpoonDataGridSourceArray($array));
	}
}

?>