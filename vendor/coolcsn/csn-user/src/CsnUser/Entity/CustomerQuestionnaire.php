<?php

namespace CsnUser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerQuestionnaire
 *
 * @ORM\Table(name="customer_questionnaire", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="questionnaire_id", columns={"questionnaire_id"}), @ORM\Index(name="completion_status_id", columns={"completion_status_id"})})
 * @ORM\Entity
 */
class CustomerQuestionnaire
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
     * @var \CsnUser\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

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
     * @var \CsnUser\Entity\CompletionStatus
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\CompletionStatus")
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
     protected $customerid;

     /**
     * @var string
     *
     * @ORM\Column(name="questionnaire_id", type="integer", length=255, nullable=true)
     */
     protected $questionnaireid;

     /**
     * Get customerid
     *
     * @return boolean
     */
     public function getcustomerid()
     {
        return $this->customerid;
     }

    /**
     * Get CustomerQuestionnaire
     *
     * @return boolean
     */
    public function getquestionnaireid()
    {
        return $this->questionnaireid;
    }
 
     /**
     * Set customer
     *
     * @param  customer $customer
     * @return CustomerQuestionnaire
     */
    public function setcustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Set questionnaire
     *
     * @param  questionnaire $questionnaire
     * @return CustomerQuestionnaire
     */
    public function setquestionnaire($questionnaire)
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }
    /**
     * Set completionStatus
     *
     * @param completionStatus $completionStatus
     * @return CustomerQuestionnaire
     */
    public function setcompletionStatus($completionStatus)
    {
        $this->completionStatus = $completionStatus;

        return $this;
    }
}
