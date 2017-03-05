<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Doctrine\Entity\Meta as MetaEntity;
use Common\Uri as CommonUri;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class represents a META-object
 *
 * @deprecated This class will be removed when all modules run on doctrine and will be replaced with the meta entity
 */
class Meta
{
    /**
     * The name of the field we should use to generate default-values
     *
     * @var string
     */
    protected $baseFieldName;

    /**
     * The callback method
     *
     * @var array
     */
    protected $callback = array();

    /**
     * Do we need meta custom
     *
     * @var bool
     */
    protected $custom;

    /**
     * The data, when a existing meta-record is loaded
     *
     * @var array
     */
    protected $data;

    /**
     * The form instance
     *
     * @var Form
     */
    protected $form;

    /**
     * The id, when an existing meta-record is loaded
     *
     * @var int
     */
    protected $id;

    /**
     * The URL-instance
     *
     * @var Url
     */
    protected $URL;

    /**
     * @param Form $form An instance of Form, the elements will be parsed in here.
     * @param int $metaId The metaID to load.
     * @param string $baseFieldName The field where the URL should be based on.
     * @param bool $showCustomMeta Add/show custom-meta.
     *
     * @throws Exception
     */
    public function __construct(
        Form $form,
        int $metaId = null,
        string $baseFieldName = 'title',
        bool $showCustomMeta = false
    ) {
        // check if URL is available from the reference
        if (!BackendModel::getContainer()->has('url')) {
            throw new Exception('URL should be available in the reference.');
        }

        // get BackendURL instance
        $this->URL = BackendModel::getContainer()->get('url');

        $this->custom = $showCustomMeta;
        $this->form = $form;
        $this->baseFieldName = $baseFieldName;

        // metaId was specified, so we should load the item
        if ($metaId !== null) {
            $this->loadMeta($metaId);
        }

        // set default callback
        $this->setURLCallback(
            'Backend\\Modules\\' . $this->URL->getModule() . '\\Engine\\Model',
            'getURL'
        );

        // load the form
        $this->loadForm();
    }

    /**
     * Generate an url, using the predefined callback.
     *
     * @param string $url The base-url to start from.
     *
     * @throws Exception When the function does not exist
     *
     * @return string
     *
     * @deprecated use the generateUrl method on the meta repository
     * This class will be removed when all modules run on doctrine
     */
    public function generateURL(string $url): string
    {
        return Model::get('fork.repository.meta')->generateURL(
            $url,
            $this->callback['class'],
            $this->callback['method'],
            $this->callback['parameters']
        );
    }

    /**
     * Get the current value for the meta-description;
     *
     * @return string|null
     */
    public function getDescription()
    {
        // not set so return null
        if (!isset($this->data['description'])) {
            return;
        }

        // return value
        return $this->data['description'];
    }

    /**
     * Should the description overwrite the default
     *
     * @return null|bool
     */
    public function getDescriptionOverwrite()
    {
        // not set so return null
        if (!isset($this->data['description_overwrite'])) {
            return;
        }

        // return value
        return $this->data['description_overwrite'] === 'Y';
    }

    /**
     * Get the current value for the metaId;
     *
     * @return null|int
     */
    public function getId()
    {
        // not set so return null
        if (!isset($this->data['id'])) {
            return;
        }

        // return value
        return (int) $this->data['id'];
    }

    /**
     * Get the current value for the meta-keywords;
     *
     * @return null|string
     */
    public function getKeywords()
    {
        // not set so return null
        if (!isset($this->data['keywords'])) {
            return;
        }

        // return value
        return $this->data['keywords'];
    }

    /**
     * Should the keywords overwrite the default
     *
     * @return null|bool
     */
    public function getKeywordsOverwrite()
    {
        // not set so return null
        if (!isset($this->data['keywords_overwrite'])) {
            return;
        }

        // return value
        return $this->data['keywords_overwrite'] === 'Y';
    }

    /**
     * Get the current value for the page title;
     *
     * @return null|string
     */
    public function getTitle()
    {
        // not set so return null
        if (!isset($this->data['title'])) {
            return;
        }

        // return value
        return $this->data['title'];
    }

    /**
     * Should the title overwrite the default
     *
     * @return null|bool
     */
    public function getTitleOverwrite()
    {
        // not set so return null
        if (!isset($this->data['title_overwrite'])) {
            return;
        }

        // return value
        return $this->data['title_overwrite'] === 'Y';
    }

    /**
     * Return the current value for an URL
     *
     * @return null|string
     */
    public function getURL()
    {
        // not set so return null
        if (!isset($this->data['url'])) {
            return;
        }

        // return value
        return urldecode($this->data['url']);
    }

