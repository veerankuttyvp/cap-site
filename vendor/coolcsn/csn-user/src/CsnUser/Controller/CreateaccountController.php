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
 * Createaccount controller
 */
class CreateaccountController extends AbstractRestfulController
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
     * Register Index Action
     *
     * Displays user registration form using Doctrine ORM and Zend annotations
     *
     * @return Zend\View\Model\ViewModel
     */

    public function createAccountAction()
    {

        if(!$user = $this->identity()) {

            return $this->redirect()->toRoute('user-index',array('action' =>  'login'));
        }

        $this->layout('layout/dashboard');
        $viewModel =  new ViewModel(array('user'=> $user->getId() ,'role' => $user->getRoleid()));

        $menuview = new ViewModel(array('name' => $user->getFirstName(), 'role' => $user->getRoleid()));
        $menuview->setTemplate('layout/menu');
        $viewModel->addChild($menuview, 'menuview');

        return $viewModel;

    }

    public function create($data)
    {
        if($admin = $this->identity()) {

             $role = $admin->getFirstName();

           #  return new JsonModel(array('status' => $data['account_type'] , 'message' => 'An email has been sent to user'));
        }



        $user = new Customer;
        if($this->getRequest()->isPost()) {
           $entityManager = $this->getEntityManager();
           $customer = $entityManager->createQuery("SELECT u FROM CsnUser\Entity\Customer u WHERE u.email = '$data[email]'")->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
                $customer = $customer[0];

            if(!$customer) {
                $user->setDomain($entityManager->find('CsnUser\Entity\Domain', 3));
                $user->setRole($entityManager->find('CsnUser\Entity\Role', $data['account_type']));
                $user->setStatus($entityManager->find('CsnUser\Entity\CustomerStatus', 1));
                $user->setFirstName($data['name']);
                #$user->setLastName($data['lastname']);
                $user->setEmail($data['email']);
                $user->setRegistrationToken(md5(uniqid(mt_rand(), true)));
                $user->setPassword(UserCredentialsService::encryptPassword($this->generatePassword()));

                try {
                    $fullLink = $this->getBaseUrl() . $this->url()->fromRoute('user-register', array( 'action' => 'confirm-email-change-password', 'id' => $user->getRegistrationToken()));
                          $this->sendEmail(
                        $user->getEmail(),
                        $this->getTranslatorHelper()->translate('Please, confirm your registration!'),
                        sprintf($this->getTranslatorHelper()->translate('Please, click the link to confirm your registration => %s'), $fullLink)
                    );
                    $entityManager->persist($user);
                        $entityManager->flush();

                     return new JsonModel(array('status' => 'true', 'message' => 'An email has been sent to user'));
                } catch (\Exception $e) {

                        return new JsonModel(array('status' => 'false', 'message' =>'Something went wrong when trying to send activation email! Please, try again later.'));
                }
            } else {

              return new JsonModel(array('status' => 'false', 'message' =>'This email is already registered'));

            }
        }

        return new JsonModel(array('status' => 'false', 'message' =>'Some Error Occured'));
    }


    /**
     * Generate Password
     *
     * Generates random password
     *
     * @return String
     */
    private function generatePassword($l = 8, $c = 0, $n = 0, $s = 0)
    {
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
