<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerQuestion
 *
 * @ORM\Table(name="customer_question", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="question_id", columns={"question_id"}), @ORM\Index(name="completion_status_id", columns={"completion_status_id"})})
 * @ORM\Entity
 */
class CustomerQuestion {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed", nullable=true)
     */
    private $completed;

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
     * @var \CAP\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \CAP\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * @var \CAP\Entity\CompletionStatus
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\CompletionStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="completion_status_id", referencedColumnName="id")
     * })
     */
    private $completionStatus;

       /**
     * Set customer
     *
     * @param  string $customer
     * @return CustomerQuestion
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

     /**
     * Set question
     *
     * @param  string $question
     * @return CustomerQuestion
     */
    public function setQuestion($question) {
        $this->question = $question;

        return $this;
    }

     /**
     * Set completionStatus
     *
     * @param  string $completionStatus
     * @return CustomerQuestion
     */
    public function setCompletionStatus($completionStatus) {
        $this->completionStatus = $completionStatus;

        return $this;
    }

     /**
     * Set created
     *
     * @param  string $created
     * @return CustomerQuestion
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }



}
