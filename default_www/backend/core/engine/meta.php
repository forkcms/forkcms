<?php

/**
 * BackendMeta
 *
 * This class represents a META-object
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendMeta
{
	/**
	 * The name of the field we should use to generate default-values
	 *
	 * @var	string
	 */
	private $baseFieldName;


	/**
	 * The callback method
	 *
	 * @var	array
	 */
	private $callback = array();


	/**
	 * Do we need meta ustom
	 *
	 * @var	bool
	 */
	private $custom;


	/**
	 * The data, when a existing meta-record is loaded
	 *
	 * @var	array
	 */
	private $data;


	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	private $frm;


	/**
	 * The id, when an existing meta-record is loaded
	 *
	 * @var	int
	 */
	private $id;


	/**
	 * The url-instance
	 *
	 * @var	BackendURL
	 */
	private $url;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	BackendForm $form
	 * @param	int[optional] $metaId
	 * @param	string[optional] $baseFieldName
	 * @param	bool[optional] $custom
	 */
	public function __construct(BackendForm $form, $metaId = null, $baseFieldName = 'title', $custom = false)
	{
		// check if url is available from the referene
		if(!Spoon::isObjectReference('url')) throw new BackendException('Url should be available in the reference.');

		// get BackendURL instance
		$this->url = Spoon::getObjectReference('url');

		// should we use meta-custom
		$this->custom = (bool) $custom;

		// set form instance
		$this->frm = $form;

		// set base field name
		$this->baseFieldName = (string) $baseFieldName;

		// metaId was specified, so we should load the item
		if($metaId !== null) $this->loadMeta($metaId);

		// load the form
		$this->loadForm();
	}


	/**
	 * Add all element into the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// add page title elements into the form
		$this->frm->addCheckBox('page_title_overwrite', (isset($this->data['title_overwrite']) && $this->data['title_overwrite'] == 'Y'));
		$this->frm->addTextField('page_title', (isset($this->data['title'])) ? $this->data['title'] : null);

		// add meta description elements into the form
		$this->frm->addCheckBox('meta_description_overwrite', (isset($this->data['description_overwrite']) && $this->data['description_overwrite'] == 'Y'));
		$this->frm->addTextField('meta_description', (isset($this->data['description'])) ? $this->data['description'] : null);

		// add meta keywords elements into the form
		$this->frm->addCheckBox('meta_keywords_overwrite', (isset($this->data['keywords_overwrite']) && $this->data['keywords_overwrite'] == 'Y'));
		$this->frm->addTextField('meta_keywords', (isset($this->data['keywords'])) ? $this->data['keywords'] : null);

		// add url elements into the form
		$this->frm->addCheckBox('url_overwrite', (isset($this->data['url_overwrite']) && $this->data['url_overwrite'] == 'Y'));
		$this->frm->addTextField('url', (isset($this->data['url'])) ? $this->data['url'] : null);

		// should we add the meta-custom field
		if($this->custom)
		{
			// add meta custom element into the form
			$this->frm->addTextArea('meta_custom', (isset($this->data['custom'])) ? $this->data['custom'] : null);
		}
	}


	/**
	 * Load a specific meta-record
	 *
	 * @return	void
	 * @param	int $id
	 */
	private function loadMeta($id)
	{
		// redefine
		$this->id = (int) $id;

		// get database
		$db = BackendModel::getDB();

		// get item
		$this->data = (array) $db->getRecord('SELECT *
												FROM meta AS m
												WHERE m.id = ?;',
												array($this->id));

		// validate meta-record
		if(empty($this->data)) throw new BackendException('Meta-record doesn\'t exist.');
	}


	/**
	 * Saves the meta object
	 *
	 * @return	void
	 * @param	bool[optional] $update
	 */
	public function save($update = false)
	{
		// redefine
		$update = (bool) $update;

		// no callback set by user?
		if(empty($this->callback))
		{
			// build class- & method-name
			$className = 'Backend'. SpoonFilter::toCamelCase($this->url->getModule()) .'Model';
			$methodName = 'getURL';

			// set
			$this->setUrlCallback($className, $methodName);
		}

		// get meta keywords
		if($this->frm->getField('meta_keywords_overwrite')->isChecked()) $keywords = $this->frm->getField('meta_keywords')->getValue();
		else $keywords = $this->frm->getField($this->baseFieldName)->getValue();

		// get meta description
		if($this->frm->getField('meta_description_overwrite')->isChecked()) $description = $this->frm->getField('meta_description')->getValue();
		else $description = $this->frm->getField($this->baseFieldName)->getValue();

		// get page title
		if($this->frm->getField('page_title_overwrite')->isChecked()) $title = $this->frm->getField('page_title')->getValue();
		else $title = $this->frm->getField($this->baseFieldName)->getValue();

		// get url
		if($this->frm->getField('url_overwrite')->isChecked()) $url = SpoonFilter::urlise($this->frm->getField('url')->getValue());
		else $url = SpoonFilter::urlise($this->frm->getField($this->baseFieldName)->getValue());

		// build parameters for use in the callback
		$parameters[] = $url;
		$parameters[] = $this->id;

		// add parameters set by user
		if(!empty($this->callback['parameters']))
		{
			foreach($this->callback['parameters'] as $parameter)
			{
				$parameters[] = $parameter;
			}
		}

		// get the real url
		$url = call_user_func_array(array($this->callback['class'], $this->callback['method']), $parameters);

		// get meta custom
		if($this->frm->getField('meta_custom')->isFilled()) $custom = $this->frm->getField('meta_custom')->getValue();
		else $custom = null;

		// build meta
		$meta['keywords'] = $keywords;
		$meta['keywords_overwrite'] = ($this->frm->getField('meta_keywords_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['description'] = $description;
		$meta['description_overwrite'] = ($this->frm->getField('meta_description_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['title'] = $title;
		$meta['title_overwrite'] = ($this->frm->getField('page_title_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['url'] = $url;
		$meta['url_overwrite'] = ($this->frm->getField('url_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['custom'] = $custom;

		// get db
		$db = BackendModel::getDB();

		// should we update the record
		if($update)
		{
			// validate
			if($this->id === null) throw new BackendException('No metaID specified.');

			// update the existing record
			$db->update('meta', $meta, 'id = ?', array($this->id));

			// return the id
			return $this->id;
		}

		// insert a new meta-record
		else
		{
			// insert
			$id = (int) $db->insert('meta', $meta);

			// return the id
			return $id;
		}
	}


	/**
	 * Set the callback to calculate an unique url
	 * REMARK: this method has to be public and static
	 * REMARK: if you specify arguments they will be appended
	 *
	 * @return	void
	 * @param	string $className
	 * @param	string $methodName
	 * @param	array[optional] $parameters
	 */
	public function setURLCallback($className, $methodName, $parameters = array())
	{
		// redefine
		$className = (string) $className;
		$methodName = (string) $methodName;
		$parameters = (array) $parameters;

		// validate (check if the function exists)
		if(!method_exists($className, $methodName)) throw new BackendException('The callback-method doesn\'t exist.');

		// store in property
		$this->callback = array('class' => $className, 'method' => $methodName, 'parameters' => $parameters);
	}


	/**
	 * Validates the form
	 * It checks if there is a value when a checkbox is checked
	 *
	 * @return	void
	 */
	public function validate()
	{
		// no callback set by user?
		if(empty($this->callback))
		{
			// build class- & method-name
			$className = 'Backend'. SpoonFilter::toCamelCase($this->url->getModule()) .'Model';
			$methodName = 'getUrl';

			// set
			$this->setUrlCallback($className, $methodName);
		}

		// page title overwrite is checked
		if($this->frm->getField('page_title_overwrite')->isChecked())
		{
			$this->frm->getField('page_title')->isFilled(BL::getError('FieldIsRequired'));
		}

		// meta description overwrite is checked
		if($this->frm->getField('meta_description_overwrite')->isChecked())
		{
			$this->frm->getField('meta_description')->isFilled(BL::getError('FieldIsRequired'));
		}

		// meta keywords overwrite is checked
		if($this->frm->getField('meta_keywords_overwrite')->isChecked())
		{
			$this->frm->getField('meta_keywords')->isFilled(BL::getError('FieldIsRequired'));
		}

		// url overwrite is checked
		if($this->frm->getField('url_overwrite')->isChecked())
		{
			// filled
			$this->frm->getField('url')->isFilled(BL::getError('FieldIsRequired'));

			// fetch url
			$url = SpoonFilter::urlise($this->frm->getField('url')->getValue());

			// build parameters for use in the callback
			$parameters[] = $url;

			// add parameters set by user
			if(!empty($this->callback['parameters']))
			{
				foreach($this->callback['parameters'] as $parameter)
				{
					$parameters[] = $parameter;
				}
			}

			// get the real url
			$generatedUrl = call_user_func_array(array($this->callback['class'], $this->callback['method']), $parameters);

			// check if urls are different
			if($url != $generatedUrl) $this->frm->getField('url')->addError(BL::getError('URLAlreadyExists'));
		}
	}
}

?>