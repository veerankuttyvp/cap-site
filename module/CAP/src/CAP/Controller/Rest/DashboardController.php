<?php
namespace CAP\Controller\Rest;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\SessionManager;
use Zend\Session\Config\StandardConfig;
use Zend\View\Model\JsonModel;
use CAP\Entity\Customer;
use CAP\Options\ModuleOptions;
use Doctrine\ORM\Query\ResultSetMapping;

class DashboardController extends AbstractRestfulController {

	public function get( $id ) {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Rest call to GET /user/".$id );
		if ( $id == 'current' ) {
			$e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
			$hydrator = new \DoctrineModule\Stdlib\Hydrator\DoctrineObject( $e );
			return new JsonModel( $hydrator->extract( $this->identity() ) );
		}
	}

	public function getList() {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Rest call to /dashboard" );
		//$logger->log( \Zend\Log\Logger::INFO, "Role is ".$this->identity()->getRole() );

		/* fetch the data for the dashboard depending on who is logged in */
		if ( $this->identity()->getRole()->getName() == "Admin" ) {
			/* get every mentee and their mentor */


			/* doctrine can't handle foreign keys that are not primary keys
				http://stackoverflow.com/questions/8919910/is-it-possible-to-reference-a-column-other-than-id-for-a-joincolumn
			//$sql = "select mentor.id as mentor_id, mentee.id, mentor.name as mentor_name, mentee.name, mentee.email, mentor.email as mentor_email, cs1.name as mentor_status, cs1.id as mentor_status_id, cs2.name as mentee_status, cs2.id as mentor_status_id from customer mentee join customer_hierarchy ch1 on mentee.id = ch1.child_customer_id join customer mentor on ch1.parent_customer_id = mentor.id join role r1 on r1.id = mentee.role_id join role r2 on mentor.role_id = r2.id join customer_status cs1 on mentor.status_id = cs1.id join customer_status cs2 on mentee.status_id = cs2.id where r1.name = 'Mentee' and r2.name = 'Mentor'";
			//$sql = "select mentee.id, mentee.name, ch1.id, mentor.id from customer mentee join customer_hierarchy ch1 on mentee.id = ch1.child_customer_id join customer mentor on mentor.id = ch1.parent_customer_id where mentee.role_id = 6";
			//$sql = "select id from customer_hierarchy";

			$rsm = new ResultSetMapping();


			$rsm->addEntityResult('\Cap\Entity\Customer','mentee');
			$rsm->addFieldResult('mentee', 'id', 'id');
			$rsm->addFieldResult('mentee', 'name', 'name');


			$rsm->addJoinedEntityResult('\CAP\Entity\CustomerHierarchy', 'ch1', 'mentee', 'children');
			//$rsm->addEntityResult('\CAP\Entity\CustomerHierarchy', 'ch1');
			$rsm->addFieldResult('ch1', 'id', 'id');
			//$rsm->addFieldResult('ch1', 'child_customer_id', 'childCustomerId');

			//$rsm->addJoinedEntityResult('\CAP\Entity\Customer' , 'mentor', 'ch1', 'parent_customer_id');
			//$rsm->addFieldResult('mentor', 'id', 'id');

			$rsm->addJoinedEntityResult('\CAP\Entity\CustomerStatus' , 'cs1', 'cs1', 'id');
			$rsm->addFieldResult('cs1', 'name', 'name');
			*/

			return new JsonModel(
				array(
					'saqList' => $this->getSAQList(),
					'mentors' => $this->getAllMentors(),
					'mentees' => $this->getAllMentees(),
					'admins'  => $this->getAllAdmins(),
				)
			);

		}

		if ( $this->identity()->getRole()->getName() == "Mentor" ) {
			$logger = $this->getServiceLocator()->get( 'Log\App' );
			/* get all mentees that are children of this mentor */
			/* get all mentees for this mentor */
			$sql = "SELECT c.id, c.name FROM CAP\Entity\CustomerHierarchy ch JOIN ch.childCustomer c WHERE ch.parentCustomerId = :parentId";
			$mentees = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )
							 				->setParameter('parentId',$this->identity()->getId())
											->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

			return new JsonModel(array('mentees' => $mentees));
		}


	}

	/* POST /dashboard */
	public function create( $data ) {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, $data );
		return new JsonModel( array( 'login' => 'false', 'message' => 'Please enter a valid email and password.' ) );
	}

	private function getSAQList() {
		/* get SAQ List */
		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
		$saqList = $entityManager->createQuery( "SELECT q.name, q.id FROM CAP\Entity\Questionnaire q " )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
		return $saqList;
	}

	private function getAllMentors() {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$sql = "SELECT c.id, c.name, s.name as status FROM CAP\Entity\Customer c JOIN c.status s JOIN c.role r WHERE r.name = 'Mentor'";
		$mentors = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
		$logger->log( \Zend\Log\Logger::INFO, $mentors );

		/* get each mentee's mentor */
		foreach ( $mentors as $idx => $mentor ) {
			$sql = "SELECT c.id, c.name FROM CAP\Entity\CustomerHierarchy ch JOIN ch.childCustomer c WHERE ch.parentCustomerId = ".$mentor['id'];
			$mentees = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
			$logger->log( \Zend\Log\Logger::INFO, $mentor );
			$mentors[$idx]['mentees'] = $mentees;
		}
		return $mentors;
	}

	private function getAllAdmins() {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		/* get all admins */
		$sql = "SELECT c.id, c.name, s.name as status FROM CAP\Entity\Customer c JOIN c.status s JOIN c.role r WHERE r.name = 'Admin'";
		$admins = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
		return $admins;
	}

	private function getAllMentees() {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		/* get all mentees */
		$sql = "SELECT c.id, c.name FROM CAP\Entity\Customer c JOIN c.role r WHERE r.name = 'Mentee'";
		$mentees = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
		$logger->log( \Zend\Log\Logger::INFO, $mentees );

		/* get each mentee's mentor */
		foreach ( $mentees as $idx => $mentee ) {
			$sql = "SELECT c.id, c.name FROM CAP\Entity\CustomerHierarchy ch JOIN ch.parentCustomer c WHERE ch.childCustomerId = ".$mentee['id'];
			$mentor = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );
			$mentees[$idx]['status'] = $mentor[0] ? 'Assigned' : 'Unassigned';
			$logger->log( \Zend\Log\Logger::INFO, $mentor );
			$mentees[$idx]['mentor'] = $mentor[0];
		}

		return $mentees;
	}

}
