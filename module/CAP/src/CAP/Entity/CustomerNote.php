<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerNote
 *
 * @ORM\Table(name="customer_note", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="questionnaire_id", columns={"questionnaire_id"})})
 * @ORM\Entity
 */
class CustomerNote {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=false)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", nullable=false)
     */
    private $modified = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="customer_id", type="integer", length=255, nullable=true)
     */
    protected $customerId;

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
     * @var \cool-csn\Entity\Questionnaire
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Questionnaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id")
     * })
     */
    private $questionnaire;


     /**
     * Get id
     *
     * @return integer
     */
     public function getId() {
        return $this->id;
     }
    /**
     * Set note
     *
     * @param  string $note
     * @return CustomerNote
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    public function getNote() {
        return $this->note;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getName() {
        return $this->name;
    }

    public function getCustomerId() {
        return $this->customerId;
    }

   /**
     * Set customer
     *
     * @param  string $customer
     * @return CustomerNote
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

}
