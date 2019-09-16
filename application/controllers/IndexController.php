<?php

/**
 * Index Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: a79314fab8ec5c9671a2587d8373747044d17dbd $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Index Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * Index action placeholder
     */
    public function indexAction()
    {
        // action body
    }

    public function buyListAction()
    {
        if (!is_null($this->_getParam('id'))) {
            $this->_forward('download-buy-list');
            return;
        }
        $buyListMapper = new Application_Model_BuyListMapper();
        $this->view->buyList = $buyListMapper->findLatestHeaders();
    }

    public function downloadBuyListAction()
    {
        $id = $this->_getParam('id');
        $buyListMapper = new Application_Model_BuyListMapper();
        $buyList = $buyListMapper->find($id);
        $this->_helper->layout()->disableLayout();

        $csv = "ISBN10,ISBN13,AMZN Price\n";
        foreach ($buyList->getItems() as $item) {
            /* @var $item Application_Model_BuyListItem */
            $data = array('="' . $item->getIsbn13Raw()->getIsbn10() . '"', '="' . $item->getIsbn13() . '"', number_format($item->getPrice(),2));
            $csv .= implode(',', $data) . "\n";
        }

        $this->view->csv = $csv;
        $this->view->filename = 'amazon-buy-list-' . $buyList->getUploadDate()->toString('M-d-y-H-m-s') . '.csv';

    }

    public function contactAction()
    {
        // action body
    }

    public function termsAction()
    {
        $clickwrapMapper = new Application_Model_ClickwrapMapper();
        $clickwrap = $clickwrapMapper->findLatest();
        $this->view->clickwrap = $clickwrap;
    }

    public function detailsAction()
    {
        $seller = $this->view->user;
        $form = new Application_Form_Seller();

        $data = array(
                'id' => $seller->getId(),
                'name' => $seller->getName(),
                'contactName' => $seller->getContactName(),
                'contactEmail' => $seller->getContactEmail(),
                'contactPhone' => $seller->getContactPhone(),
                'payeeName' => $seller->getPayeeName(),
                'paymentAddress1' => $seller->getPaymentAddress1(),
                'paymentAddress2' => $seller->getPaymentAddress2(),
                'paymentCity' => $seller->getPaymentCity(),
                'paymentState' => $seller->getPaymentState(),
                'paymentZip' => $seller->getPaymentZip(),
                'emailNotify' => $seller->getEmailNotify(),
                );
        $form->populate($data);
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $this->view->success = array('Details updated successfully.');
                $sellerMapper = new Application_Model_SellerMapper();
                $this->view->user->exchangeArray($form->getValues());
                $sellerMapper->save($this->view->user);
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $view->sellerId = $this->view->user->getId();
                $view->companyName = $this->view->user->getName();
                $mail = new Zend_Mail();
                $mail->addTo($this->getInvokeArg('bootstrap')->getOption('updateNotifications'));
                $mail->setSubject('Seller Account Change: ' . $this->view->user->getId());
                $mail->setBodyText($view->render('seller.phtml'));
                $mail->send();
            } else {
                foreach ($form->getElements() as $element) {
                    /* @var $element Zend_Form_Element */
                    if ($element->hasErrors()) {
                        $element->setAttrib('placeholder', null);
                    }
                }
                $this->view->error = array('There were errors saving your data. See the error messages below.');
            }
        }
        $this->view->form = $form;
    }

}
