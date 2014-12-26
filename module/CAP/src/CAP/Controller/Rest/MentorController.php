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

class MentorController extends AbstractRestfulController {
  /**
   * @var ModuleOptions
   */
  protected $options;

  /**
   * @var Zend\Mvc\I18n\Translator
   */
  protected $translatorHelper;


  /* will return mentor info for the given id if Admin or one of the mentor's Mentees.  will also return list of mentees if admin */
	public function get( $id ) {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Rest call to GET /mentor/".$id );

		/* must be logged in & must be either admin or a mentee of this mentor */
		if ( !$this->identity() ) {
			return JsonModel( array() );
		}

		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );

		/* if not admin make sure this is a parent of the current logged in customer */
		if ($this->identity()->getRole()->getName() !== "Admin") {
	    $ch = $entityManager->getRepository('CAP\Entity\CustomerHierarchy')
	    										->findOneBy(array('parentCustomer' => $id,
	    																			'childCustomerd' => $this->identity()->getId()));
	    if (!$ch) {
				return new JsonModel();
	    }
	  }

	  /* still here? thatmeans we're either admin or this mentor is our parent mentor */
   	$mentor = $entityManager->getRepository('CAP\Entity\Customer')->find($id);

    if (!$mentor) {
			return new JsonModel();
    }
		$logger->log( \Zend\Log\Logger::INFO, 'mentor is');
		$logger->log( \Zend\Log\Logger::INFO, $mentor->getName());
		/* handle the admin scenario - get list of mentees */
		if ( $this->identity()->getRole()->getName() === 'Admin' ) {
      /* get all mentees for this mentor */
      $sql = "SELECT c.id, c.name FROM CAP\Entity\CustomerHierarchy ch JOIN ch.childCustomer c WHERE ch.parentCustomerId = :parentId";
      $mentees = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )
                      ->setParameter('parentId',$id)
                      ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
		}


		return new JsonModel(array('mentor' => array('id' => $mentor->getId(),
																								 'name' => $mentor->getName(),
																								 'email' => $mentor->getEmail(),
																								 'title' => $mentor->getTitle(),
																								 'status' => $mentor->getStatus()->getName(),
																								 'phoneNumber' => $mentor->getPhoneNumber()
																								 ),
															 'mentees' => $mentees));

	}

	/* will return a list of mentors that belong to the logged in user (or all mentors if Admin) if key is passed in the body it will filter by key */
	public function getList() {

	}

	/* PUT /mentor/:id  - this doesn't modify the mentor (use PUT /customer/:id for that - this assigns a mentee to mentor */
	public function update($id, $data) {
		if ( !$this->identity() || !( $this->identity()->getRole()->getName() === 'Admin' ) ) {
			return JsonModel( array() );
		}


		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, $id);
		$logger->log( \Zend\Log\Logger::INFO, $data);

		$e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $ch = $e->createQuery( "SELECT c FROM CAP\Entity\CustomerHierarchy c WHERE c.childCustomerId = :menteeId" )
            ->setParameter('menteeId',$data['mentee'])
            ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

    if ( $ch ) {
      $ch = $ch[0];
    } else {
      $ch = new \CAP\Entity\CustomerHierarchy;
      $ch->setChildCustomer( $e->find( 'CAP\Entity\Customer', $data['mentee'] ) );
    }
    $ch->setParentCustomer( $e->find( 'CAP\Entity\Customer', $id ) );

    $e->persist( $ch );
    $e->flush();

		return new JsonModel(array('success' => true));
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
