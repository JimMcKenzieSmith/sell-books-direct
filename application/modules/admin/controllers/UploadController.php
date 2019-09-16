<?php

/**
 * Upload Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 70f277a9c4fb89b5f526afa2603163d7bea09d9e $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Upload Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Admin_UploadController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_helper->redirector('upload');
    }

    public function uploadAction()
    {
        if (!is_null($this->_getParam('id'))) {
            $this->_forward('download-buy-list','index','default');
            return;
        }
        $form = new Application_Form_Upload();
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                if (!$form->file->receive()) {
                    $this->_helper->redirector('upload');
                }
                $location = $form->file->getFileName();
                $dest = realpath($this->getInvokeArg('bootstrap')->getOption('buyerUploadsPath')) . '/' . $this->view->user->getId() . '-' . uniqid() . '.csv';
                copy($location, $dest);
                Zend_Registry::set('location', $location);
                $this->_forward('confirm', null, null, array('form'=>$form));
            }
        }
        $buyListMapper = new Application_Model_BuyListMapper();
        $this->view->buyList = $buyListMapper->findLatestHeaders();
        $this->view->headScript()->appendFile('/js/upload.js');
        $this->view->form = $form;
    }

    public function confirmAction()
    {
        if (!Zend_Registry::isRegistered('location')) {
            $this->_helper->redirector('upload');
        }
        $location = Zend_Registry::get('location');
        $this->view->fileName = basename($location, '.csv');
        $rows = array();
        if (($handle = fopen($location, "r")) !== FALSE) {
            $line = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (!empty($data[0]) || !empty($data[1]) || !empty($data[2])) {
                    $line++;
                    $rows[$line] = $data;
                }
            }
            fclose($handle);
        }
        if (count($rows) < 1) {
            $this->_helper->flashMessenger->setNamespace('error');
            $this->_helper->flashMessenger('Your buy list did not contain any items.');
            $this->_helper->redirector('upload');
        }
        $isbnFilter = new Zend_Filter();
        $isbnFilter->addFilter(new Zend_Filter_Alnum());
        $isbnFilter->addFilter(new Zend_Filter_StringToUpper());
        $isbnFilter->addFilter(new Zend_Filter_Callback('str_pad', array(10,'0',STR_PAD_LEFT)));
        //$isbnValidator = new Zend_Validate_Isbn();
        //$quantityFilter = new Zend_Filter_Digits();
        //$quantityValidator = new Zend_Validate_GreaterThan(0);
        //$priceFilter = new Zend_Filter_StringTrim('\\\\s$');
        //$priceValidator = new Zend_Validate_Float();
        $results = array();
        $i = 0;
        $errorCount = 0;
        if (isset($rows[1][1]) && !is_numeric($rows[1][1])) {
            $i++;
            $results[$i]['originalIsbn'] = $rows[1][0];
            $results[$i]['quantity'] = $rows[1][1];
            $results[$i]['price'] = $rows[1][2];
            $results[$i]['errors'] = array();
            unset($rows[1]);
        }
        foreach ($rows as $row) {
            $errors = array();
            $i++;
            $row[0] = trim($row[0]);
            if (!isset($row[0])) {
                $errors[] = 'No ISBN found.';
                $results[$i]['isbn'] = 'No ISBN found.';
            } else {
                $isbn = $isbnFilter->filter($row[0]);
                $results[$i]['originalIsbn'] = $row[0];
                $len = strlen($isbn);
                if(strpos($row[0],'B') !== false) {
                    $errors[] = 'This is not a valid ISBN.';
                }elseif ($len != 13 && $len != 10) {
                    $errors[] = 'ISBN must be 10 or 13 digits.';
                } elseif($len == 10) {
                    $isbn = $this->convert10to13($isbn);
                }
                $results[$i]['isbn'] = $isbn;
            }
            if (!isset($row[1])) {
                $errors[] = 'No quantity found.';
                $results[$i]['quantity'] = 'No quantity found.';
            } else {
                $quantity = trim($row[1]);
                if (!preg_match("/^[1-9][0-9]{0,3}$/", $quantity)) {
                    $errors[] = 'Quantity not formatted correctly.';
                }
                $results[$i]['quantity'] = $quantity;
            }
            $results[$i]['price'] = '';
            if (!isset($row[2])) {
                $errors[] = 'No price found.';
                $results[$i]['price'] = 'No price found.';
            } else {
                $price = round(trim(str_replace('$','',$row[2])),2);

                if (!preg_match("/^\d+(?:\.\d{1,2})?$/", $price)) {
                    $errors[] = 'Price not formatted correctly.';
                }
                $results[$i]['price'] = $price;
            }
            $results[$i]['errors'] = $errors;
            if (count($errors) > 0) {
                $errorCount++;
            }
        }
        $this->view->results = $results;
        $this->view->errorCount = $errorCount;
        $session = new Zend_Session_Namespace('buy-list');
        $session->results = $results;
    }

    /**
     * Convert legacy ISBN10 to new ISBN13
     *
     * @param $isbn10 string
     *            ISBN10 value
     * @return string
     */
    public function convert10to13($isbn10)
    {
        $isbn13 = '978' . substr($isbn10, 0, 9);
        $sum = 0;
        for ($i = 0; $i < 12; $i ++) {
            if ($i % 2 == 0) {
                $sum += $isbn13{$i};
            } else {
                $sum += 3 * $isbn13{$i};
            }
        }
        $checksum = 10 - ($sum % 10);
        if ($checksum == 10) {
            $checksum = '0';
        }
        $isbn13 = $isbn13 . $checksum;

        $this->_isbn13 = $isbn13;
        return $this->_isbn13;
    }

    public function errorsAction()
    {
        $session = new Zend_Session_Namespace('buy-list');
        if (isset($session->results) && count($session->results) < 1) {
            exit;
        }
        $this->view->results = $session->results;
    }

    public function finishAction()
    {
        $session = new Zend_Session_Namespace('buy-list');
        if (isset($session->results) && count($session->results) > 0) {
            $results = $session->results;
            $intersect = array();
            foreach ($results as $result) {
                if (count($result['errors']) < 1 && isset($result['isbn'])) {
                    $intersect[] = $result;
                }
            }
            if (count($intersect) < 1) {
                $session->unsetAll();
                $this->_helper->flashMessenger->setNamespace('error');
                $this->_helper->flashMessenger('None of the items on your buy list are valid.');
                $this->_helper->redirector('upload');
                return;
            }
            $buyListMapper = new Application_Model_BuyListMapper();
            $buyList = new Application_Model_BuyList();
            $buyList->setUploadDate(Zend_Date::now());
            $buyList->setName('MKNBI'.date('Ymd'));
            foreach ($intersect as $item) {
                $lineItem = new Application_Model_BuyListItem();
                $lineItem->setIsbn13($item['isbn']);
                $lineItem->setQuantity($item['quantity']);
                $lineItem->setPrice($item['price']);
                $buyList->addItem($lineItem);
            }
            $buyListMapper->save($buyList);
            unset($session);
            $sellerMapper = new Application_Model_SellerMapper();
            $activeSellers = $sellerMapper->fetchAllActive();
            $view = new Zend_View();
            $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
            foreach ($activeSellers as $seller) {
                /* @var $seller Application_Model_Seller */
                $view->seller = $seller;
	            $mail = new Zend_Mail();
	            $mail->setSubject('New Amazon Buy List');
                $mail->addTo($seller->getContactEmail(), $seller->getContactName());
                $mail->setBodyText($view->render('new-buy-list.phtml'));
                $mail->send();
            }
        } else {
            $this->_helper->redirector('upload');
        }
    }

}
