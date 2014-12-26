<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerNoteMap
 *
 * @ORM\Table(name="customer_note_map", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="customer_note_id", columns={"customer_note_id"})})
 * @ORM\Entity
 */
class CustomerNoteMap {
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
     * @ORM\Column(name="created", nullable=true)
     */
    private $created;

    /**
     * @var \share
     *
     * @ORM\Column(name="share", type="boolean", nullable=false)
     */
    private $share;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", nullable=false)
     */
    private $modified = 'CURRENT_TIMESTAMP';

    /**
     * @var \cool-csn\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \cool-csn\Entity\CustomerNote
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\CustomerNote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_note_id", referencedColumnName="id")
     * })
     */
    private $customerNote;

    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }


     /**
     * Set customer
     *
     * @param  string $customer
     * @return CustomerNoteMap
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Set customerNote
     *
     * @param  string $customerNote
     * @return CustomerNoteMap
     */
    public function setCustomerNote($customerNote) {
        $this->customerNote = $customerNote;

        return $this;
    }


    public function getShare() {
        return $this->share;
    }

    public function setShare($bool) {
        $this->share = $bool;
        return $this;
    }

}
