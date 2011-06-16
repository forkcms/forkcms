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

		// user is god?
		$isGod = BackendAuthentication::getUser()->isGod();

		// get possible languages
		if($isGod) $possibleLanguages = array_unique(array_merge(BL::getWorkingLanguages(), BL::getInterfaceLanguages()));
		else $possibleLanguages = BL::getWorkingLanguages();

		// get parameters
		$language = SpoonFilter::getPostValue('language', array_keys($possibleLanguages), null, 'string');
		$module = SpoonFilter::getPostValue('module', BackendModel::getModules(false), null, 'string');
		$name = SpoonFilter::getPostValue('name', null, null, 'string');
		$type = SpoonFilter::getPostValue('type', BackendModel::getDB()->getEnumValues('locale', 'type'), null, 'string');
		$application = SpoonFilter::getPostValue('application', array('backend', 'frontend'), null, 'string');
		$value = SpoonFilter::getPostValue('value', null, null, 'string');

		// validate values
		if(trim($value) == '' || $language == '' || $module == '' || $type == '' || $application == '' || ($application == 'frontend' && $module != 'core')) $error = BL::err('InvalidValue');

		// in case this is a 'act' type, there are special rules concerning possible values
		if($type == 'act' && !isset($error)) if(!SpoonFilter::isValidAgainstRegexp('|^([a-z0-9\-\_])+$|', $value)) $error = BL::err('InvalidActionValue', 'locale');

		// no error?
		if(!isset($error))
		{
			// build item
			$item['language'] = $language;
			$item['module'] = $module;
			$item['name'] = $name;
			$item['type'] = $type;
			$item['application'] = $application;
			$item['value'] = $value;
			$item['edited_on'] = BackendModel::getUTCDate();
			$item['user_id'] = BackendAuthentication::getUser()->getUserId();

			// does the translation exist?
			if(BackendLocaleModel::existsByName($name, $type, $module, $language, $application))
			{
				// add the id to the item
				$item['id'] = (int) BackendLocaleModel::getByName($name, $type, $module, $language, $application);

				// update in db
				BackendLocaleModel::update($item);
			}

			// doesn't exist yet
			else
			{
				// insert in db
				BackendLocaleModel::insert($item);
			}

			// output OK
			$this->output(self::OK);
		}

		// output the error
		else $this->output(self::ERROR, null, $error);
	}
}

?>