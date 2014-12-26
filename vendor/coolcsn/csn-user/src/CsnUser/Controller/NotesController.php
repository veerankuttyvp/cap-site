<?php
/**
 * CsnUser - Coolcsn Zend Framework 2 User Module
 *
 * @link https://github.com/coolcsn/CsnUser for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnUser/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */

namespace CsnUser\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message;
use Zend\Validator\Identical as IdenticalValidator;
use Zend\View\Model\JsonModel;
use CsnUser\Entity\Customer;
use CsnUser\Entity\CustomerStatus;
use CsnUser\Entity\CustomerHierarchy;
use CsnUser\Entity\CustomerNote;
use CsnUser\Entity\CustomerNoteMap;
use CsnUser\Options\ModuleOptions;
use CsnUser\Service\UserService as UserCredentialsService;

/**
 * AdminMentor controller
 */
class NotesController extends AbstractRestfulController
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Zend\Mvc\I18n\Translator
     */
    protected $translatorHelper;

    /**
     * @var Zend\Form\Form
     */
    protected $userFormHelper;

    /**
     * Reset Password Action
     *
     * Send email reset link to user
     *
     * @return Zend\View\Model\ViewModel
     */

     public function getList()
    {

     if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

    $uid =  $user->getId();
    $entityManager = $this->getEntityManager();
       # $user = $entityManager->getRepository('CsnUser\Entity\Question')->findall();

       $notes = $entityManager->createQuery("SELECT n.note as title, n.created, n.id FROM CsnUser\Entity\CustomerNote n  WHERE n.customer = '$uid'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      return new JsonModel(array('data' => $notes));

    }

    public function get($id)
    {

      if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

      $uid =  $user->getId();

        $entityManager = $this->getEntityManager();
       # $user = $entityManager->getRepository('CsnUser\Entity\Question')->findall();

       $notes = $entityManager->createQuery("SELECT cn.id, cn.note as title, cn.id, cn.created FROM CsnUser\Entity\CustomerNoteMap nm JOIN nm.customerNote cn WHERE cn.customer = '$id' and nm.customer = $uid")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      return new JsonModel(array('data' => $notes));

    }

    public function create($data)
    {

    if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

      $uid =  $user->getId();


      $entityManager = $this->getEntityManager();

      $note = new CustomerNote;

     # $user = $user[0];

      $note->setCustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
      $note->setNote($data['title']);
      $note->setCreated($data['created']);
      $entityManager->persist($note);
      $entityManager->flush();
      $id = $note->getId();

      if($data['share_with_mentee']) {


         $this->sharenote($data['mentee'], $id);
      }


      return new JsonModel(array('status' => $id));

    }


    public function update($id, $data)
    {

    if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

      $uid =  $user->getId();


      $entityManager = $this->getEntityManager();

      $note = $entityManager->createQuery("SELECT n FROM CsnUser\Entity\CustomerNote n  WHERE n.id = '$id'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      $note = $note[0];

      #$note->setCustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
      $note->setNote($data['title']);
      $note->setCreated($data['created']);
      $entityManager->persist($note);
      $entityManager->flush();

      if($data['share_with_mentee']) {
         $this->sharenote($data['mentee'], $id);
      }
      return new JsonModel(array('status' => true));

    }



    public function delete($id) {

      $status = "failed";
      $entityManager = $this->getEntityManager();
      $note = $entityManager->getRepository('CsnUser\Entity\CustomerNote')->findOneBy(array('id' => $id));

      if($note) {

          $entityManager->remove($note);
          $entityManager->flush();
          $status = "success";
      }
      return new JsonModel(array('status' => $status));

    }

    public function sharenote($cid, $note=null) {

     if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

        $entityManager = $this->getEntityManager();
       # $user = $entityManager->getRepository('CsnUser\Entity\Question')->findall();

       $notes = $entityManager->createQuery("SELECT cn FROM CsnUser\Entity\CustomerNoteMap cn WHERE cn.customerNote='$note'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

     if(!$notes) {

      $notes = new CustomerNoteMap;

      $notes->setCustomer($entityManager->find('CsnUser\Entity\Customer', $cid));
      $notes->setCustomerNote($entityManager->find('CsnUser\Entity\CustomerNote', $note));
      $notes->setCreated('434343');
      $entityManager->persist($notes);
      $entityManager->flush();

      return 1;
     }

     }
    /**
     * Confirm Saq View Action
     *
     * @return Zend\View\Model\ViewModel
     */
 #   public function menteeAction()
 #   {

 #    if (!$user = $this->identity()) {

 #            return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

 #    }

 #      $this->layout('layout/dashboard');
 #      $viewModel  =  new ViewModel(array('mentor_id' => $this->params('id')));
 #
 #      $menuview = new ViewModel(array('name' => $user->getFirstName()));
 #      $menuview->setTemplate('layout/menu');
 #      $viewModel->addChild($menuview, 'menuview');
 #      return $viewModel;

 #   }


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
     * get options
     *
     * @return ModuleOptions
     */
    private function getOptions()
    {
        if(null === $this->options) {
            $this->options = $this->getServiceLocator()->get('csnuser_module_options');
        }

        return $this->options;
    }

    /**
     * get entityManager
     *
     * @return Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        if(null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->entityManager;
    }

    /**
     * get translatorHelper
     *
     * @return  Zend\Mvc\I18n\Translator
     */
    private function getTranslatorHelper()
    {
        if(null === $this->translatorHelper) {
            $this->translatorHelper = $this->getServiceLocator()->get('MvcTranslator');
        }

        return $this->translatorHelper;
    }

    /**
     * get userFormHelper
     *
     * @return  Zend\Form\Form
     */
    private function getUserFormHelper()
    {
        if(null === $this->userFormHelper) {
            $this->userFormHelper = $this->getServiceLocator()->get('csnuser_user_form');
        }

        return $this->userFormHelper;
    }
}
