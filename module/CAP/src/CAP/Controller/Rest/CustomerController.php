<?php
namespace CAP\Controller\Rest;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\SessionManager;
use Zend\Session\Config\StandardConfig;
use Zend\View\Model\JsonModel;
use CAP\Entity\Customer;
use CAP\Options\ModuleOptions;
use Zend\Mail\Message;
use CAP\Service\UserService;

class CustomerController extends AbstractRestfulController {
  /**
   * @var ModuleOptions
   */
  protected $options;

  /**
   * @var Zend\Mvc\I18n\Translator
   */
  protected $translatorHelper;

  public function update( $id, $data ) {
    $logger = $this->getServiceLocator()->get( 'Log\App' );
    $logger->log( \Zend\Log\Logger::INFO, "Rest call to PUT /customer/".$id );
    $logger->log( \Zend\Log\Logger::INFO, $data );
    /* only admins can modify a customer */
    if ( !$this->identity() || !( $this->identity()->getRole()->getName() === 'Admin' ) ) {
      return new JsonModel( array('success' => false) );
    }

    /* must have a cust id to update */
    if (!$id) {
      return new JsonModel( array('success' => false) );
    }

    $e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $c = $e->find("CAP\Entity\Customer", $id);

    /* if there's no existing customer then bail */
    if ( !$c ) {
      return JsonModel( array('success' => false) );
    }

    if ($data['status']) {
      /* get a status obj for this name */
      $cs = $e->getRepository("CAP\Entity\CustomerStatus")->findOneBy(array('name' => $data['status']));

      if (!$cs->getId()) {
        return JsonModel( array('success' => false, 'message' => 'invalid status') );
      }

      $c->setStatus( $cs );

      /* if status is being set to INACTIVE or DELETED, destory the customer hierarchy records */
      if ($data['status'] !== "ACTIVE") {
        $chs = $e->getRepository('CAP\Entity\CustomerHierarchy')->findBy(array('parentCustomer' => $id));
        foreach ($chs as $ch) {
          $logger->log( \Zend\Log\Logger::INFO, "remove customer hierarchy for ".$id." and ".$ch->getId() );
          $e->remove($ch);
          $e->flush();
        }
      }
    }

    $e->persist( $c );
    $e->flush();

    return new JsonModel( array('success' => true) );
  }

	public function get( $id ) {
    $logger = $this->getServiceLocator()->get( 'Log\App' );
    $logger->log( \Zend\Log\Logger::INFO, "Rest call to GET /customer/".$id );
		if ( $id == 'current' ) {
			$e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
			$hydrator = new \DoctrineModule\Stdlib\Hydrator\DoctrineObject( $e );
			return new JsonModel( $hydrator->extract( $this->identity() ) );
		}

	}

	public function getList() {
		if ( !$this->identity() || !( $this->identity()->getRole()->getName() === 'Admin' ) ) {
			return JsonModel( array() );
		}

		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Rest call to /customer" );

		/* this serves as the get call for the create account page */
		/* get the possible roles */
    $e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $r = $e->createQuery( "SELECT r FROM CAP\Entity\Role r" )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

		$results = array();

		foreach ($r as $role) {

			array_push($results, array('id' => $role->getId(),
																 'name'=> $role->getName()));
		}
		return new JsonModel(array('roles' => $results));
	}

	/* POST /customer - should create a new customer */
	public function create( $data ) {
    if ( !$this->identity() || !( $this->identity()->getRole()->getName() === 'Admin' ) ) {
      return JsonModel( array() );
    }

		/* TODO Validate $data */
		if (!$data['email']) {
			return new JsonModel(array('status' => false, 'error' => 'Email is required'));
		}


		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, $data );

    $entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $c = $entityManager->createQuery( "SELECT u FROM CAP\Entity\Customer u WHERE u.email = :email" )
      ->setParameter('email', $data['email'])
      ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

