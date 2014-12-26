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
use CAP\Entity\Customer;
use CAP\Options\ModuleOptions;

class PartialsController extends AbstractActionController {

	public function assignMenteeModalAction() {
		$viewModel = new ViewModel();
    $viewModel->setTerminal(true);
    return $viewModel;
	}

	public function assignSaqModalAction() {
		$viewModel = new ViewModel();
    $viewModel->setTerminal(true);
    return $viewModel;
	}

	public function saqListAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
   	//$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');


		$logger->log( \Zend\Log\Logger::INFO, "partials saqlist action");

		/* if they're not logged in - redirect to login */
		if ( $user = $this->identity() ) {
			$viewModel = new ViewModel();
      $viewModel->setTerminal(true);
      return $viewModel;
		}

	}

	public function mentorsListAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
   	//$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');


		/* if they're not logged in - redirect to login */
		if ( $user = $this->identity() ) {
			$viewModel = new ViewModel();
      $viewModel->setTerminal(true);
      return $viewModel;
		}

	}

	public function menteesListAction() {
		$logger        = $this->getServiceLocator()->get( 'Log\App' );
   	//$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');


		/* if they're not logged in - redirect to login */
		if ( $user = $this->identity() ) {
			$viewModel = new ViewModel();
      $viewModel->setTerminal(true);
      return $viewModel;
		}

	}

}