    /**
     * Should the URL overwrite the default
     *
     * @return null|bool
     */
    public function getURLOverwrite()
    {
        // not set so return null
        if (!isset($this->data['url_overwrite'])) {
            return;
        }

        // return value
        return $this->data['url_overwrite'] === 'Y';
    }

    /**
     * If the fields are disabled we don't have any values in the post.
     * When an error occurs in the other fields of the form the meta-fields would be cleared
     * therefore we alter the POST so it contains the initial values.
     */
    private function loadValuesOfDisabledFields()
    {
        if (!isset($_POST['page_title'])) {
            $_POST['page_title'] = $this->data['title'] ?? null;
        }
        if (!isset($_POST['meta_description'])) {
            $_POST['meta_description'] = $this->data['description'] ?? null;
        }
        if (!isset($_POST['meta_keywords'])) {
            $_POST['meta_keywords'] = $this->data['keywords'] ?? null;
        }
        if (!isset($_POST['url'])) {
            $_POST['url'] = $this->data['url'] ?? null;
        }
        if ($this->custom && !isset($_POST['meta_custom'])) {
            $_POST['meta_custom'] = $this->data['custom'] ?? null;
        }
        if (!isset($_POST['seo_index'])) {
            $_POST['seo_index'] = $this->data['data']['seo_index'] ?? 'none';
        }
        if (!isset($_POST['seo_follow'])) {
            $_POST['seo_follow'] = $this->data['data']['seo_follow'] ??'none';
        }
    }