    /* if there's no existing customer then its ok to create one */
    if ( !$c ) {

			$customer = new Customer;
			/* TODO : make domain a global */
			$customer->setDomain( $entityManager->find( 'CAP\Entity\Domain', 3 ) );
			$customer->setRole(   $entityManager->find( 'CAP\Entity\Role', $data['role_id'] ) );

      $customer->setStatus( $entityManager->find( 'CAP\Entity\CustomerStatus', 2 ) );
			$customer->setName( $data['name'] );
			$customer->setEmail( $data['email'] );
			$customer->setRegistrationToken( md5( uniqid( mt_rand(), true ) ) );
			$customer->setPassword( UserService::encryptPassword( $this->generatePassword() ) );

			try {
				$fullLink = $this->getBaseUrl() . $this->url()->fromRoute( 'dashboard', array( 'action' => 'confirm-email', 'id' => $customer->getRegistrationToken() ) );
				$this->sendEmail(
					$customer->getEmail(),
					$this->getTranslatorHelper()->translate( 'Please, confirm your registration!' ),
					sprintf( $this->getTranslatorHelper()->translate( 'Please, click the link to confirm your registration => %s' ), $fullLink )
				);
        $entityManager->persist( $customer );
        $entityManager->flush();

				return new JsonModel( array( 'success' => true, 'message' => 'An email has been sent to '.$customer->getEmail() ) );
			} catch ( \Exception $e ) {
				$logger->log( \Zend\Log\Logger::INFO, $e );
				return new JsonModel( array( 'success' => false, 'message' =>'Something went wrong when trying to send the activation email! Please contact your administrator.' ) );
			}
		} else {
			return new JsonModel( array( 'success' => false, 'message' =>'This email is already registered' ) );
		}
	}

  /**
   * Generate Password
   *
   * Generates random password
   *
   * @return String
   */
  private function generatePassword($l = 8, $c = 0, $n = 0, $s = 0) {
      $count = $c + $n + $s;
      $out = '';
      if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
          trigger_error('Argument(s) not an integer', E_USER_WARNING);
          return false;
      } else if($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
          trigger_error('Argument(s) out of range', E_USER_WARNING);
          return false;
      } else if($c > $l) {
          trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
          return false;
      } else if($n > $l) {
          trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
          return false;
      } else if($s > $l) {
          trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
          return false;
      } else if($count > $l) {
          trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
          return false;
      }

      $chars = "abcdefghijklmnopqrstuvwxyz";
      $caps = strtoupper($chars);
      $nums = "0123456789";
      $syms = "!@#$%^&*()-+?";

      for ($i = 0; $i < $l; $i++) {
          $out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
      }

      if($count) {
          $tmp1 = str_split($out);
          $tmp2 = array();

          for ($i = 0; $i < $c; $i++) {
              array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
          }

          for ($i = 0; $i < $n; $i++) {
              array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
          }

          for ($i = 0; $i < $s; $i++) {
              array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
          }

          $tmp1 = array_slice($tmp1, 0, $l - $count);
          $tmp1 = array_merge($tmp1, $tmp2);
          shuffle($tmp1);
          $out = implode('', $tmp1);
      }

      return $out;
  }

  /**
   * Send Email
   *
   * Sends plain text emails
   *
   */
  private function sendEmail($to = '', $subject = '', $messageText = '') {
      $transport = $this->getServiceLocator()->get('mail.transport');
      $message = new Message();

      $message->addTo($to)
              ->addFrom($this->getOptions()->getSenderEmailAdress())
              ->setSubject($subject)
              ->setBody($messageText);

      $transport->send($message);
  }


  /**
   * Get Base Url
   *
   * Get Base App Url
   *
   */
  private function getBaseUrl() {
      $uri = $this->getRequest()->getUri();
      return sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
  }

  /**
   * get translatorHelper
   *
   * @return  Zend\Mvc\I18n\Translator
   */
  private function getTranslatorHelper() {
      if(null === $this->translatorHelper) {
          $this->translatorHelper = $this->getServiceLocator()->get('MvcTranslator');
      }

      return $this->translatorHelper;
  }

  /**
   * get options
   *
   * @return ModuleOptions
   */
  private function getOptions()
  {
      if(null === $this->options) {
          $this->options = $this->getServiceLocator()->get('cap_module_options');
      }

      return $this->options;
  }



}
