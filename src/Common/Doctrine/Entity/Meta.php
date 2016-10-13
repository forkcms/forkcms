<?php

namespace Common\Doctrine\Entity;

use Backend\Core\Engine\Meta as BackendMeta;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="meta")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Meta
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $keywords;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="keywords_overwrite", options={"default" = "N"})
     */
    private $keywordsOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="description_overwrite", options={"default" = "N"})
     */
    private $descriptionOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="title_overwrite", options={"default" = "N"})
     */
    private $titleOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="url_overwrite", options={"default" = "N"})
     */
    private $urlOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom;

    /**
     * @var array
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @param string $keywords
     * @param bool $keywordsOverwrite
     * @param string $description
     * @param bool $descriptionOverwrite
     * @param string $title
     * @param bool $titleOverwrite
     * @param string $url
     * @param bool $urlOverwrite
     * @param string $custom
     * @param array $data
     * @param int|null $id
     */
    public function __construct(
        $keywords,
        $keywordsOverwrite,
        $description,
        $descriptionOverwrite,
        $title,
        $titleOverwrite,
        $url,
        $urlOverwrite,
        $custom,
        array $data,
        $id = null
    ) {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->url = $url;
        $this->urlOverwrite = $urlOverwrite;
        $this->custom = $custom;
        $this->data = $data;
        $this->id = $id;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serialiseData()
    {
        if (!empty($this->data)) {
            $this->data = serialize($this->data);

            return;
        }

        $this->data = null;
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @ORM\PostLoad
     */
    public function unSerialiseData()
    {
        if ($this->data !== null) {
            $this->data = unserialize($this->data);

            return;
        }

        $this->data = array();
    }

    /**
     * @param BackendMeta $meta
     *
     * @return self
     */
    public static function fromBackendMeta(BackendMeta $meta)
    {
        $metaData = $meta->getData();

        return new self(
            $metaData['keywords'],
            $metaData['keywords_overwrite'] === 'Y',
            $metaData['description'],
            $metaData['description_overwrite'] === 'Y',
            $metaData['title'],
            $metaData['title_overwrite'] === 'Y',
            $metaData['url'],
            $metaData['url_overwrite'] === 'Y',
            $metaData['custom'],
            isset($metaData['data']) ? $metaData['data'] : [],
            $meta->getId()
        );
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @return boolean
     */
    public function isKeywordsOverwrite()
    {
        return $this->keywordsOverwrite;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function isDescriptionOverwrite()
    {
        return $this->descriptionOverwrite;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return boolean
     */
    public function isTitleOverwrite()
    {
        return $this->titleOverwrite;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return boolean
     */
    public function isUrlOverwrite()
    {
        return $this->urlOverwrite;
    }

    /**
     * @return string
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return SEOIndex
     */
    public function getSEOIndex()
    {
        return SEOIndex::fromString($this->data['seo_index']);
    }

    /**
     * @return SEOFollow
     */
    public function getSEOFollow()
    {
        return SEOFollow::fromString($this->data['seo_follow']);
    }
}
