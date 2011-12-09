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
<<<<<<< HEAD
 * @author Tijs Verkoyen <tijs@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
=======
 * @author Tijs Verkoyen <tijs@sumocoders.be>
>>>>>>> master
 */
class BackendMeta
{
	/**
	 * The default sitemap priority
	 *
	 * @var int
	 */
	const DEFAULT_PRIORITY = 0.8;

	/**
	 * The action to use for the sitemap
	 *
	 * @var	string
	 */
	protected $action;

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
	 * Do we need custom meta
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
	 * @var	BackendForm
	 */
	protected $form;

	/**
	 * @var	int
	 */
	protected $id;

	/**
	 * The module to use
	 *
	 * @var	string
	 */
	protected $module;

	/**
	 * The URL-instance
	 *
	 * @var	BackendURL
	 */
	protected $url;

	/**
	 * @param BackendForm $form An instance of Backendform, the elements will be parsed in here.
	 * @param int[optional] $metaId The metaID to load.
	 * @param string[optional] $baseFieldName The field where the URL should be based on.
	 * @param bool[optional] $custom Add/show custom-meta.
	 * @return BackendMeta
	 */
	public function __construct($metaId = null, $baseFieldName = 'title', $custom = false)
	{
		// check if URL is available from the referene
		if(!Spoon::exists('url')) throw new BackendException('URL should be available in the reference.');

		// get BackendURL instance
		$this->url = Spoon::get('url');
		$this->setModule();

		// should we use meta-custom
		$this->custom = (bool) $custom;

		// set base field name
		$this->baseFieldName = (string) $baseFieldName;

		// metaId was specified, so we should load the item
		if($metaId !== null) $this->loadMeta($metaId);

		return $this;
	}

	/**
	 * Delete the meta record
	 *
	 * @param int[optional] $metaId
	 */
	public function delete($metaId = null)
	{
		if($metaId === null && $this->id === null) throw new Exception('You must provide a meta-id to delete');

		if($metaId !== null) $this->loadMeta($metaId);

		// delete the meta data
		$db = FrontendModel::getDB(true);
		$db->delete('meta', 'id = ?', (int) $this->id);
		$db->delete('meta_sitemap', 'id = ?', (int) $this->data['sitemap_id']);
	}

	/**
	 * Generate an url, using the predefined callback.
	 *
	 * @param string $URL The base-url to start from.
	 * @return string
	 */
	public function generateUrl($URL)
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
	 * This will generate the full url of an item
	 *
	 * @return mixed
	 */
	public function getFullUrl()
	{
		// no module set
		if(!isset($this->module)) return false;

		if($this->module == 'pages') $fullUrl = (SITE_MULTILANGUAGE) ? '/' . BL::getWorkingLanguage() : '';
		else $fullUrl = BackendModel::getURLForBlock($this->module, $this->action);

		// 404 url?
		if($fullUrl == BackendModel::getURL(404)) return false;
		else return SITE_URL . $fullUrl;
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
		if($this->form->isSubmitted())
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
			if(!isset($_POST['use_sitemap'])) $_POST['use_sitemap'] = (isset($this->data['use_sitemap'])) ? $this->data['use_sitemap'] : 'N';
		}

		// add page title elements into the form
		$this->form->addCheckbox('page_title_overwrite', (isset($this->data['title_overwrite']) && $this->data['title_overwrite'] == 'Y'));
		$this->form->addText('page_title', (isset($this->data['title'])) ? $this->data['title'] : null);

		// add meta description elements into the form
		$this->form->addCheckbox('meta_description_overwrite', (isset($this->data['description_overwrite']) && $this->data['description_overwrite'] == 'Y'));
		$this->form->addText('meta_description', (isset($this->data['description'])) ? $this->data['description'] : null);

		// add meta keywords elements into the form
		$this->form->addCheckbox('meta_keywords_overwrite', (isset($this->data['keywords_overwrite']) && $this->data['keywords_overwrite'] == 'Y'));
		$this->form->addText('meta_keywords', (isset($this->data['keywords'])) ? $this->data['keywords'] : null);

