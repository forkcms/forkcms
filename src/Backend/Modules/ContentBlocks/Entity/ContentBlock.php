<?php

namespace Backend\Modules\ContentBlocks\Entity;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the ContentBlock Entity
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ContentBlock
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $revisionId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $extraId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isHidden = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedOn;

    /**
     * Gets the value of revisionId.
     *
     * @return int
     */
    public function getRevisionId()
    {
        return $this->revisionId;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param  int  $id the id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the value of userId.
     *
     * @param int $userId the user id
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Gets the value of extraId.
     *
     * @return int
     */
    public function getExtraId()
    {
        return $this->extraId;
    }

    /**
     * Sets the value of extraId.
     *
     * @param int $extraId the extra id
     * @return self
     */
    public function setExtraId($extraId)
    {
        $this->extraId = $extraId;

        return $this;
    }

    /**
     * Gets the value of template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the value of template.
     *
     * @param string $template the template
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Gets the value of language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param string $language the language
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the value of text.
     *
     * @param string $text the text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Gets the value of isHidden.
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Sets the value of isHidden.
     *
     * @param boolean $isHidden the is hidden
     * @return self
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param string $status the status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets the value of createdOn.
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Sets the value of createdOn.
     *
     * @param \DateTime $createdOn the created on
     * @return self
     */
    public function setCreatedOn(\DateTime $createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Gets the value of editedOn.
     *
     * @return \DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * Sets the value of editedOn.
     *
     * @param \DateTime $editedOn the edited on
     * @return self
     */
    public function setEditedOn(\DateTime $editedOn)
    {
        $this->editedOn = $editedOn;

        return $this;
    }

    /**
     *  @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdOn = $this->editedOn = new \Datetime();
    }

    /**
     *  @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->editedOn = new \Datetime();
    }
}
