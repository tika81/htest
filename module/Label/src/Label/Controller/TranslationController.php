<?php
namespace Label\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Label\Model\TranslationModel;
use Label\Model\LabelModel;
use Zend\Json\Json;//test
use Label\Form\TranslationForm;
use Label\Form\TranslationFormValidator;
use Zend\Stdlib\ArrayObject;

class TranslationController extends AbstractActionController
{
    /**
     * get database adapter
     */
    private function getDBAdapter()
    {
        return $this->getServiceLocator()->get('db');
    }
    
    /**
     * provides existing translations for label and functionality for displaying not existing translations for label
     * @return void|\Zend\View\Model\ViewModel
     */
    public function showTranslationsAction()
    {
        $post = $this->getRequest()->getPost();//get all posts
        $labelId = $post['label_id'];
        
        if (!$this->getRequest()->isXmlHttpRequest() || !$labelId) {
            //if is not ajax request or label id is not defined, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $dbAdapter = $this->getDBAdapter();
        $translationModel = new TranslationModel($dbAdapter);
        
        $existingTrans    = $translationModel->getExistingTranslations($labelId);
        $notExistingTrans = $translationModel->getNotExistingTranslations($labelId);
        
        $viewModel = new ViewModel([
            'existingTrans'    => $existingTrans, 
            'notExistingTrans' => $notExistingTrans, 
            'labelId'          => $labelId
        ]);
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    /**
     * provides form for inserting new translation
     * @return void|\Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $form = new TranslationForm();
        $post = $this->getRequest()->getPost();
        $id = $post['id'];//translation id
        
        if (!$this->getRequest()->isXmlHttpRequest() || $id) {
            //if is not ajax request or translation id is not empty, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $dbAdapter = $this->getDBAdapter();
    
        $language = $post['language'];//translation language
        $labelId  = $post['label_id']; //label id
        
        $labelModel = new LabelModel($dbAdapter);
        $translationModel = new TranslationModel($dbAdapter);
        
        $label = $labelModel->getLabel($labelId);
        
        //if someone try to mess with hidden fields
        if (!$label) {
            $this->flashMessenger()->addMessage('Label does not exist.');
            return $this->redirect()->toRoute('label', ['action' => 'info', 'param' => 'warning']);
        }
        
        //if someone try to mess with hidden fields
        $translation = $translationModel->getTranslation('', $labelId, $language);
        if ($translation) {
            $this->flashMessenger()->addMessage('Translation is already defined.');
            return $this->redirect()->toRoute('label', ['action' => 'info', 'param' => 'warning']);
        }
        
        $tranArrObj = new ArrayObject();
        $tranArrObj['label_id'] = $labelId;
        $tranArrObj['language'] = $language;
        
        $form->bind($tranArrObj);
    
        if ($post['submit']) {
    
            $formValidator = new TranslationFormValidator();
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($post);
             
            if ($form->isValid()) {
                $data = $form->getData();
                $translationModel->saveTranslation($data);
                
                //show success message
                $this->flashMessenger()->addMessage('Translation is successfully added.');
                return $this->redirect()->toRoute('label', ['action' => 'info']);
            }
        }
    
        $title = 'Add new translation';
        $defaultText = $label['default_text'];
        
        $viewModel = new ViewModel([
            'form'        => $form, 
            'action'      => 'add', 
            'title'       => $title, 
            'defaultText' => $defaultText
        ]);
        
        $viewModel->setTemplate('label/translation/form');
        $viewModel->setTerminal(true);
    
        return $viewModel;
    }
    
    /**
     * provides form for editing existing translations
     * @return void|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $form = new TranslationForm();
        $post = $this->getRequest()->getPost();
        $id = $post['id'];//translation id
        
        if (!$this->getRequest()->isXmlHttpRequest() || !$id) {
            //if is not ajax request or label id is empty, redirect to index page
            return $this->redirect()->toRoute('label');
        }
    
        $dbAdapter = $this->getDBAdapter();
        $translationModel = new TranslationModel($dbAdapter);
        $translation = $translationModel->getTranslation($id);
        
        $labelId  = $translation['label_id'];
        $language = $translation['language'];
        $text     = $translation['text'];
    
        $tranArrObj = new ArrayObject();
        $tranArrObj['id']       = $id;
        $tranArrObj['label_id'] = $labelId;
        $tranArrObj['language'] = $language;
        $tranArrObj['text']     = $text;
        
        $form->bind($tranArrObj);
    
        if ($post['submit']) {
            $formValidator = new TranslationFormValidator();
            
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($post);
             
            if ($form->isValid()) {
                $data = $form->getData();
                $translationModel->saveTranslation($data);
                
                //show success message
                $this->flashMessenger()->addMessage('Translation is successfully edited.');
                return $this->redirect()->toRoute('label', ['action' => 'info']);
            }
        }
        
        $labelModel = new LabelModel($dbAdapter);
        
        $label = $labelModel->getLabel($labelId);
        $defaultText = $label['default_text'];
    
        $title = 'Edit label';
        $viewModel = new ViewModel([
            'form'        => $form, 
            'action'      => 'edit', 
            'title'       => $title, 
            'defaultText' => $defaultText
        ]);
    
        $viewModel->setTemplate('label/translation/form');
        $viewModel->setTerminal(true);
    
        return $viewModel;
    }
    
    /**
     * deletes existing translation
     */
    public function deleteAction()
    {
        $post = $this->getRequest()->getPost();
        $id = $post['trans_id'];//translation id
        
        if (!$this->getRequest()->isXmlHttpRequest() || !$id) {
            //if is not ajax request or translation id is empty, redirect to index page
            return $this->redirect()->toRoute('label');
        }
    
        $dbAdapter = $this->getDBAdapter();
        $translationModel = new TranslationModel($dbAdapter);
        $translation = $translationModel->deleteTranslation($id);
    
        $this->flashMessenger()->addMessage('Translation is successfully deleted.');
    
        return $this->redirect()->toRoute('label', ['action' => 'info']);
    }
}