		// add URL elements into the form
		$this->form->addCheckbox('url_overwrite', (isset($this->data['url_overwrite']) && $this->data['url_overwrite'] == 'Y'));
		$this->form->addText('url', (isset($this->data['url'])) ? urldecode($this->data['url']) : null);

		// sitemap enabled
		$this->form->addCheckbox('use_sitemap', (!isset($this->data['sitemap_use_sitemap']) || (isset($this->data['sitemap_use_sitemap']) && $this->data['sitemap_use_sitemap'] == 'Y')));
		$this->form->addCheckbox('sitemap_priority_overwrite', (isset($this->data['sitemap_priority']) && $this->data['sitemap_priority'] != self::DEFAULT_PRIORITY));
		$this->form->addText('sitemap_priority', (isset($this->data['sitemap_priority'])) ? urldecode($this->data['sitemap_priority']) : self::DEFAULT_PRIORITY);

		// the values for the change frequency
		$changeFrequency = array(
			'always' => BL::lbl('Always'),
			'hourly' => BL::lbl('Hourly'),
			'daily' => BL::lbl('Daily'),
			'weekly' => BL::lbl('Weekly'),
			'monthly' => BL::lbl('Monthly'),
			'yearly' => BL::lbl('Yearly'),
			'never' => BL::lbl('Never')
		);
		$this->form->addDropdown('sitemap_change_frequency', $changeFrequency, (isset($this->data['sitemap_change_frequency'])) ? $this->data['sitemap_change_frequency'] : 'weekly');

		// advanced SEO
		$indexValues = array(
			array('value' => 'none', 'label' => BL::getLabel('None')),
			array('value' => 'index', 'label' => 'index'),
			array('value' => 'noindex', 'label' => 'noindex')
		);
		$this->form->addRadiobutton('seo_index', $indexValues, (isset($this->data['data']['seo_index'])) ? $this->data['data']['seo_index'] : 'none');
		$followValues = array(
			array('value' => 'none', 'label' => BL::getLabel('None')),
			array('value' => 'follow', 'label' => 'follow'),
			array('value' => 'nofollow', 'label' => 'nofollow')
		);
		$this->form->addRadiobutton('seo_follow', $followValues, (isset($this->data['data']['seo_follow'])) ? $this->data['data']['seo_follow'] : 'none');

		// should we add the meta-custom field
		if($this->custom)
		{
			// add meta custom element into the form
			$this->form->addTextarea('meta_custom', (isset($this->data['custom'])) ? $this->data['custom'] : null);
		}

