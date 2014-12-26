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
use CsnUser\Entity\CustomerQuestionnaire;
use CsnUser\Options\ModuleOptions;
use CsnUser\Service\UserService as UserCredentialsService;

/**
 * Saqlist controller
 */
class SaqlistController extends AbstractRestfulController
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


       #return new JsonModel(array('data' => ''));

      $entityManager = $this->getEntityManager();
                $user = $entityManager->createQuery("SELECT q.name, q.id FROM CsnUser\Entity\Questionnaire q ")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);


        return new JsonModel(array('data' => $user));
    }

    public function get($id)
    {


        $entityManager = $this->getEntityManager();
       # $user = $entityManager->getRepository('CsnUser\Entity\Question')->findall();

       $user = $entityManager->createQuery("SELECT q.id, q.questionText, q.questionOrder, s.name FROM CsnUser\Entity\Question q JOIN q.section s WHERE q.questionnaire = '$id'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      foreach($user as $key => $u) {

            $answers = $entityManager->createQuery("SELECT a.id FROM CsnUser\Entity\Answer a WHERE a.question = '$u[id]'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

            $cus_ans = $entityManager->createQuery("SELECT ca.id FROM CsnUser\Entity\CustomerAnswer ca WHERE ca.answer IN (2,3)")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

            $user[$key]['answers'] = $cus_ans;
      }
      return new JsonModel(array('data' => $user));

    }

    public function create($data)
    {


      $entityManager = $this->getEntityManager();

      if($questionnaire = $this->getEntityManager()->createQuery("SELECT u.id FROM CsnUser\Entity\CustomerQuestionnaire u WHERE u.customer = $data[mentee_id] and u.questionnaire = $data[questionnaire_id]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT)) {

          $msg = "already_assigned";
      } else {

        $questionnaire =  new CustomerQuestionnaire;
        $questionnaire->setcustomer($entityManager->find('CsnUser\Entity\Customer', $data['mentee_id']));
        $questionnaire->setquestionnaire($entityManager->find('CsnUser\Entity\Questionnaire', $data['questionnaire_id']));
        $questionnaire->setcompletionStatus($entityManager->find('CsnUser\Entity\CompletionStatus', '1'));
        $entityManager->persist($questionnaire);
        $entityManager->flush();
        $msg = "success";
      }
      #$user = $user[0];
      #if($data['new_status'] == 1) {$status = "ACTIVE";} else if($data['new_status'] == 2) { $status = "INACTIVE";}
      #$user->setStatus($entityManager->find('CsnUser\Entity\CustomerStatus', $data['new_status']));
      #$entityManager->persist($user);
      #$entityManager->flush();

      return new JsonModel(array('status' => $msg));

    }

    /**
     * Confirm Saq View Action
     *
     * @return Zend\View\Model\ViewModel
     */
    public function saqviewAction()
    {

     if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

       $this->layout('layout/saq');
       $viewModel  =  new ViewModel(array('id' => $this->params('id')));
       return $viewModel;

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
