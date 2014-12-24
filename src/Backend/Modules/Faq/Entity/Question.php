<?php

namespace Backend\Modules\Faq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Backend\Core\Entity\Meta;

/**
 * This is the Faq Question Entity
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *     name="FaqQuestion",
 *     indexes={@ORM\Index(
 *         name="fk_faq_questions_faq_categories",
 *         columns={"isHidden", "language"})
 *     }
 * )
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="questions")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     **/
    private $category;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="question")
     */
    private $feedback;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\OneToOne(targetEntity="Backend\Core\Entity\Meta")
     * @ORM\JoinColumn(name="meta_id", referencedColumnName="id")
     **/
    private $meta;

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
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $answer;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numViews = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numUsefullYes = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numUsefullNo = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isHidden = false;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $sequence;

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
     * Constructor
     */
    public function __construct()
    {
        $this->feedback = new ArrayCollection();
    }

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
     * Set category
     *
     * @param Category $category
     * @return Question
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add feedback
     *
     * @param Feedback $feedback
     * @return Question
     */
    public function addFeedback(Feedback $feedback)
    {
        $this->feedback[] = $feedback;

        return $this;
    }

    /**
     * Remove feedback
     *
     * @param Feedback $feedback
     */
    public function removeFeedback(Feedback $feedback)
    {
        $this->feedback->removeElement($feedback);
    }

    /**
     * Get feedback
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set meta
     *
     * @param Meta $meta
     * @return Category
     */
    public function setMeta(Meta $meta = null)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set extraId
     *
     * @param integer $extraId
     * @return Category
     */
    public function setExtraId($extraId)
    {
        $this->extraId = $extraId;

        return $this;
    }

    /**
     * Get extraId
     *
     * @return integer
     */
    public function getExtraId()
    {
        return $this->extraId;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Category
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Category
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
     * Set userId
     *
     * @param integer $userId
     * @return Question
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set question
     *
     * @param string $question
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answer
     *
     * @param string $answer
     * @return Question
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set numViews
     *
     * @param integer $numViews
     * @return Question
     */
    public function setNumViews($numViews)
    {
        $this->numViews = $numViews;

        return $this;
    }

    /**
     * Get numViews
     *
     * @return integer
     */
    public function getNumViews()
    {
        return $this->numViews;
    }

    /**
     * Set numUsefullYes
     *
     * @param integer $numUsefullYes
     * @return Question
     */
    public function setNumUsefullYes($numUsefullYes)
    {
        $this->numUsefullYes = $numUsefullYes;

        return $this;
    }

    /**
     * Get numUsefullYes
     *
     * @return integer
     */
    public function getNumUsefullYes()
    {
        return $this->numUsefullYes;
    }

    /**
     * Set numUsefullNo
     *
     * @param integer $numUsefullNo
     * @return Question
     */
    public function setNumUsefullNo($numUsefullNo)
    {
        $this->numUsefullNo = $numUsefullNo;

        return $this;
    }

    /**
     * Get numUsefullNo
     *
     * @return integer
     */
    public function getNumUsefullNo()
    {
        return $this->numUsefullNo;
    }

    /**
     * Set isHidden
     *
     * @param boolean $isHidden
     * @return Question
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
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

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return Category
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Category
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set editedOn
     *
     * @param \DateTime $editedOn
     * @return Category
     */
    public function setEditedOn($editedOn)
    {
        $this->editedOn = $editedOn;

        return $this;
    }

    /**
     * Get editedOn
     *
     * @return \DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }
}