		$this->form->addHidden('meta_id', $this->id);
		$this->form->addHidden('base_field_name', $this->baseFieldName);
		$this->form->addHidden('custom', $this->custom);
		$this->form->addHidden('class_name', $this->callback['class']);
		$this->form->addHidden('method_name', $this->callback['method']);
		$this->form->addHidden('parameters', SpoonFilter::htmlspecialchars(serialize($this->callback['parameters'])));
	}

	/**
	 * Load a specific meta-record
	 *
	 * @param int $id The id of the record to load.
	 */
	protected function loadMeta($id)
	{
		$this->id = (int) $id;

		$this->data = (array) BackendModel::getDB()->getRecord(
			'SELECT
			 	m.*, s.id AS sitemap_id, s.module, s.action, s.priority AS sitemap_priority,
			 	s.change_frequency AS sitemap_change_frequency, s.visible AS sitemap_use_sitemap
			 FROM meta AS m
			 LEFT OUTER JOIN meta_sitemap AS s ON s.id = m.sitemap_id
			 WHERE m.id = ?',
			array($this->id)
		);

		// validate meta-record
		if(empty($this->data)) throw new BackendException('Meta-record doesn\'t exist.');

		// unserialize data
		if(isset($this->data['data'])) $this->data['data'] = @unserialize($this->data['data']);

		// set the module
		if(isset($this->data['module'])) $this->setModule($this->data['module']);
		if(isset($this->data['action'])) $this->setAction($this->data['action']);
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
		if($this->form->getField('meta_keywords_overwrite')->isChecked()) $keywords = $this->form->getField('meta_keywords')->getValue();
		else $keywords = $this->form->getField($this->baseFieldName)->getValue();

		// get meta description
		if($this->form->getField('meta_description_overwrite')->isChecked()) $description = $this->form->getField('meta_description')->getValue();
		else $description = $this->form->getField($this->baseFieldName)->getValue();

		// get page title
		if($this->form->getField('page_title_overwrite')->isChecked()) $title = $this->form->getField('page_title')->getValue();
		else $title = $this->form->getField($this->baseFieldName)->getValue();

		// get URL
		if($this->form->getField('url_overwrite')->isChecked()) $URL = SpoonFilter::htmlspecialcharsDecode($this->form->getField('url')->getValue());
		else $URL = SpoonFilter::htmlspecialcharsDecode($this->form->getField($this->baseFieldName)->getValue());

		// get the real URL
		$URL = $this->generateUrl($URL);

		// get meta custom
		if($this->custom && $this->form->getField('meta_custom')->isFilled()) $custom = $this->form->getField('meta_custom')->getValue(true);
		else $custom = null;

		// build meta
		$meta['keywords'] = $keywords;
		$meta['keywords_overwrite'] = ($this->form->getField('meta_keywords_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['description'] = $description;
		$meta['description_overwrite'] = ($this->form->getField('meta_description_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['title'] = $title;
		$meta['title_overwrite'] = ($this->form->getField('page_title_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['url'] = $URL;
		$meta['url_overwrite'] = ($this->form->getField('url_overwrite')->isChecked()) ? 'Y' : 'N';
		$meta['custom'] = $custom;
		$meta['data'] = null;
		if($this->form->getField('seo_index')->getValue() != 'none') $meta['data']['seo_index'] = $this->form->getField('seo_index')->getValue();
		if($this->form->getField('seo_follow')->getValue() != 'none') $meta['data']['seo_follow'] = $this->form->getField('seo_follow')->getValue();
		if(isset($meta['data'])) $meta['data'] = serialize($meta['data']);

		// get db
		$db = BackendModel::getDB(true);

		$sitemapPriority = $this->form->getField('sitemap_priority')->getValue();
		$sitemap['module'] = $this->module;
		$sitemap['action'] = $this->action;
		$sitemap['language'] = BL::getWorkingLanguage();
		$sitemap['url'] = $this->getURL();
		$sitemap['priority'] = ($sitemapPriority == '') ? self::DEFAULT_PRIORITY : $sitemapPriority;
		$sitemap['change_frequency'] = $this->form->getField('sitemap_change_frequency')->getValue();
		$sitemap['visible'] = ($this->form->getField('use_sitemap')->isChecked()) ? 'Y' : 'N';
		$sitemap['edited_on'] = BackendModel::getUTCDate();
		if(isset($this->data['sitemap_id'])) $sitemap['id'] = (int) $this->data['sitemap_id'];

		if($update)
		{
			if($this->id === null) throw new BackendException('No metaID specified.');
			$meta['sitemap_id'] = $this->saveSitemap($sitemap);

			$db->update('meta', $meta, 'id = ?', (int) $this->id);
		}
		else
		{
			// when working with revisions, a sitemap will already provided
			if(isset($this->data['sitemap_id'])) $meta['sitemap_id'] = $this->saveSitemap($sitemap);
			else $meta['sitemap_id'] = $this->saveSitemap($sitemap);

			$this->id = (int) $db->insert('meta', $meta);
		}

		// return the meta id
		return $this->id;
	}

	/**
	 * Save the sitemap data
	 *
	 * @param array $data
	 * @return int
	 */
	public function saveSitemap(array $data)
	{
		$db = BackendModel::getDB(true);

		// if there is an id given, we should update the record
		if(isset($data['id']) && $data['id'] != 0)
		{
			$itemId = $data['id'];
			unset($data['id']);

			$db->update('meta_sitemap', $data, 'id = ?', (int) $itemId);
		}
		// if there is no id given, create a new record
		else $itemId = $db->insert('meta_sitemap', $data);

		return $itemId;
	}

	/**
	 * Sets the action to use
	 *
	 * @return	void
	 * @param	string $action		The action to set.
	 */
	public function setAction($action)
	{
		$this->action = (string) $action;
	}

	/**
	 * Set the form
	 *
	 * @param BackendForm $form
	 */
	public function setForm(BackendForm $form)
	{
		$this->form = $form;

		// set default callback
		$this->setUrlCallback('Backend' . SpoonFilter::toCamelCase($this->url->getModule()) . 'Model', 'getURL');

		// load the form
		$this->loadForm();
	}

	/**
	 * Sets the module to use
	 *
	 * @param string[optional] $module
	 */
	public function setModule($module = null)
	{
		if($module === null)
		{
			$this->module = $this->url->getModule();
		}
		else $this->module = (string) $module;
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
		if($this->form->getField('page_title_overwrite')->isChecked())
		{
			$this->form->getField('page_title')->isFilled(BL::err('FieldIsRequired'));
		}

		// meta description overwrite is checked
		if($this->form->getField('meta_description_overwrite')->isChecked())
		{
			$this->form->getField('meta_description')->isFilled(BL::err('FieldIsRequired'));
		}

		// meta keywords overwrite is checked
		if($this->form->getField('meta_keywords_overwrite')->isChecked())
		{
			$this->form->getField('meta_keywords')->isFilled(BL::err('FieldIsRequired'));
		}

		// URL overwrite is checked
		if($this->form->getField('url_overwrite')->isChecked())
		{
			// filled
			$this->form->getField('url')->isFilled(BL::err('FieldIsRequired'));

			// fetch url
			$URL = $this->form->getField('url')->getValue();

			// get the real url
			$generatedUrl = $this->generateUrl($URL);

			// check if urls are different
			if($URL != $generatedUrl) $this->form->getField('url')->addError(BL::err('URLAlreadyExists'));
		}

		// if the form was submitted correctly the data array should be populated
		if($this->form->isCorrect())
		{
			// get meta keywords
			if($this->form->getField('meta_keywords_overwrite')->isChecked()) $keywords = $this->form->getField('meta_keywords')->getValue();
			else $keywords = $this->form->getField($this->baseFieldName)->getValue();

			// get meta description
			if($this->form->getField('meta_description_overwrite')->isChecked()) $description = $this->form->getField('meta_description')->getValue();
			else $description = $this->form->getField($this->baseFieldName)->getValue();

			// get page title
			if($this->form->getField('page_title_overwrite')->isChecked()) $title = $this->form->getField('page_title')->getValue();
			else $title = $this->form->getField($this->baseFieldName)->getValue();

			// get URL
			if($this->form->getField('url_overwrite')->isChecked()) $URL = SpoonFilter::htmlspecialcharsDecode($this->form->getField('url')->getValue());
			else $URL = SpoonFilter::htmlspecialcharsDecode($this->form->getField($this->baseFieldName)->getValue());

			// sitemap enabled
			if($this->form->getField('use_sitemap')->isChecked()) $useSitemap = 'Y';
			else $useSitemap = 'N';

			// get the real URL
			$URL = $this->generateUrl($URL);

			// get meta custom
			if($this->custom && $this->form->getField('meta_custom')->isFilled()) $custom = $this->form->getField('meta_custom')->getValue();
			else $custom = null;

			// set data
			$this->data['keywords'] = $keywords;
			$this->data['keywords_overwrite'] = ($this->form->getField('meta_keywords_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['description'] = $description;
			$this->data['description_overwrite'] = ($this->form->getField('meta_description_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['title'] = $title;
			$this->data['title_overwrite'] = ($this->form->getField('page_title_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['url'] = $URL;
			$this->data['url_overwrite'] = ($this->form->getField('url_overwrite')->isChecked()) ? 'Y' : 'N';
			$this->data['custom'] = $custom;
			$this->data['use_sitemap'] = $useSitemap;
			$this->data['sitemap_priority'] = $this->form->getField('sitemap_priority')->getValue();
			$this->data['sitemap_change_frequency'] = $this->form->getField('sitemap_change_frequency')->getValue();
			if($this->form->getField('seo_index')->getValue() == 'none') unset($this->data['data']['seo_index']);
			else $this->data['data']['seo_index'] = $this->form->getField('seo_index')->getValue();
			if($this->form->getField('seo_follow')->getValue() == 'none') unset($this->data['data']['seo_follow']);
			else $this->data['data']['seo_follow'] = $this->form->getField('seo_follow')->getValue();
		}
	}
}
