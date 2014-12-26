<?php

namespace CapAuth\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\JsonModel;

use CapAuth\Model\User;

class AuthController extends AbstractRestfulController
{
    protected $form;
    protected $storage;
    protected $authservice;

    // test function
    public function getList() {

     if ($email = $this->getAuthService()->hasIdentity()){

       return new JsonModel(array(
            'data' => $this->getAuthService()->getIdentity())
        );
     }
     else {

     return new JsonModel(array(
            'data' => 'sorry')
        );

    }
    }



    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                                      ->get('AuthService');
        }
        
        return $this->authservice;
    }
    
    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()
                                  ->get('CapAuth\Model\MyAuthStorage');
        }
        
        return $this->storage;
    }
    
    
    public function loginAction()
    {
        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('success');
        }
                
        $form       = $this->getForm();
        
        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages()
        );
    }
    
    public function create($data)
    {
                
             //check authentication...
             
              $login = "false";                

                $this->getAuthService()->getAdapter()
                                       ->setIdentity($data['email'])
                                       ->setCredential($data['password']);
                                       
                $result = $this->getAuthService()->authenticate();
                foreach($result->getMessages() as $message)
                {
                    //save message temporary into flashmessenger
                    $this->flashmessenger()->addMessage($message);
                }
                
                if ($result->isValid()) {
                    $login = 'true';
                    //check if it has rememberMe :
                    if ($data['rememberme'] == 1 ) {
                        $this->getSessionStorage()
                             ->setRememberMe(1);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                    
                    $result = $this->getAuthService()->getAdapter()->getResultRowObject();
                    $uid = $result->id;
                    $role_id = $result->role_id;
                    $this->getAuthService()->getStorage()->write($uid);

                }
          
        
        return new JsonModel(array(
            'login' => $login,
            'uid' => $uid,
            'role_id' => $role_id 
             )
        );
    }
    
    public function logoutAction()
    {
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
            $this->flashmessenger()->addMessage("You've been logged out");
        }
        
        return new JsonModel(array('login' => 'false',));
    }
}
