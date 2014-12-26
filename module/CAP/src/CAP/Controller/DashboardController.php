<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CAP\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
use Zend\Session\Config\StandardConfig;
use Zend\View\Model\JsonModel;
use CAP\Entity\Customer;
use CAP\Options\ModuleOptions;

class DashboardController extends AbstractActionController {

	public function indexAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
		//$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');


		$logger->log( \Zend\Log\Logger::INFO, "Dashboard index action" );
		$u = $this->identity();
		$logger->log( \Zend\Log\Logger::INFO, $u->getRole()->getName() );

		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}



		/* display the appropriate view depending on the role of the logged in user */


		return new viewModel();
	}


	public function saqAction() {
    $id = $this->params()->fromRoute('id');
		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}
		$viewModel = new ViewModel();
		return $viewModel->setTemplate('cap/dashboard/detail/saq.phtml');
	}

	/* view mentor detail page */
	public function mentorAction() {
    $id = $this->params()->fromRoute('id');
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "display mentor detail for ".$id );

		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}

		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );

		/* if not admin make sure this is a parent of the current logged in customer */
		if ($this->identity()->getRole()->getName() !== "Admin") {
	    $ch = $entityManager->getRepository('CAP\Entity\CustomerHierarchy')->findOneBy(array('parentCustomer' => $id, 'childCustomer' => $this->identity()->getId()));
	    if (!$ch) {
				return $this->redirect()->toRoute('dashboard');
	    }
	  }

   	$mentor = $entityManager->getRepository('CAP\Entity\Customer')->find($id);

    if (!$mentor) {
			return $this->redirect()->toRoute('dashboard');
    }

		$viewModel = new ViewModel(array(
			'mentor' => $mentor,
		));
		return $viewModel->setTemplate('cap/dashboard/detail/mentor.phtml');
	}

	/* view mentee detail page */
	public function menteeAction() {
    $id = $this->params()->fromRoute('id');
		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}

		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );

		/* if not admin make sure this is a child of the current logged in customer */
		if ($this->identity()->getRole()->getName() !== "Admin") {
	    $ch = $entityManager->getRepository('CAP\Entity\CustomerHierarchy')->findOneBy(array('childCustomer' => $id, 'parentCustomer' => $this->identity()->getId()));
	    if (!$ch) {
				return $this->redirect()->toRoute('dashboard');
	    }
	  }

   	$mentee = $entityManager->getRepository('CAP\Entity\Customer')->find($id);

    if (!$mentee) {
			return $this->redirect()->toRoute('dashboard');
    }

		$viewModel = new ViewModel(array(
			'mentee' => $mentee,
		));

		return $viewModel->setTemplate('cap/dashboard/detail/mentee.phtml');
	}

	public function adminAction() {
    $id = $this->params()->fromRoute('id');
		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}
		if ( $this->identity()->getRole()->getName() !=='Admin' ) {
			return $this->redirect()->toRoute( 'home' );
		}

		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
   	$admin = $entityManager->getRepository('CAP\Entity\Customer')->find($id);

    if (!$admin) {
			return $this->redirect()->toRoute('dashboard');
    }

		$viewModel = new ViewModel(array(
			'admin' => $admin
		));

		return $viewModel->setTemplate('cap/dashboard/detail/admin.phtml');
	}

	public function createAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
		//$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');


		$logger->log( \Zend\Log\Logger::INFO, "Display dashboard create form" );
		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}

		/* if they're not admin in - redirect to dashboard */
		if ( $this->identity()->getRole()->getName() !== 'Admin' ) {
			return $this->redirect()->toRoute( 'dashboard' );
		}
		return new viewModel();
	}

	public function confirmEmailAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Display confirm email form" );

		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $token = $this->params()->fromRoute('id');
    $c = $entityManager->getRepository('CAP\Entity\Customer')->findOneBy(array('registrationToken' => $token));

    try {
			//TODO: index token on customer table
      if($token && $c) {
        $viewModel = new ViewModel(array(
            'token' => $c->getRegistrationToken(),
        ));
        return $viewModel;

      } else {
        return $this->redirect()->toRoute('home');
      }
    } catch (\Exception $e) {
    }
		return new viewModel();
	}

	public function settingsAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
		//$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');


		$logger->log( \Zend\Log\Logger::INFO, "Dashboard settings action" );
		/* if they're not logged in - redirect to login */
		if ( !$user = $this->identity() ) {
			return $this->redirect()->toRoute( 'home' );
		}

		/* if they're not admin in - redirect to dashboard */
		if ( $this->identity()->getRole()->getName() !== 'Admin' ) {
			return $this->redirect()->toRoute( 'dashboard' );
		}
		return new viewModel();
	}


	public function logoutAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "logout" );

		$auth = $this->getServiceLocator()->get( 'Zend\Authentication\AuthenticationService' );
		if ( $auth->hasIdentity() ) {
			$auth->clearIdentity();
			$sessionManager = new SessionManager();
			$sessionManager->forgetMe();
		}

		return $this->redirect()->toRoute( 'home', array( 'action' =>  'index' ) );
	}

}
