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

class IndexController extends AbstractActionController {

	public function indexAction() {

		/* if they're logged in - redirect to the dashboard */
		if ( $user = $this->identity() ) {
			return $this->redirect()->toRoute( 'dashboard');
		}

		try {
			// ...
			$logger = $this->getServiceLocator()->get( 'Log\App' );
			// ...
		} catch ( \Exception $e ) {
			do {
				var_dump( $e->getMessage() );
			} while ( $e = $e->getPrevious() );
		}

		$logger->log( \Zend\Log\Logger::INFO, "This is a little log!" );


		return new viewModel();
	}
}
