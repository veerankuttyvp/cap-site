<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerSection
 *
 * @ORM\Table(name="customer_section", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="section_id", columns={"section_id"}), @ORM\Index(name="completion_status_id", columns={"completion_status_id"})})
 * @ORM\Entity
 */
class CustomerSection {
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
     * @ORM\Column(name="completed",  nullable=true)
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
     * @var \CAP\Entity\Section
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Section")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * })
     */
    private $section;

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
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

     /**
     * Set section
     *
     * @param  string $section
     * @return CustomerQuestion
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

     /**
     * Set completionStatus
     *
     * @param  string $completionStatus
     * @return CustomerQuestion
     */
    public function setCompletionStatus($completionStatus)
    {
        $this->completionStatus = $completionStatus;

        return $this;
    }

     /**
     * Set created
     *
     * @param  string $created
     * @return CustomerQuestion
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

}
