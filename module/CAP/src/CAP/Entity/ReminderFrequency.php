<?php
/**
 * CAP - Coolcsn Zend Framework 2 User Module
 *
 * @link https://github.com/coolcsn/CAP for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CAP/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */

namespace CAP\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ReminderFrequency
 *
 * @ORM\Table(name="reminder_frequency")
 * @ORM\Entity
 */

class ReminderFrequency {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="frequency", type="string", columnDefinition="ENUM('DAILY','WEEKLY','MONTHLY','QUARTERLY')")
     */
    protected $frequency;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set frequency
     *
     * @param  string $frequency
     * @return ReminderFrequency
     */
    public function setFrequency($frequency) {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get frequency
     *
     * @return string
     */
    public function getFrequency() {
        return $this->frequency;
    }

}
