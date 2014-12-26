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
use Zend\Session\SessionManager;
use Zend\Session\Config\StandardConfig;
use Zend\View\Model\JsonModel;
use CsnUser\Entity\Customer;
use CsnUser\Options\ModuleOptions;

/**
 * Index controller
 */
class IndexController extends AbstractRestfulController
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
     * Index action
     *
     * The method show to users they are guests
     *
     * @return Zend\View\Model\ViewModelarray navigation menu
     */
    public function getList()
    {
        return $this->redirect()->toRoute('user-index',array('action' =>  'login'));
    }

    /**
     * Log in action
     *
     * The method uses Doctrine Entity Manager to authenticate the input data
     *
     * @return Zend\View\Model\ViewModel|array login form|array messages|array navigation menu
     */
    public function loginAction() {
        if ($user = $this->identity()) {

            return $this->redirect()->toRoute('user-index',array('action' =>  'dashboard'));
        }


        return new ViewModel();
    }


    public function dashboardAction() {
        if (!$user = $this->identity()) {

             return $this->redirect()->toRoute('user-index',array('action' =>  'login'));

         }

      $this->layout('layout/dashboard');
      $viewModel  =  new ViewModel(array('user'=> $user->getId() ,'role' => $user->getRoleid()));

      if($user->getRoleid() == 6) {

          $pid = $user->getRoleid();
          $entityManager = $this->getEntityManager();
          $parent = $entityManager->createQuery("SELECT ch.firstName, ch.id, ch.email FROM CsnUser\Entity\CustomerHierarchy c JOIN c.parentCustomer ch WHERE c.childCustomer = '$pid'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

      } else {



      }


      $menuview = new ViewModel(array('name' => $user->getFirstName(), 'role' => $user->getRoleid()));
      $menuview->setTemplate('layout/menu');
      $viewModel->addChild($menuview, 'menuview');

     return $viewModel;

    }

    public function create($data) {

        $user = new Customer;
        #$form = $this->getUserFormHelper()->createUserForm($user, 'login');
        $messages = null;
        if ($this->getRequest()->isPost()) {
                $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                $adapter = $authService->getAdapter();
                $usernameOrEmail = $data['email'];

                try {
                    $user = $this->getEntityManager()->createQuery("SELECT u FROM CsnUser\Entity\Customer u WHERE u.email = '$usernameOrEmail'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
                    $user = $user[0];

                    if(!isset($user)) {
                        $message = 'The username or email is not valid!';
                        return new JsonModel(array('login' => 'false', 'message' => $message));
                    }

                    $adapter->setIdentityValue($user->getEmail());
                    $adapter->setCredentialValue($data['password']);

                    $authResult = $authService->authenticate();
                    if ($authResult->isValid()) {
                        $identity = $authResult->getIdentity();
                        $authService->getStorage()->write($identity);

                        if ($data['rememberme']) {
                            $time = 1209600; // 14 days (1209600/3600 = 336 hours => 336/24 = 14 days)
                            $sessionManager = new SessionManager();
                            $sessionManager->rememberMe($time);
                        }

                        return new JsonModel(array('login' => 'true', 'message' => 'success'));;
                    }

                    foreach ($authResult->getMessages() as $message) {
                      $messages .= "$message\n";
                    }
                } catch (\Exception $e) {
                    return $this->getServiceLocator()->get('csnuser_error_view')->createErrorView(
                        $this->getTranslatorHelper()->translate('Something went wrong during login! Please, try again later.'),
                        $e,
                        $this->getOptions()->getDisplayExceptions(),
                        $this->getOptions()->getNavMenu()
                    );
                }

        }

        return new JsonModel(array('login' => 'false', 'message' => 'credential are not valid'));
    }

    /**
     * Log out action
     *
     * The method destroys session for a logged user
     *
     * @return redirect to specific action
     */
    public function logoutAction() {
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }

        return $this->redirect()->toRoute('user-index',array('action' =>  'login'));
    }

    /**
     * get options
     *
     * @return ModuleOptions
     */
    private function getOptions() {
        if (null === $this->options) {
            $this->options = $this->getServiceLocator()->get('csnuser_module_options');
        }

        return $this->options;
    }

    /**
     * get entityManager
     *
     * @return EntityManager
     */
    private function getEntityManager() {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->entityManager;
    }

    /**
     * get translatorHelper
     *
     * @return  Zend\Mvc\I18n\Translator
     */
    private function getTranslatorHelper() {
        if (null === $this->translatorHelper) {
           $this->translatorHelper = $this->getServiceLocator()->get('MvcTranslator');
        }

        return $this->translatorHelper;
    }

    /**
     * get userFormHelper
     *
     * @return  Zend\Form\Form
     */
    private function getUserFormHelper() {
        if (null === $this->userFormHelper) {
           $this->userFormHelper = $this->getServiceLocator()->get('csnuser_user_form');
        }

        return $this->userFormHelper;
    }
}
