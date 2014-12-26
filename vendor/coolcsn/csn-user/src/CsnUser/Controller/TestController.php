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
 * Test controller
 */
class TestController extends AbstractRestfulController
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
 
       $user = $entityManager->createQuery("SELECT q.questionText,q.questionOrder, s.name FROM CsnUser\Entity\Question q JOIN q.section s WHERE q.questionnaire = '$id'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      return new JsonModel(array('data' => $user));
   
    }

    public function create($data)
    {

      if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }

      $uid = $user->getId();

       
       $entityManager = $this->getEntityManager();

       if(isset($data['AnswerSubmit'])) {
  
        $ans = json_decode($data['selected_answers']);  
 
        if($data['answers_type'] == 'ENUM') {
       
        $enums = [];
        foreach($ans as $a) {

          $a = (array)$a; 
          
        if($a['selected_enume']){ 
            
            $enums[$a['id']] = $a['selected_enume'];

           $customeranswer =  new CustomerAnswer;
           $customeranswer->setcustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
           $customeranswer->setAnswer($entityManager->find('CsnUser\Entity\Answer', $a['id']));

           $enummap = $entityManager->createQuery("SELECT em.answerEnumId FROM CsnUser\Entity\AnswerEnumMap em WHERE em.id= $a[selected_enume]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
           
           $enummap = $enummap[0];
           #return new JsonModel(array('status' => $parent));
           $customeranswer->setAnswerEnum($entityManager->find('CsnUser\Entity\AnswerEnum', $enummap['answerEnumId'])); 
           $customeranswer->setCreated('test');
           $entityManager->persist($customeranswer);
           $entityManager->flush();
  
                   

       }
     }
 
        }

      else if ($data['answers_type'] == 'CHECKBOX') {

        foreach($ans as $a) {

          $a = (array)$a;

        #  return new JsonModel(array('status' => $a));
        if($a['selected']){

           $customeranswer =  new CustomerAnswer;
           $customeranswer->setcustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
           $customeranswer->setAnswer($entityManager->find('CsnUser\Entity\Answer', $a['id']));

          # $enummap = $entityManager->createQuery("SELECT em.answerEnumId FROM CsnUser\Entity\AnswerEnumMap em WHERE em.id= $a[selected_enume]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

          # $enummap = $enummap[0];
           #return new JsonModel(array('status' => $parent));
          # $customeranswer->setAnswerEnum($entityManager->find('CsnUser\Entity\AnswerEnum', $enummap['answerEnumId']));
           $customeranswer->setCreated('test');
           $entityManager->persist($customeranswer);
           $entityManager->flush();

       }
     }

    }

 
    #    return new JsonModel(array('status' => $ans));
       else if(is_array($ans)) {

        foreach ($ans as $a) {
     
        #return new JsonModel(array('status' => 'array'));
        
        $customeranswer =  new CustomerAnswer;
        $customeranswer->setcustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
        $customeranswer->setAnswer($entityManager->find('CsnUser\Entity\Answer', $a)); 
        $customeranswer->setCreated('test');
        $entityManager->persist($customeranswer);
        $entityManager->flush();
     
        }
        }
       
        else if ($data['answers_type'] == 'TEXT' or $data['answers_type'] == 'TEXTAREA') {

        $customeranswer =  new CustomerAnswer;
        $customeranswer->setcustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
        $customeranswer->setAnswer($entityManager->find('CsnUser\Entity\Answer', 1));
        $customeranswer->setAnswerText($data['selected_answers']);
        $customeranswer->setCreated('test');
        $entityManager->persist($customeranswer);
        $entityManager->flush();

        }

       else {
       
        #return new JsonModel(array('status' => $ans));         

        $customeranswer =  new CustomerAnswer;
        $customeranswer->setcustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
        $customeranswer->setAnswer($entityManager->find('CsnUser\Entity\Answer', $ans)); 
        $customeranswer->setCreated('test');
        $entityManager->persist($customeranswer);
        $entityManager->flush();
   
        }

 
        $customer_question = new CustomerQuestion;
        $customer_question->setCustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
        $customer_question->setQuestion($entityManager->find('CsnUser\Entity\Question', $data['id']));
        $customer_question->setCompletionStatus($entityManager->find('CsnUser\Entity\CompletionStatus', 3));
        $customer_question->setCreated('test');
        $entityManager->persist($customer_question);
        $entityManager->flush();


       
        $qst = $entityManager->createQuery("SELECT q FROM CsnUser\Entity\Question q WHERE q.id = $data[id]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
         $qst = $qst[0];
         $qid  =  $qst->getQuestionnaireid();
         $section =  $qst->getSectionid();
         $order =  $qst->getQuestionOrder();
          
       } else {

          $qid = $data['qid'];
          $section = 1;
          $order = 0;
       
       }
       $entityManager = $this->getEntityManager();
       if(!$question = $entityManager->createQuery("SELECT q.id, q.questionnaireid, qn.name as saq_name, q.questionText,q.questionOrder, s.name as section_name, s.id as section_id FROM CsnUser\Entity\Question q JOIN q.section s JOIN q.questionnaire qn WHERE q.questionnaire = '$qid' and q.section = '$section' and q.questionOrder > '$order' order by q.questionOrder")->setMaxResults(1)->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT)) {


        $customer_section = new CustomerSection;
        $customer_section->setCustomer($entityManager->find('CsnUser\Entity\Customer', $uid));
        $customer_section->setSection($entityManager->find('CsnUser\Entity\Section', $section));
        $customer_section->setCompletionStatus($entityManager->find('CsnUser\Entity\CompletionStatus', 3));
        $customer_section->setCreated('test');
        $entityManager->persist($customer_section);
        $entityManager->flush();



            $section = $section+1;
            $order = 0;
            if(!$question = $entityManager->createQuery("SELECT q.id, q.questionnaireid, qn.name as saq_name, q.questionText,q.questionOrder, s.name FROM CsnUser\Entity\Question q JOIN q.section s JOIN q.questionnaire qn WHERE q.questionnaire = '$qid' and q.section = '$section' and q.questionOrder > '$order' order by q.questionOrder")->setMaxResults(1)->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT)) { 

            $cus_qstnr = $entityManager->createQuery("SELECT cq FROM CsnUser\Entity\CustomerQuestionnaire cq WHERE cq.questionnaire = $qid and cq.customer = $uid")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

            $cus_qstnr = $cus_qstnr[0];
            $cus_qstnr->setcompletionStatus($entityManager->find('CsnUser\Entity\CompletionStatus', 3));
           # $cus_qstnr->setcustomer($entityManager->find('CsnUser\Entity\Customer', $uid));        
            $entityManager->persist($cus_qstnr);
            $entityManager->flush(); 
            return new JsonModel(array('status' => 'finished'));

         }
       }
       $question_id = $question[0]['id']; 
       $answers = $entityManager->createQuery("SELECT a.id, a.answerNumber, a.answerText, a.answerOrder, a.answerType FROM CsnUser\Entity\Answer a WHERE a.question = '$question_id'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        foreach ($answers as $key => $answer) {
             $answer_enum = $entityManager->createQuery("SELECT aem.id, ae.name FROM CsnUser\Entity\AnswerEnumMap aem JOIN aem.answerEnum ae WHERE aem.answer = '$answer[id]'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
             $answers[$key]['answer_enum'] = $answer_enum;
        }

       return new JsonModel(array('question' => $question, 'answer' => $answers));


    #  $entityManager = $this->getEntityManager();

    #  if($questionnaire = $this->getEntityManager()->createQuery("SELECT u.id FROM CsnUser\Entity\CustomerQuestionnaire u WHERE u.customer = $data[mentee_id] and u.questionnaire = $data[questionnaire_id]")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT)) {

     #     $msg = "already_assigned";
     # } else {

     #   $questionnaire =  new CustomerQuestionnaire;
     #   $questionnaire->setcustomer($entityManager->find('CsnUser\Entity\Customer', $data['mentee_id']));
     #   $questionnaire->setquestionnaire($entityManager->find('CsnUser\Entity\Questionnaire', $data['questionnaire_id'])); 
     #   $questionnaire->setcompletionStatus($entityManager->find('CsnUser\Entity\CompletionStatus', '1'));
     #   $entityManager->persist($questionnaire);
     #   $entityManager->flush();
     #   $msg = "success";
   #   }      
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
    public function saqAction()
    {

     if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }    

      # $this->layout('layout/saq');
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
