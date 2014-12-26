<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answer", indexes={@ORM\Index(name="question_id", columns={"question_id"})})
 * @ORM\Entity
 */
class Answer {
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
     * @ORM\Column(name="answer_number", type="integer", nullable=false)
     */
    private $answerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_text", type="text", nullable=false)
     */
    private $answerText;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_type", type="string", nullable=true)
     */
    private $answerType = 'TEXT';

    /**
     * @var integer
     *
     * @ORM\Column(name="answer_order", type="integer", nullable=false)
     */
    private $answerOrder = '0';

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
     * @var \CAP\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;


}
