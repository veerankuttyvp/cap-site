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
use CsnUser\Options\ModuleOptions;
use CsnUser\Service\UserService as UserCredentialsService;

/**
 * Resetpassword controller
 */
class ResetpasswordController extends AbstractRestfulController
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
    public function confirmEmailChangePasswordAction()
    {


      $token = $this->params()->fromRoute('id');
      try {
        $entityManager = $this->getEntityManager();
        if($token !== '' && $user = $entityManager->getRepository('CsnUser\Entity\Customer')->findOneBy(array('registrationToken' => $token))) {

          $form = $this->getUserFormHelper()->createUserForm($user, 'ChangePassword');

          $viewModel = new ViewModel(array(
              'token' => $user->getRegistrationToken(),
              'form' => $form,
              'navMenu' => $this->getOptions()->getNavMenu()
          ));
          return $viewModel;
        } else {
          return $this->redirect()->toRoute('user-index');
        }
      } catch (\Exception $e) {
        return $this->getServiceLocator()->get('csnuser_error_view')->createErrorView(
            $this->getTranslatorHelper()->translate('An error occured during the confirmation of your password change! Please, try again later.'),
            $e,
            $this->getOptions()->getDisplayExceptions(),
            $this->getOptions()->getNavMenu()
        );
      }


   }

    public function create($data)
    {
      $token  = '';
       if($this->getRequest()->isPost()) {

            $user = new Customer;
            if($data['new_password'] == $data['confirm_password']) {
             #   $data = $form->getData();
                #$identicalValidator = new IdenticalValidator(array('token' => $currentAnswer));
                $token = $data['token'];
                $entityManager = $this->getEntityManager();

                if($token !== '' && $user = $entityManager->getRepository('CsnUser\Entity\Customer')->findOneBy(array('registrationToken' => $token)))                {
                    $user->setPassword(UserCredentialsService::encryptPassword($data['new_password']));
                    $entityManager = $this->getEntityManager();
                    $user->setRegistrationToken(md5(uniqid(mt_rand(), true)));
                    $entityManager->persist($user);
                    $entityManager->flush();
                    return new JsonModel(array('passwordreset' => 'success', 'message' => 'password successfully reset'));
                } else {
                    $message = $this->getTranslatorHelper()->translate('Invalid request');
                }
            }

          return new JsonModel(array('passwordreset' => 'failed', 'message' => $message));
        } else {

               $message = "New Password and Confirm Password do not match";
         }

    }
    /**
     * Send Email
     *
     * Sends plain text emails
     *
     */
    private function sendEmail($to = '', $subject = '', $messageText = '')
    {
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
