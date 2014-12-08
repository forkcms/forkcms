<?php
namespace Backend\Core\Entity;
use Doctrine\ORM\Mapping AS ORM;
use Common\Uri AS CommonUri;

/**
 * @ORM\Entity
 * @ORM\Table(name="meta")
 */
class Meta
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $keywords;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $keywords_overwrite;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $description_overwrite;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $title_overwrite;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $url_overwrite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $data;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Meta
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set keywords_overwrite
     *
     * @param string $keywordsOverwrite
     * @return Meta
     */
    public function setKeywordsOverwrite($keywordsOverwrite)
    {
        $this->keywords_overwrite = $keywordsOverwrite;

        return $this;
    }

    /**
     * Get keywords_overwrite
     *
     * @return string 
     */
    public function getKeywordsOverwrite()
    {
        return $this->keywords_overwrite;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Meta
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description_overwrite
     *
     * @param string $descriptionOverwrite
     * @return Meta
     */
    public function setDescriptionOverwrite($descriptionOverwrite)
    {
        $this->description_overwrite = $descriptionOverwrite;

        return $this;
    }

    /**
     * Get description_overwrite
     *
     * @return string 
     */
    public function getDescriptionOverwrite()
    {
        return $this->description_overwrite;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Meta
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title_overwrite
     *
     * @param string $titleOverwrite
     * @return Meta
     */
    public function setTitleOverwrite($titleOverwrite)
    {
        $this->title_overwrite = $titleOverwrite;

        return $this;
    }

    /**
     * Get title_overwrite
     *
     * @return string 
     */
    public function getTitleOverwrite()
    {
        return $this->title_overwrite;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Meta
     */
    public function setUrl($url)
    {
        $this->url = CommonUri::getUrl($url);

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url_overwrite
     *
     * @param string $urlOverwrite
     * @return Meta
     */
    public function setUrlOverwrite($urlOverwrite)
    {
        $this->url_overwrite = $urlOverwrite;

        return $this;
    }

    /**
     * Get url_overwrite
     *
     * @return string 
     */
    public function getUrlOverwrite()
    {
        return $this->url_overwrite;
    }

    /**
     * Set custom
     *
     * @param string $custom
     * @return Meta
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Get custom
     *
     * @return string 
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return Meta
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }
}
