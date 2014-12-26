<?php

namespace CAP\Controller\Rest;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message;
use Zend\Validator\Identical as IdenticalValidator;
use Zend\View\Model\JsonModel;
use CAP\Entity\ReminderFrequency;
use CAP\Options\ModuleOptions;
use CAP\Service\UserService as UserCredentialsService;

/**
 * Reminderfrequency controller
 */
class SettingsController extends AbstractRestfulController {

    public function getList() {
        if ( !$this->identity() || !($this->identity()->getRole()->getName() === 'Admin') ) {
            return JsonModel(array());
        }


        $entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );

        /* get all the settings - right now its just reminder frequency */
        $rm = $entityManager
        ->createQuery( "SELECT rf FROM CAP\Entity\ReminderFrequency rf ORDER BY rf.id" )
        ->setMaxResults( 1 )
        ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

        return new JsonModel(
            array( 'settings' => array(
                    'reminderFrequency' => array(
                        'frequency' => $rm[0]->getFrequency(),
                    ),
                )
            )

        );

    }

    public function create( $data ) {
        /* save the reminder frequency */
        if ( !$this->identity() || !($this->identity()->getRole()->getName() === 'Admin') ) {
            return JsonModel(array());
        }

        $entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
        $rf = $entityManager->createQuery( "SELECT u FROM CAP\Entity\ReminderFrequency u WHERE u.id = '1'" )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
        $rf = $rf[0];
        $rf->setFrequency( $data['reminderFrequency']['frequency'] );
        $entityManager->persist( $rf );
        $entityManager->flush();

        return new JsonModel(
            array( 'success' => true )
        );
    }
}
