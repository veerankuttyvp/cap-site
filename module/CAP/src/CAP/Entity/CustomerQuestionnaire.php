<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerQuestionnaire
 *
 * @ORM\Table(name="customer_questionnaire", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="questionnaire_id", columns={"questionnaire_id"}), @ORM\Index(name="completion_status_id", columns={"completion_status_id"})})
 * @ORM\Entity
 */
class CustomerQuestionnaire {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \CAP\Entity\Questionnaire
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Questionnaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id")
     * })
     */
    private $questionnaire;

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
     * @var string
     *
     * @ORM\Column(name="customer_id", type="integer", length=255, nullable=true)
     */
     protected $customerId;

     /**
     * @var string
     *
     * @ORM\Column(name="questionnaire_id", type="integer", length=255, nullable=true)
     */
     protected $questionnaireId;

     /**
     * Get customerid
     *
     * @return boolean
     */
     public function getCustomerId() {
        return $this->customerId;
     }
     public function getCustomer() {
        return $this->customer;
     }

    /**
     * Get CustomerQuestionnaire
     *
     * @return boolean
     */
    public function getQuestionnaireId() {
        return $this->questionnaireId;
    }

    public function getQuestionnaire() {
        return $this->questionnaire;
    }

     /**
     * Set customer
     *
     * @param  customer $customer
     * @return CustomerQuestionnaire
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Set questionnaire
     *
     * @param  questionnaire $questionnaire
     * @return CustomerQuestionnaire
     */
    public function setQuestionnaire($questionnaire) {
        $this->questionnaire = $questionnaire;

        return $this;
    }
    /**
     * Set completionStatus
     *
     * @param completionStatus $completionStatus
     * @return CustomerQuestionnaire
     */
    public function setCompletionStatus($completionStatus) {
        $this->completionStatus = $completionStatus;

        return $this;
    }
}
