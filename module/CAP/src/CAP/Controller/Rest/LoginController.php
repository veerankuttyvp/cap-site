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
use Zend\Crypt\Password\Bcrypt;

class LoginController extends AbstractRestfulController {

	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/* GET /login */
	public function getList() {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Rest call to /user" );
	}

	/* POST /login - create is a misnomer its actually the login function */
	public function create( $data ) {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, $data );

    //$bcrypt = new Bcrypt(array('cost' => 10));
    //$logger->log(\Zend\Log\Logger::INFO, $bcrypt->create($data['password']));

		$user = new Customer;
		$messages = null;

		if ( $this->getRequest()->isPost() ) {
			$authService     = $this->getServiceLocator()->get( 'Zend\Authentication\AuthenticationService' );
			$adapter         = $authService->getAdapter();

			try {
				$user = $this->getEntityManager()->createQuery( "SELECT u FROM CAP\Entity\Customer u WHERE u.email = :email" )
				->setParameter('email', $data['email'])
				->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
				$user = $user[0];

				if ( !isset( $user ) ) {
					$message = 'Please enter a valid email and password.';
					return new JsonModel( array( 'login' => false, 'message' => $message ) );
				}

				$adapter->setIdentityValue( $user->getEmail() );
				$adapter->setCredentialValue( $data['password'] );

				$authResult = $authService->authenticate();

				if ( $authResult->isValid() ) {
					$identity = $authResult->getIdentity();
					/* must be active too */
					$logger->log( \Zend\Log\Logger::INFO, "STATUS IS: "+$identity->getStatus()->getName() );
					if ($identity->getStatus()->getName() !== 'ACTIVE') {
						return new JsonModel( array( 'login' => false, 'message' => 'This account is not active.  Please contact the administrator.' ) );
					}


					$authService->getStorage()->write( $identity );

					if ( $data['rememberme'] ) {
						$time = 1209600; // 14 days (1209600/3600 = 336 hours => 336/24 = 14 days)
						$sessionManager = new SessionManager();
						$sessionManager->rememberMe( $time );
					}

					return new JsonModel( array( 'login' => true, 'message' => 'success' ) );
				}

				foreach ( $authResult->getMessages() as $message ) {
					$messages .= "$message\n";
				}
				return new JsonModel( array( 'login' => false, 'message' => $messages ) );
			} catch ( \Exception $e ) {
				return new JsonModel( array( 'login' => false, 'message' => 'Unable to process request.  Please contact your administrator. '. $e->getMessage()) );;
			}

		}
		return new JsonModel( array( 'login' => false, 'message' => 'Please enter a valid email and password.' ) );
	}

  /**
   * get entityManager
   *
   * @return EntityManager
   */
  private function getEntityManager() {
      if (null === $this->entityManager) {
          $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
      }

      return $this->entityManager;
  }


}
