<?php

class BackendDataGrid extends SpoonDataGrid
{
	public function __construct(SpoonDataGridSource $source)
	{
		// execute parent constructor
		parent::__construct($source);

		// paginering object

		// instellen van default template, als die niet meegegeven is

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