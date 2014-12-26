<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CAP\Controller\Rest;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\SessionManager;
use Zend\Session\Config\StandardConfig;
use Zend\View\Model\JsonModel;
use CAP\Entity\Customer;
use CAP\Options\ModuleOptions;
use Zend\Mail\Message;
use CAP\Service\UserService;

class PasswordController extends AbstractRestfulController {

	/* POST /password - set the password for the customer with this token */
	public function create( $data ) {
		if ( ( $data['password'] !== '' ) && ( $data['password'] == $data['password2'] ) ) {
			$token         = $data['token'];
			$entityManager = $this->getEntityManager();
			$customer      = $entityManager->getRepository( 'CAP\Entity\Customer' )->findOneBy( array( 'registrationToken' => $token ) );

			if ( $token !== '' && $customer ) {

				$customer->setPassword( UserService::encryptPassword( $data['password'] ) );
				$customer->setRegistrationToken( md5( uniqid( mt_rand(), true ) ) );
				$customer->setStatus( $entityManager->find( 'CAP\Entity\CustomerStatus', 1 ) );
				$entityManager->persist( $customer );
				$entityManager->flush();

				/* auth this person */

				$authService     = $this->getServiceLocator()->get( 'Zend\Authentication\AuthenticationService' );
				$adapter         = $authService->getAdapter();
				$adapter->setIdentityValue( $customer->getEmail() );
				$adapter->setCredentialValue( $data['password'] );
				$authResult = $authService->authenticate();

				if ( $authResult->isValid() ) {
					$identity = $authResult->getIdentity();
					$authService->getStorage()->write( $identity );
					return new JsonModel( array( 'success' => true ) );
				} else {
					return new JsonModel( array( 'success' => false ) );
				}
			}
		}
		return new JsonModel( array( 'success' => false ) );
	}

	/**
	 * get entityManager
	 *
	 * @return EntityManager
	 */
	private function getEntityManager() {
		if ( null === $this->entityManager ) {
			$this->entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
		}

		return $this->entityManager;
	}


}
