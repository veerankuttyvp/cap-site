<?php

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerRecommendation
 *
 * @ORM\Table(name="customer_recommendation", indexes={@ORM\Index(name="customer_id", columns={"customer_id"}), @ORM\Index(name="questionnaire_id", columns={"questionnaire_id"}), @ORM\Index(name="recommendation_id", columns={"recommendation_id"})})
 * @ORM\Entity
 */
class CustomerRecommendation {
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
     * @var \CAP\Entity\Recommendation
     *
     * @ORM\ManyToOne(targetEntity="CAP\Entity\Recommendation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recommendation_id", referencedColumnName="id")
     * })
     */
    private $recommendation;


}
