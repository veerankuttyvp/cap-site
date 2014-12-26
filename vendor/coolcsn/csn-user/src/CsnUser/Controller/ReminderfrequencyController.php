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
use CsnUser\Entity\ReminderFrequency;
use CsnUser\Options\ModuleOptions;
use CsnUser\Service\UserService as UserCredentialsService;

/**
 * Reminderfrequency controller
 */
class ReminderfrequencyController extends AbstractRestfulController
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


    public function get($id) 
    {

     return new JsonModel(array('data' => $id)); 
      $user = $this->getEntityManager()->createQuery("SELECT u FROM CsnUser\Entity\ReminderFrequency u WHERE u.id = '1'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      # $user = new ReminderFrequency;
        $user = $user[0];
       $user->setFrequency("DAILY");
       $entityManager = $this->getEntityManager();
       $entityManager->persist($user);
                    $entityManager->flush();
      #  $u = $user->getFrequency();
       return new JsonModel(array('data' => $u));
    
    }


    public function setfrequencyAction()
    {

       if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

       }
     
       $this->layout('layout/dashboard');
       $viewModel  =  new ViewModel();
     
       $menuview = new ViewModel(array('name' => $user->getFirstName()));
       $menuview->setTemplate('layout/menu');
       $viewModel->addChild($menuview, 'menuview');

       return $viewModel;

    }



    public function create($data)
    {

     if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

     }  
   
      $user = $this->getEntityManager()->createQuery("SELECT u FROM CsnUser\Entity\ReminderFrequency u WHERE u.id = '1'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

       $user = $user[0];
       $user->setFrequency($data['reminder_interval']);
       $entityManager = $this->getEntityManager();
       $entityManager->persist($user);
       $entityManager->flush();
       return new JsonModel(array('status' => "success"));
    
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
