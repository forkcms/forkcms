<?php

/**
 * This action will update a translation using AJAX
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Lowie Benoot <lowie@netlash.com>
 * @since		2.1
 */
class BackendLocaleAjaxSaveTranslation extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$language = SpoonFilter::getPostValue('language', null, null, 'string');
		$module = SpoonFilter::getPostValue('module', null, null, 'string');
		$name = SpoonFilter::getPostValue('name', null, null, 'string');
		$type = SpoonFilter::getPostValue('type', null, null, 'string');
		$application = SpoonFilter::getPostValue('application', null, null, 'string');
		$value = SpoonFilter::getPostValue('value', null, null, 'string');

		// validate values
		$error = null;

		// in case this is a 'act' type, there are special rules concerning possible values
		if($type == 'act')
		{
			if(!SpoonFilter::isValidAgainstRegexp('|^([a-z0-9\-\_])+$|', $value)) $error = BL::err('InvalidActionValue', 'locale');
		}

		// no error?
		if($error == null)
		{
			// build item
			$item['language'] = $language;
			$item['module'] = $module;
			$item['name'] = $name;
			$item['type'] = $type;
			$item['application'] = $application;
			$item['value'] = $value;

			// save values
			if(BackendLocaleModel::existsByName($name, $type, $module, $language, $application))
			{
				// add the id to the item
				$item['id'] = (int) BackendLocaleModel::getByName($name, $type, $module, $language, $application);

				// update in db
				BackendLocaleModel::update($item);
			}

			else
			{
				// insert in db
				BackendLocaleModel::insert($item);
			}

			// output
			$this->output(self::OK);
		}

		// output the error
		else $this->output(self::ERROR, null, BL::getError('InvalidValue'));
	}
}

?>