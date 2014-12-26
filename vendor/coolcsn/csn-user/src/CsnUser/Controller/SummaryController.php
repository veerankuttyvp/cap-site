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
use CsnUser\Entity\Answer;
use CsnUser\Entity\AnswerEnumMap;
use CsnUser\Entity\AnswerEnum;
use CsnUser\Entity\CustomerAnswer;
use CsnUser\Entity\CustomerQuestion;
use CsnUser\Entity\CustomerSection;
use CsnUser\Options\ModuleOptions;
use CsnUser\Service\UserService as UserCredentialsService;

/**
 * Summary controller
 */
class SummaryController extends AbstractRestfulController
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
/*
     public function getList()
    {


       #return new JsonModel(array('data' => ''));

      $entityManager = $this->getEntityManager();
                $user = $entityManager->createQuery("SELECT q.name, q.id FROM CsnUser\Entity\Questionnaire q ")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);


        return new JsonModel(array('data' => $user));
    }
*/
    public function get($id)
    {
   
   
      if (!$user = $this->identity()) {
 
             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

      }

        $uid = $user->getId();
        $entityManager = $this->getEntityManager();
 
       $sections = $entityManager->createQuery("SELECT s.id, s.name FROM CsnUser\Entity\Section s JOIN s.questionnaire q WHERE q.id = '$id'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      foreach($sections as $section) {

         $cus_sec = $entityManager->createQuery("SELECT cmps.name as status FROM CsnUser\Entity\CustomerSection cs JOIN cs.completionStatus cmps WHERE cs.customer = $uid and cs.section = $section[id]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
       
         $section['status'] = $cus_sec[0]['status'];

        $sec_qstn = $entityManager->createQuery("SELECT COUNT(q) FROM CsnUser\Entity\Question q WHERE q.section =  $section[id]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

         $section['total_questions'] = $sec_qstn[0][1];

         if($section['status'] == 'COMPLETED'){
          
              $section['completed_questions'] = $section['total_questions'];
         }
 

         $data['sections'][] = $section;
      }

      return new JsonModel(array('data' => $data));
   
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
