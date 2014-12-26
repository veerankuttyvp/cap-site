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

class MenteeController extends AbstractRestfulController {
  /**
   * @var ModuleOptions
   */
  protected $options;

  /**
   * @var Zend\Mvc\I18n\Translator
   */
  protected $translatorHelper;


  /* will return saq info for the given id if Admin or one of the saq's mentees.  will also return list of saqs if admin */
	public function get( $id ) {
    $logger = $this->getServiceLocator()->get( 'Log\App' );
    $logger->log( \Zend\Log\Logger::INFO, "Rest call to GET /saq/".$id );

    /* must be logged in & must be either admin or a mentor to this mentee */
    if ( !$this->identity() ) {
      return JsonModel( array() );
    }

    $entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );

    /* if not admin make sure this is a parent of the current logged in customer */
    if ($this->identity()->getRole()->getName() !== "Admin") {
      $ch = $entityManager->getRepository('CAP\Entity\CustomerHierarchy')
                          ->findOneBy(array('childCustomer' => $id,
                                            'parentCustomer' => $this->identity()->getId()));
      if (!$ch) {
        return new JsonModel();
      }

    }

    /* still here? that means we're either admin or this mentee is the logged in mentor's mentee */
    $mentee = $entityManager->getRepository('CAP\Entity\Customer')->find($id);

    if (!$mentee) {
      return new JsonModel();
    }

    $mentors = null;

    /* handle the admin scenario - get list of saqs */
    if ( $this->identity()->getRole()->getName() === 'Admin' ) {

      /* get all mentees for this mentor */
      $sql = "SELECT c.id, c.name, s.name as status FROM CAP\Entity\CustomerHierarchy ch JOIN ch.parentCustomer c JOIN c.status s WHERE ch.childCustomerId = :childId";
      $mentors = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )
                      ->setParameter('childId',$id)
                      ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

      /* get all notes belonging to this mentee */
      $notes = $entityManager->createQuery("SELECT n.customerId, n.name, n.note, n.created, n.id FROM CAP\Entity\CustomerNote n  WHERE n.customer = :menteeId")
                             ->setParameter('menteeId', $id)
                             ->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }

    if ( $this->identity()->getRole()->getName() === 'Mentor') {

      /* get mentors notes on this mentee */
      $myNotes = $entityManager->createQuery("SELECT cn.id, cn.customerId, cn.note, cn.name, cn.id, cn.created, nm.share FROM CAP\Entity\CustomerNoteMap nm JOIN nm.customerNote cn WHERE cn.customer = :mentorId and nm.customer = :menteeId")
                               ->setParameter('menteeId', $id)
                               ->setParameter('mentorId', $this->identity()->getId())
                               ->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      /* get mentee notes shared with this mentor */
      $sharedNotes = $entityManager->createQuery("SELECT cn.id, cn.customerId, cn.note, cn.name, cn.id, cn.created, nm.share FROM CAP\Entity\CustomerNoteMap nm JOIN nm.customerNote cn WHERE cn.customer = :menteeId and nm.customer = :mentorId and nm.share = TRUE")
                                   ->setParameter('menteeId', $id)
                                   ->setParameter('mentorId', $this->identity()->getId())
                                   ->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }

    /* get all saqs for this mentee */
    $saqs = $entityManager->createQuery( "SELECT q.id, c.id as questionnaire_id, c.name, cs.name as completion_status FROM CAP\Entity\CustomerQuestionnaire q JOIN q.questionnaire c JOIN q.completionStatus cs where q.customer = :customerId" )
                          ->setParameter('customerId', $id)
                          ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );


    /* get all notes for this mentee
      - admin can see all notes
      - mentor can only see shared notes
    */




    return new JsonModel(array('mentee' => array('id' => $mentee->getId(),
                                                 'name' => $mentee->getName(),
                                                 'email' => $mentee->getEmail(),
                                                 'title' => $mentee->getTitle(),
                                                 'status' => $mentee->getStatus()->getName(),
                                                 'phoneNumber' => $mentee->getPhoneNumber()
                                                 ),
                               'myNotes' => $myNotes,
                               'sharedNotes' => $sharedNotes,
                               'saqs' => $saqs,
                               'mentors' => $mentors));

	}

	/* will return a list of mentees that belong to the logged in user (or all mentees if Admin) */
	public function getList() {

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
