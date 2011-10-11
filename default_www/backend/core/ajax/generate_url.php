<?php

/**
 * This action will generate a valid url based upon the submitted url.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendCoreAjaxGenerateUrl extends BackendBaseAJAXAction
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

		// create bogus form
		$frm = new BackendForm('meta');

		// get parameters
		$URL = SpoonFilter::getPostValue('url', null, '', 'string');
		$metaId = SpoonFilter::getPostValue('meta_id', null, null);
		$baseFieldName = SpoonFilter::getPostValue('baseFieldName', null, '', 'string');
		$custom = SpoonFilter::getPostValue('custom', null, false, 'bool');
		$className = SpoonFilter::getPostValue('className', null, '', 'string');
		$methodName = SpoonFilter::getPostValue('methodName', null, '', 'string');
		$parameters = SpoonFilter::getPostValue('parameters', null, '', 'string');

		// cleanup values
		$metaId = $metaId ? (int) $metaId : null;
		$parameters = @unserialize($parameters);

		// meta object
		$this->meta = new BackendMeta($frm, $metaId, $baseFieldName, $custom);

		// set callback for generating an unique URL
		$this->meta->setUrlCallback($className, $methodName, $parameters);

		// fetch generated meta url
		$URL = $this->meta->generateURL($URL);

		// output
		$this->output(self::OK, $URL);
	}
}

?>