<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class represents a META-object
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendMeta
{
	/**
	 * The name of the field we should use to generate default-values
	 *
	 * @var	string
	 */
	protected $baseFieldName;

	/**
	 * The callback method
	 *
	 * @var	array
	 */
	protected $callback = array();

	/**
	 * Do we need meta custom
	 *
	 * @var	bool
	 */
	protected $custom;

	/**
	 * The data, when a existing meta-record is loaded
	 *
	 * @var	array
	 */
	protected $data;

	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	protected $frm;

	/**
	 * The id, when an existing meta-record is loaded
	 *
	 * @var	int
	 */
	protected $id;

	/**
	 * The URL-instance
	 *
	 * @var	BackendURL
	 */
	protected $URL;

	/**
	 * @param BackendForm $form An instance of Backendform, the elements will be parsed in here.
	 * @param int[optional] $metaId The metaID to load.
	 * @param string[optional] $baseFieldName The field where the URL should be based on.
	 * @param bool[optional] $custom Add/show custom-meta.
	 */
	public function __construct(BackendForm $form, $metaId = null, $baseFieldName = 'title', $custom = false)
	{
		// check if URL is available from the reference
		if(!Spoon::exists('url')) throw new BackendException('URL should be available in the reference.');

		// get BackendURL instance
		$this->URL = Spoon::get('url');

		// should we use meta-custom
		$this->custom = (bool) $custom;

		// set form instance
		$this->frm = $form;

		// set base field name
		$this->baseFieldName = (string) $baseFieldName;

		// metaId was specified, so we should load the item
		if($metaId !== null) $this->loadMeta($metaId);

		// set default callback
		$this->setUrlCallback('Backend' . SpoonFilter::toCamelCase($this->URL->getModule()) . 'Model', 'getURL');

		// load the form
		$this->loadForm();
	}

	/**
	 * Generate an url, using the predefined callback.
	 *
	 * @param string $URL The base-url to start from.
	 * @return string
	 */
	public function generateURL($URL)
	{
		// validate (check if the function exists)
		if(!is_callable(array($this->callback['class'], $this->callback['method']))) throw new BackendException('The callback-method doesn\'t exist.');

		// build parameters for use in the callback
		$parameters[] = SpoonFilter::urlise($URL);

		// add parameters set by user
		if(!empty($this->callback['parameters']))
		{
			foreach($this->callback['parameters'] as $parameter) $parameters[] = $parameter;
		}

		// get the real url
		return call_user_func_array(array($this->callback['class'], $this->callback['method']), $parameters);
	}

	/**
	 * Get the current value for the meta-description;
	 *
	 * @return mixed
	 */
	public function getDescription()
	{
		// not set so return null
		if(!isset($this->data['description'])) return null;

		// return value
		return $this->data['description'];
	}

	/**
	 * Should the description overwrite the default
	 *
	 * @return mixed
	 */
	public function getDescriptionOverwrite()
	{
		// not set so return null
		if(!isset($this->data['description_overwrite'])) return null;

		// return value
		return ($this->data['description_overwrite'] == 'Y');
	}

	/**
	 * Get the current value for the metaId;
	 *
	 * @return mixed
	 */
	public function getId()
	{
		// not set so return null
		if(!isset($this->data['id'])) return null;

		// return value
		return (int) $this->data['id'];
	}

	/**
	 * Get the current value for the meta-keywords;
	 *
	 * @return mixed
	 */
	public function getKeywords()
	{
		// not set so return null
		if(!isset($this->data['keywords'])) return null;

		// return value
		return $this->data['keywords'];
	}

	/**
	 * Should the keywords overwrite the default
	 *
	 * @return mixed
	 */
	public function getKeywordsOverwrite()
	{
		// not set so return null
		if(!isset($this->data['keywords_overwrite'])) return null;

		// return value
		return ($this->data['keywords_overwrite'] == 'Y');
	}

	/**
	 * Get the current value for the page title;
	 *
	 * @return mixed
	 */
	public function getTitle()
	{
		// not set so return null
		if(!isset($this->data['title'])) return null;

		// return value
		return $this->data['title'];
	}

	/**
	 * Should the title overwrite the default
	 *
	 * @return mixed
	 */
	public function getTitleOverwrite()
	{
		// not set so return null
		if(!isset($this->data['title_overwrite'])) return null;

		// return value
		return ($this->data['title_overwrite'] == 'Y');
	}

	/**
	 * Return the current value for an URL
	 *
	 * @return mixed
	 */
	public function getURL()
	{
		// not set so return null
		if(!isset($this->data['url'])) return null;

		// return value
		return urldecode($this->data['url']);
	}

	/**
	 * Should the URL overwrite the default
	 *
	 * @return mixed
	 */
	public function getURLOverwrite()
	{
		// not set so return null
		if(!isset($this->data['url_overwrite'])) return null;

		// return value
		return ($this->data['url_overwrite'] == 'Y');
	}

	/**
	 * Add all element into the form
	 */
	protected function loadForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			/**
			 * If the fields are disabled we don't have any values in the post. When an error occurs in the other fields of the form the meta-fields would be cleared
			 * therefore we alter the POST so it contains the initial values.
			 */
			if(!isset($_POST['page_title'])) $_POST['page_title'] = (isset($this->data['title'])) ? $this->data['title'] : null;
			if(!isset($_POST['meta_description'])) $_POST['meta_description'] = (isset($this->data['description'])) ? $this->data['description'] : null;
			if(!isset($_POST['meta_keywords'])) $_POST['meta_keywords'] = (isset($this->data['keywords'])) ? $this->data['keywords'] : null;
			if(!isset($_POST['url'])) $_POST['url'] = (isset($this->data['url'])) ? $this->data['url'] : null;
			if($this->custom && !isset($_POST['meta_custom'])) $_POST['meta_custom'] = (isset($this->data['custom'])) ? $this->data['custom'] : null;
			if(!isset($_POST['seo_index'])) $_POST['seo_index'] = (isset($this->data['data']['seo_index'])) ? $this->data['data']['seo_index'] : 'none';
			if(!isset($_POST['seo_follow'])) $_POST['seo_follow'] = (isset($this->data['data']['seo_follow'])) ? $this->data['data']['seo_follow'] : 'none';
		}

		// add page title elements into the form
		$this->frm->addCheckbox('page_title_overwrite', (isset($this->data['title_overwrite']) && $this->data['title_overwrite'] == 'Y'));
		$this->frm->addText('page_title', (isset($this->data['title'])) ? $this->data['title'] : null);

		// add meta description elements into the form
		$this->frm->addCheckbox('meta_description_overwrite', (isset($this->data['description_overwrite']) && $this->data['description_overwrite'] == 'Y'));
		$this->frm->addText('meta_description', (isset($this->data['description'])) ? $this->data['description'] : null);

		// add meta keywords elements into the form
		$this->frm->addCheckbox('meta_keywords_overwrite', (isset($this->data['keywords_overwrite']) && $this->data['keywords_overwrite'] == 'Y'));
		$this->frm->addText('meta_keywords', (isset($this->data['keywords'])) ? $this->data['keywords'] : null);

		// add URL elements into the form
		$this->frm->addCheckbox('url_overwrite', (isset($this->data['url_overwrite']) && $this->data['url_overwrite'] == 'Y'));
		$this->frm->addText('url', (isset($this->data['url'])) ? urldecode($this->data['url']) : null);

		// advanced SEO
		$indexValues = array(
			array('value' => 'none', 'label' => BL::getLabel('None')),
			array('value' => 'index', 'label' => 'index'),
			array('value' => 'noindex', 'label' => 'noindex')
		);
		$this->frm->addRadiobutton('seo_index', $indexValues, (isset($this->data['data']['seo_index'])) ? $this->data['data']['seo_index'] : 'none');
		$followValues = array(
			array('value' => 'none', 'label' => BL::getLabel('None')),
			array('value' => 'follow', 'label' => 'follow'),
			array('value' => 'nofollow', 'label' => 'nofollow')
		);
		$this->frm->addRadiobutton('seo_follow', $followValues, (isset($this->data['data']['seo_follow'])) ? $this->data['data']['seo_follow'] : 'none');

		// should we add the meta-custom field
		if($this->custom)
		{
			// add meta custom element into the form
			$this->frm->addTextarea('meta_custom', (isset($this->data['custom'])) ? $this->data['custom'] : null);
		}

		$this->frm->addHidden('meta_id', $this->id);
		$this->frm->addHidden('base_field_name', $this->baseFieldName);
		$this->frm->addHidden('custom', $this->custom);
		$this->frm->addHidden('class_name', $this->callback['class']);
		$this->frm->addHidden('method_name', $this->callback['method']);
		$this->frm->addHidden('parameters', SpoonFilter::htmlspecialchars(serialize($this->callback['parameters'])));
	}

	/**
	 * Load a specific meta-record
	 *
	 * @param int $id The id of the record to load.
	 */
	protected function loadMeta($id)
	{
		$this->id = (int) $id;

		// get item
		$this->data = (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT *
			 FROM meta AS m
			 WHERE m.id = ?',
			array($this->id)
		);

		// validate meta-record
		if(empty($this->data)) throw new BackendException('Meta-record doesn\'t exist.');

		// unserialize data
		if(isset($this->data['data'])) $this->data['data'] = @unserialize($this->data['data']);
	}

	/**
	 * Saves the meta object
	 *
	 * @param bool[optional] $update Should we update the record or insert a new one.
	 * @return int
	 */
	public function save($update = false)
	{
		$update = (bool) $update;

		// get meta keywords
		if($this->frm->getField('meta_keywords_overwrite')->isChecked()) $keywords = $this->frm->getField('meta_keywords')->getValue();
		else $keywords = $this->frm->getField($this->baseFieldName)->getValue();

		// get meta description
		if($this->frm->getField('meta_description_overwrite')->isChecked()) $description = $this->frm->getField('meta_description')->getValue();
		else $description = $this->frm->getField($this->baseFieldName)->getValue();

		// get page title
		if($this->frm->getField('page_title_overwrite')->isChecked()) $title = $this->frm->getField('page_title')->getValue();
		else $title = $this->frm->getField($this->baseFieldName)->getValue();

		// get URL
		if($this->frm->getField('url_overwrite')->isChecked()) $URL = SpoonFilter::htmlspecialcharsDecode($this->frm->getField('url')->getValue());
		else $URL = SpoonFilter::htmlspecialcharsDecode($this->frm->getField($this->baseFieldName)->getValue());

		// get the real URL
		$URL = $this->generateURL($URL);

		// get meta custom
		if($this->custom && $this->frm->getField('meta_custom')->isFilled()) $custom = $this->frm->getField('meta_custom')->getValue(true);
		else $custom = null;

		// build meta
		$meta['keywords'] = $keywords;
		$meta['keywords_overwrite'] = ($this->frm->getField('meta_keywords_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['description'] = $description;
		$meta['description_overwrite'] = ($this->frm->getField('meta_description_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['title'] = $title;
		$meta['title_overwrite'] = ($this->frm->getField('page_title_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['url'] = $URL;
		$meta['url_overwrite'] = ($this->frm->getField('url_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['custom'] = $custom;
		$meta['data'] = null;
		if($this->frm->getField('seo_index')->getValue() != 'none') $meta['data']['seo_index'] = $this->frm->getField('seo_index')->getValue();
		if($this->frm->getField('seo_follow')->getValue() != 'none') $meta['data']['seo_follow'] = $this->frm->getField('seo_follow')->getValue();
		if(isset($meta['data'])) $meta['data'] = serialize($meta['data']);

		// get db
		$db = BackendModel::getContainer()->get('database');

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
	 * Set the callback to calculate an unique URL
	 * REMARK: this method has to be public and static
	 * REMARK: if you specify arguments they will be appended
	 *
	 * @param string $className Name of the class to use.
	 * @param string $methodName Name of the method to use.
	 * @param array[optional] $parameters Parameters to parse, they will be passed after ours.
	 */
	public function setURLCallback($className, $methodName, $parameters = array())
	{
		$className = (string) $className;
		$methodName = (string) $methodName;
		$parameters = (array) $parameters;

		// store in property
		$this->callback = array('class' => $className, 'method' => $methodName, 'parameters' => $parameters);

		// re-load the form
		$this->loadForm();
	}

	/**
	 * Validates the form
	 * It checks if there is a value when a checkbox is checked
	 */
	public function validate()
	{
		// page title overwrite is checked
		if($this->frm->getField('page_title_overwrite')->isChecked())
		{
			$this->frm->getField('page_title')->isFilled(BL::err('FieldIsRequired'));
		}

		// meta description overwrite is checked
		if($this->frm->getField('meta_description_overwrite')->isChecked())
		{
			$this->frm->getField('meta_description')->isFilled(BL::err('FieldIsRequired'));
		}

		// meta keywords overwrite is checked
		if($this->frm->getField('meta_keywords_overwrite')->isChecked())
		{
			$this->frm->getField('meta_keywords')->isFilled(BL::err('FieldIsRequired'));
		}

		// URL overwrite is checked
		if($this->frm->getField('url_overwrite')->isChecked())
		{
			// filled
			$this->frm->getField('url')->isFilled(BL::err('FieldIsRequired'));

			// fetch url
			$URL = $this->frm->getField('url')->getValue();

			// get the real url
			$generatedUrl = $this->generateURL($URL);

			// check if urls are different
			if($URL != $generatedUrl) $this->frm->getField('url')->addError(BL::err('URLAlreadyExists'));
		}

		// if the form was submitted correctly the data array should be populated
		if($this->frm->isCorrect())
		{
			// get meta keywords
			if($this->frm->getField('meta_keywords_overwrite')->isChecked()) $keywords = $this->frm->getField('meta_keywords')->getValue();
			else $keywords = $this->frm->getField($this->baseFieldName)->getValue();

			// get meta description
			if($this->frm->getField('meta_description_overwrite')->isChecked()) $description = $this->frm->getField('meta_description')->getValue();
			else $description = $this->frm->getField($this->baseFieldName)->getValue();

			// get page title
			if($this->frm->getField('page_title_overwrite')->isChecked()) $title = $this->frm->getField('page_title')->getValue();
			else $title = $this->frm->getField($this->baseFieldName)->getValue();

			// get URL
			if($this->frm->getField('url_overwrite')->isChecked()) $URL = SpoonFilter::htmlspecialcharsDecode($this->frm->getField('url')->getValue());
			else $URL = SpoonFilter::htmlspecialcharsDecode($this->frm->getField($this->baseFieldName)->getValue());

			// get the real URL
			$URL = $this->generateURL($URL);

			// get meta custom
			if($this->custom && $this->frm->getField('meta_custom')->isFilled()) $custom = $this->frm->getField('meta_custom')->getValue();
			else $custom = null;

			// set data
			$this->data['keywords'] = $keywords;
			$this->data['keywords_overwrite'] = ($this->frm->getField('meta_keywords_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['description'] = $description;
			$this->data['description_overwrite'] = ($this->frm->getField('meta_description_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['title'] = $title;
			$this->data['title_overwrite'] = ($this->frm->getField('page_title_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['url'] = $URL;
			$this->data['url_overwrite'] = ($this->frm->getField('url_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['custom'] = $custom;
			if($this->frm->getField('seo_index')->getValue() == 'none') unset($this->data['data']['seo_index']);
			else $this->data['data']['seo_index'] = $this->frm->getField('seo_index')->getValue();
			if($this->frm->getField('seo_follow')->getValue() == 'none') unset($this->data['data']['seo_follow']);
			else $this->data['data']['seo_follow'] = $this->frm->getField('seo_follow')->getValue();
		}
	}
}
