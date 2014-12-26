<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerHierarchy
 *
 * @ORM\Table(name="customer_hierarchy", uniqueConstraints={@ORM\UniqueConstraint(name="parent_customer_id", columns={"parent_customer_id"}), @ORM\UniqueConstraint(name="child_customer_id", columns={"child_customer_id"})})
 * @ORM\Entity
 */
class CustomerHierarchy {
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
     * @ORM\Column(name="child_customer_id", type="integer", length=255, nullable=true)
     */
    protected $childCustomerId;

    /**
     * @var string
     *
     * @ORM\Column(name="parent_customer_id", type="integer", length=255, nullable=true)
     */
    protected $parentCustomerId;


    /**
     * @var \CAP\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_customer_id", referencedColumnName="id")
     * })
     */
    private $parentCustomer;

    /**
     * @var \CAP\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="child_customer_id", referencedColumnName="id")
     * })
     */
    private $childCustomer;

    /**
     * Set parentCustomer
     *
     * @param  string $parentCustomer
     * @return CustomerHierarchy
     */
    public function setParentCustomer($parentCustomer) {
        $this->parentCustomer = $parentCustomer;

        return $this;
    }

   /**
     * Set childCustomer
     *
     * @param  string $childCustomer
     * @return CustomerHierarchy
     */
    public function setChildCustomer($childCustomer) {
        $this->childCustomer = $childCustomer;

        return $this;
    }

    public function getChildCustomer() {
        return $this->childCustomer;
    }
    public function getId() {
        return $this->id;
    }

}
