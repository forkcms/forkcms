<?php

/**
 * BackendForm, this is our extended version of SpoonForm
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendForm extends SpoonForm
{
	/**
	 * TEten
	 *
	 * @var	BackendURL
	 */
	private $url;

	public function __construct($name = null, $action = null, $method = 'post')
	{
		$this->url = Spoon::getObjectReference('url');

		$name = ($name === null) ? SpoonFilter::toCamelCase($this->url->getModule() .'_'. $this->url->getAction(), '_', true) : (string) $name;
		$action = ($action === null) ? BackendModel::createUrlForAction() : (string) $action;
		parent::__construct($name, $action, $method);
	}


	public function addTextFieldsssss()
	{
		// 255 maxlength
	}


	public function parse(SpoonTemplate $tpl)
	{
		parent::parse($tpl);


		$this->validate();
		Spoon::dump($this);
		if($this->isSubmitted() && !$this->getCorrect()) $tpl->assign('formError', true);

		Spoon::dump($tpl);
	}
}

?>