    /**
     * Add all element into the form
     */
    protected function loadForm()
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            $this->loadValuesOfDisabledFields();
        }

        // add page title elements into the form
        $this->form->addCheckbox(
            'page_title_overwrite',
            isset($this->data['title_overwrite']) && $this->data['title_overwrite'] === 'Y'
        );
        $this->form->addText('page_title', $this->data['title'] ?? null);

        // add meta description elements into the form
        $this->form->addCheckbox(
            'meta_description_overwrite',
            isset($this->data['description_overwrite']) && $this->data['description_overwrite'] === 'Y'
        );
        $this->form->addText(
            'meta_description',
            $this->data['description'] ?? null
        );

        // add meta keywords elements into the form
        $this->form->addCheckbox(
            'meta_keywords_overwrite',
            isset($this->data['keywords_overwrite']) && $this->data['keywords_overwrite'] === 'Y'
        );
        $this->form->addText('meta_keywords', $this->data['keywords'] ?? null);

        // add URL elements into the form
        $this->form->addCheckbox(
            'url_overwrite',
            isset($this->data['url_overwrite']) && $this->data['url_overwrite'] === 'Y'
        );
        $this->form->addText('url', isset($this->data['url']) ? urldecode($this->data['url']) : null);

        // advanced SEO
        $indexValues = array(
            array('value' => 'none', 'label' => BackendLanguage::getLabel('None')),
            array('value' => 'index', 'label' => 'index'),
            array('value' => 'noindex', 'label' => 'noindex'),
        );
        $this->form->addRadiobutton(
            'seo_index',
            $indexValues,
            $this->data['data']['seo_index'] ?? 'none'
        );
        $followValues = array(
            array('value' => 'none', 'label' => BackendLanguage::getLabel('None')),
            array('value' => 'follow', 'label' => 'follow'),
            array('value' => 'nofollow', 'label' => 'nofollow'),
        );
        $this->form->addRadiobutton(
            'seo_follow',
            $followValues,
            $this->data['data']['seo_follow'] ?? 'none'
        );

        // should we add the meta-custom field
        if ($this->custom) {
            // add meta custom element into the form
            $this->form->addTextarea('meta_custom', $this->data['custom'] ?? null);
        }

        $this->form->addHidden('meta_id', $this->id);
        $this->form->addHidden('base_field_name', $this->baseFieldName);
        $this->form->addHidden('custom', $this->custom);
        $this->form->addHidden('class_name', $this->callback['class']);
        $this->form->addHidden('method_name', $this->callback['method']);
        $this->form->addHidden('parameters', \SpoonFilter::htmlspecialchars(serialize($this->callback['parameters'])));
    }

    /**
     * Load a specific meta-record
     *
     * @param int $id The id of the record to load.
     *
     * @throws Exception If no meta-record exists with the provided id
     */
    protected function loadMeta(int $id)
    {
        $this->id = $id;

        $this->data = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT *
             FROM meta AS m
             WHERE m.id = ?',
            array($this->id)
        );

        // validate meta-record
        if (empty($this->data)) {
            throw new Exception('Meta-record doesn\'t exist.');
        }

        // unserialize data
        if (isset($this->data['data'])) {
            $this->data['data'] = @unserialize($this->data['data']);
        }
    }

    /**
     * Saves the meta object
     *
     * @param bool $update Should we update the record or insert a new one.
     *
     * @throws Exception If no meta id was provided.
     *
     * @return int
     *
     * @deprecated just use the entity for doctrine.
     *             This class will be removed when all modules run on doctrine and will be replaced with the meta entity
     */
    public function save(bool $update = false): int
    {
        $this->validate();

        //serialize data for save
        if (!empty($this->data['data'])) {
            $this->data['data'] = serialize($this->data['data']);
        }

        // build meta
        $db = BackendModel::getContainer()->get('database');

        if ($this->id !== null && $update === true) {
            $db->update('meta', $this->data, 'id = ?', array($this->id));

            return $this->id;
        }

        unset($this->data['id']);

        return (int) $db->insert('meta', $this->data);
    }

    /**
     * Set the callback to calculate an unique URL
     * REMARK: this method has to be public and static
     * REMARK: if you specify arguments they will be appended
     *
     * @param string $className Name of the class to use.
     * @param string $methodName Name of the method to use.
     * @param array $parameters Parameters to parse, they will be passed after ours.
     */
    public function setURLCallback(string $className, string $methodName, array $parameters = array())
    {
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
        if ($this->form->getField('page_title_overwrite')->isChecked()) {
            $this->form->getField('page_title')->isFilled(BackendLanguage::err('FieldIsRequired'));
        }

        // meta description overwrite is checked
        if ($this->form->getField('meta_description_overwrite')->isChecked()) {
            $this->form->getField('meta_description')->isFilled(BackendLanguage::err('FieldIsRequired'));
        }

        // meta keywords overwrite is checked
        if ($this->form->getField('meta_keywords_overwrite')->isChecked()) {
            $this->form->getField('meta_keywords')->isFilled(BackendLanguage::err('FieldIsRequired'));
        }

        // URL overwrite is checked
        if ($this->form->getField('url_overwrite')->isChecked()) {
            $this->form->getField('url')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $url = \SpoonFilter::htmlspecialcharsDecode($this->form->getField('url')->getValue());
            $generatedUrl = $this->generateURL($url);

            // check if urls are different
            if (CommonUri::getUrl($url) !== $generatedUrl) {
                $this->form->getField('url')->addError(
                    BackendLanguage::err('URLAlreadyExists')
                );
            }
        }

        // if the form was submitted correctly the data array should be populated
        if (!$this->form->isCorrect()) {
            return;
        }

        $this->data['keywords'] = $this->form->getField('meta_keywords_overwrite')->getActualValue(
            $this->form->getField('meta_keywords')->getValue(),
            $this->form->getField($this->baseFieldName)->getValue()
        );
        $this->data['keywords_overwrite'] = $this->form->getField('meta_keywords_overwrite')->getActualValue();
        $this->data['description'] = $this->form->getField('meta_description_overwrite')->getActualValue(
            $this->form->getField('meta_description')->getValue(),
            $this->form->getField($this->baseFieldName)->getValue()
        );
        $this->data['description_overwrite'] = $this->form->getField('meta_description_overwrite')->getActualValue();
        $this->data['title'] = $this->form->getField('page_title_overwrite')->getActualValue(
            $this->form->getField('page_title')->getValue(),
            $this->form->getField($this->baseFieldName)->getValue()
        );
        $this->data['title_overwrite'] = $this->form->getField('page_title_overwrite')->getActualValue();
        $this->data['url'] = $this->generateURL(
            $this->form->getField('url_overwrite')->getActualValue(
                \SpoonFilter::htmlspecialcharsDecode($this->form->getField('url')->getValue()),
                \SpoonFilter::htmlspecialcharsDecode($this->form->getField($this->baseFieldName)->getValue())
            )
        );
        $this->data['url_overwrite'] = $this->form->getField('url_overwrite')->getActualValue();
        $this->data['custom'] = $this->custom && $this->form->getField('meta_custom')->isFilled()
            ? $this->form->getField('meta_custom')->getValue() : null;
        $this->data['data']['seo_index'] = $this->form->getField('seo_index')->getValue();
        $this->data['data']['seo_follow'] = $this->form->getField('seo_follow')->getValue();

        if ($this->data['data']['seo_index'] === 'none') {
            unset($this->data['data']['seo_index']);
        }
        if ($this->data['data']['seo_follow'] === 'none') {
            unset($this->data['data']['seo_follow']);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return MetaEntity
     */
    public function getMetaEntity(): MetaEntity
    {
        $this->validate();

        return MetaEntity::fromBackendMeta($this);
    }
}
