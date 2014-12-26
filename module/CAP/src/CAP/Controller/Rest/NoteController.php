<?php
namespace CAP\Controller\Rest;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use CAP\Entity\Customer;
use CAP\Entity\CustomerNote;
use CAP\Entity\CustomerNoteMap;

class NoteController extends AbstractRestfulController {

  /* will return note title and note data */
	public function get( $id ) {
		$logger = $this->getServiceLocator()->get( 'Log\App' );
		$logger->log( \Zend\Log\Logger::INFO, "Rest call to GET /mentor/".$id );

		/* must be logged in & must be either admin or a mentee of this mentor */
		if ( !$this->identity() ) {
			return JsonModel( array() );
		}

		$entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );

    /* make sure you own this note or this note has been shared with you */

    /* TODO: implement this */

    return new JsonModel();

	}

	/* will return a list myNotes and sharedNotes for a given mentor-mentee relationship
    depending on context of who is logged in myNotes should be appropriate
  */
	public function getList() {

	}

  /* PUT /note/:id  - edit a note */
  public function update($id, $data) {
    if ( !$this->identity() ) {
      return JsonModel( array() );
    }


    $logger = $this->getServiceLocator()->get( 'Log\App' );
    $logger->log( \Zend\Log\Logger::INFO, $id);
    $logger->log( \Zend\Log\Logger::INFO, $data);

    /* make sure this is my note */
    $e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $n = $e->getRepository('CAP\Entity\CustomerNote')->findOneBy(array('id' => $id, 'customerId' => $this->identity()->getId()));
    if (!$n) {
      return JsonModel( array() );
    }

    $n->setName($data['name']);
    $n->setNote($data['note']);

    $e->persist( $n );
    $e->flush();

    /* see if share is changing */

    return new JsonModel(array('success' => true));
  }

  public function delete($id) {
    if ( !$this->identity() ) {
      return JsonModel( array() );
    }

    $logger = $this->getServiceLocator()->get( 'Log\App' );
    $logger->log( \Zend\Log\Logger::INFO, $id);

    /* make sure this is my note */
    $e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $n = $e->getRepository('CAP\Entity\CustomerNote')->findOneBy(array('id' => $id, 'customerId' => $this->identity()->getId()));
    if (!$n) {
      $logger->log( \Zend\Log\Logger::INFO, "no note to delete!");
      return JsonModel( array() );
    }
    $e->remove($n);
    $e->flush();

    return new JsonModel(array('success' => true));
  }

  /* POST /note  - edit a note */
  public function create($data) {
    $logger = $this->getServiceLocator()->get( 'Log\App' );
    $logger->log( \Zend\Log\Logger::INFO, $data);

    if ( !$this->identity() ) {
      return JsonModel( array() );
    }

    /* make sure the relationship exists between the 2 customers */
    $sql = "SELECT ch.id FROM CAP\Entity\CustomerHierarchy ch WHERE (ch.parentCustomer = :parentId AND ch.childCustomer = :childId) OR (ch.parentCustomer = :childId AND ch.childCustomer = :parentId)";
    $ch = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' )->createQuery( $sql )
               ->setParameter('childId',$data['customerId'])
               ->setParameter('parentId',$this->identity()->getId())
               ->getResult( \Doctrine\ORM\Query::HYDRATE_OBJECT );

    if (!$ch || !$ch[0]) {
      $logger->log( \Zend\Log\Logger::INFO, 'theres no customer relationship');
      return JsonModel( array() );
    }
    $now = date("Y-m-d H:i:s");
    /* make sure this is my note */
    $e = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
    $n = new CustomerNote;
    $n->setCustomer($this->identity());
    $n->setName($data['note']['name']);
    $n->setNote($data['note']['note']);
    $n->setCreated($now);
    $e->persist( $n );

    /* add the customer_note_map */
    $nm = new CustomerNoteMap;
    $nm->setCustomer($e->find( 'CAP\Entity\Customer', $data['customerId'] ));
    $nm->setCustomerNote($n);
    $nm->setShare($data['note']['share']);
    $nm->setCreated($now);
    $e->persist($nm);

    $e->flush();

    $noteData = array(
      'id' => $n->getId(),
      'name' => $n->getName(),
      'note' => $n->getNote(),
      'created' => $n->getCreated(),
      'share' => ($data['note']['share'])
    );

    return new JsonModel(array('success' => true,'note' => $noteData));
  }


}
