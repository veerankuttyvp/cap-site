<?php

namespace CsnUser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="questionnaire_id", columns={"questionnaire_id"}), @ORM\Index(name="section_id", columns={"section_id"})})
 * @ORM\Entity
 */
class Question
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_number", type="integer", nullable=false)
     */
    private $questionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="question_text", type="text", nullable=false)
     */
    private $questionText;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_order", type="integer", nullable=false)
     */
    private $questionOrder = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified = 'CURRENT_TIMESTAMP';

    /**
     * @var \CsnUser\Entity\Questionnaire
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\Questionnaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id")
     * })
     */
    private $questionnaire;

    /**
     * @var \CsnUser\Entity\Section
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\Section")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * })
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(name="section_id", type="integer", length=255, nullable=true)
     */

    private $sectionid;

     /**
     * @var string
     *
     * @ORM\Column(name="questionnaire_id", type="integer", length=255, nullable=true)
     */

    private $questionnaireid;

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
     * Get sectionid
     *
     * @return sectionid
     */
    public function getSectionid()
    {
        return $this->sectionid;
    }

    /**
     * Get questionnaireid
     *
     * @return questionnaireid
     */
    public function getQuestionnaireid()
    {
        return $this->questionnaireid;
    }

    /**
     * Get questionOrder
     *
     * @return QuestionOrder
     */
    public function getQuestionOrder()
    {
        return $this->questionOrder;
    }
}
