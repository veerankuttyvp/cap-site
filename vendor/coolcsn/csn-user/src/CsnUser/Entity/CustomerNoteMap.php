<?php

namespace CsnUser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerNoteMap
 *
 * @ORM\Table(name="customer_note_map", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="customer_note_id", columns={"customer_note_id"})})
 * @ORM\Entity
 */
class CustomerNoteMap
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
     * @var \cool-csn\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \cool-csn\Entity\CustomerNote
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\CustomerNote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_note_id", referencedColumnName="id")
     * })
     */
    private $customerNote;

     /**
     * Set customer
     *
     * @param  string $customer
     * @return CustomerNoteMap 
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Set customerNote
     *
     * @param  string $customerNote
     * @return CustomerNoteMap 
     */
    public function setCustomerNote($customerNote)
    {
        $this->customerNote = $customerNote;

        return $this;
    }

    /**
     * Set created
     *
     * @param  string $created
     * @return CustomerNote 
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

}
