<?php
namespace Label\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Label\Model\LabelModel;
use Label\Service\LabelService;
use Zend\Json\Json;
use Label\Form\LabelForm;
use Label\Form\LabelFormValidator;
use Zend\Stdlib\ArrayObject;

class LabelController extends AbstractActionController
{
    /**
     * get database adapter
     */
    private function getDBAdapter()
    {
        return $this->getServiceLocator()->get('db');
    }
    
    public function indexAction()
    {
        return new ViewModel(array(
        ));
    }
    
    /**
     * provides labels data for datatables
     */
    public function ajaxGetLabelsAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //if is not ajax request, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $dbAdapter = $this->getDBAdapter();
        
        $labelModel = new LabelModel($dbAdapter);
        $labelService = new LabelService();
        
        $post = $this->getRequest()->getPost();//get all posts
        
        //this variables are provided by datatables plug-in
        $displayStart  = $post['iDisplayStart'];
        $displayLenght = $post['iDisplayLength'];
        $keyword       = $post['sSearch'];
        $iSortCol      = $post['iSortingCols'];
        $sortDirection = $post['sSortDir_0'];
        $sEcho         = intval($post['sEcho']);
        
        $order = $labelService::getOrderByColumn($iSortCol, $sortDirection);
        
        $labels = $labelModel->getLabels($displayStart, $displayLenght, $keyword, $order);
        $countedLabels = $labelModel->countLabels($keyword);
        
        $labelDataForDT = $labelService->getLabelDataForDT($labels, $countedLabels, $sEcho);
        
        return $this->getResponse()->setContent(Json::encode($labelDataForDT));
    }
    
    /**
     * provides form for inserting new label
     * @return void|\Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $post = $this->getRequest()->getPost();
        $id = $post['id'];//label id
        
        if (!$this->getRequest()->isXmlHttpRequest() || $id) {
            //if is not ajax request or label id is not empty, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $form = new LabelForm();
        
        if ($post['submit']) {
            $dbAdapter = $this->getDBAdapter();
            $formValidator = new LabelFormValidator();
            
            //needed for label name validation.Label name must be unique
            $formValidator->setDbAdapter($dbAdapter);
            $formValidator->data = ['id' => $id,];
            
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($post);
             
            if ($form->isValid()) {
                $data = $form->getData();
                $labelModel = new LabelModel($dbAdapter);
                $labelModel->saveLabel($data);
                
                //show success message
                $this->flashMessenger()->addMessage('Label is successfully added.');
                return $this->redirect()->toRoute('label', ['action' => 'info']);
            }
        }
        
        $title = 'Add new label';
        $viewModel = new ViewModel(['form' => $form, 'action' => 'add', 'title' => $title]);
        $viewModel->setTemplate('label/label/form');
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    /**
     * provides form for editing existing label
     * @return void|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //if is not ajax request, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $post = $this->getRequest()->getPost();
        $id = $post['id'];//label id
        
        if (!$id) {
            //If label id is empty, redirect to the add form
            return $this->redirect()->toRoute('label', ['action' => 'add']);
        }
        
        $form = new LabelForm();
        
        $dbAdapter = $this->getDBAdapter();
        $labelModel = new LabelModel($dbAdapter);
        $label = $labelModel->getLabel($id);
        
        $labelArrObj = new ArrayObject();
        $labelArrObj['id']           = $label['id'];
        $labelArrObj['name']         = $label['name'];
        $labelArrObj['default_text'] = $label['default_text'];
        
        $form->bind($labelArrObj);
        
        if ($post['submit']) {
            $formValidator = new LabelFormValidator();
            
            //needed for label name validation. Label name must be unique
            $formValidator->setDbAdapter($dbAdapter);
            $formValidator->data = ['id' => $id,];
            
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($post);
             
            if ($form->isValid()) {
                $data = $form->getData();
                $labelModel->saveLabel($data);
                
                //show success message
                $this->flashMessenger()->addMessage('Label is successfully edited.');
                return $this->redirect()->toRoute('label', ['action' => 'info']);
            }
        }
        
        $title = 'Edit label';
        $viewModel = new ViewModel(['form' => $form, 'action' => 'edit', 'title' => $title]);
        
        $viewModel->setTemplate('label/label/form');
        $viewModel->setTerminal(true);
        
        return $viewModel;
        
    }
    
    /**
     * deletes existing label and existing translations for this label
     */
    public function deleteAction()
    {
        $post = $this->getRequest()->getPost();
        $id = $post['id'];//label id
        
        if (!$this->getRequest()->isXmlHttpRequest() || !$id) {
            //if is not ajax request or label id is empty, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $dbAdapter = $this->getDBAdapter();
        $labelModel = new LabelModel($dbAdapter);
        $label = $labelModel->deleteLabel($id);
        
        //show success message
        $this->flashMessenger()->addMessage('Label is successfully deleted.');
        return $this->redirect()->toRoute('label', ['action' => 'info']);
    }
    
    /**
     * provides alert messages
     * @return \Zend\View\Model\ViewModel
     */
    public function infoAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //if is not ajax request, redirect to index page
            return $this->redirect()->toRoute('label');
        }
        
        $class = $this->getEvent()->getRouteMatch()->getParam('param');
        //if class is not defined, set default class
        if (!$class) {
            $class = 'success';
        }
        
        $viewModel = new ViewModel(['class' => $class]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
}
