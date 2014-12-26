<?php

namespace CsnUser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerAnswer
 *
 * @ORM\Table(name="customer_answer", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="answer_id", columns={"answer_id"}), @ORM\Index(name="answer_enum_id", columns={"answer_enum_id"})})
 * @ORM\Entity
 */
class CustomerAnswer
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
     * @var stringanswerText
     *
     * @ORM\Column(name="answer_text", type="text", nullable=true)
     */
    private $answerText;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", nullable=false)
     */
    private $modified = 'CURRENT_TIMESTAMP';

    /**
     * @var \CsnUser\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \CsnUser\Entity\Answer
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\Answer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     * })
     */
    private $answer;

    /**
     * @var \CsnUser\Entity\AnswerEnum
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\AnswerEnum")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="answer_enum_id", referencedColumnName="id")
     * })
     */
    private $answerEnum;

     /**
     * Set customer
     *
     * @param  string $customer
     * @return CustomerAnswer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }
    
     /**
     * Set answer
     *
     * @param  string $answer
     * @return CustomerAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Set answerEnum
     *
     * @param  string $answerEnum
     * @return CustomerAnswer
     */
    public function setAnswerEnum($answerEnum)
    {
        $this->answerEnum = $answerEnum;

        return $this;
    }
      
    /**
     * Set answerText
     *
     * @param  string $answerText
     * @return CustomerAnswer
     */
    public function setAnswerText($answerText)
    {
        $this->answerText = $answerText;

        return $this;
    }

     /**
     * Set created
     *
     * @param  string $created
     * @return CustomerAnswer
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
