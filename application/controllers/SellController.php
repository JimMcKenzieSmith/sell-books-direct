<?php

/**
 * Sell Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: b05062c93cc6c556ca31ea03c1b34e6d05e7a40a $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Sell Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class SellController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_helper->redirector('upload');
    }

    public function uploadAction()
    {
        $form = new Application_Form_Upload();
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        $this->view->message = $request->getParam('error');
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $invoiceTable = new Application_Model_DbTable_Invoices();
                // delete this seller's old quote invoice
                $invoiceTable->delete(array(
                        'sellerId = ?' => $this->view->user->getId(),
                        'invoiceStatus = 0',
                        ));
                // also delete other sellers' invoices that are more than $purge_quote_invoices_after_n_minutes
                $purge_quote_invoices_after_n_minutes = 240;
                $invoiceTable->delete(array(
                        'invoiceStatus = 0',
                        'TIMESTAMPDIFF(MINUTE, createTs, NOW()) > ?' => $purge_quote_invoices_after_n_minutes,
                        ));
                if (!$form->file->receive()) {
                    $this->_helper->redirector('upload');
                }
                $location = $form->file->getFileName();
                $dest = realpath($this->getInvokeArg('bootstrap')->getOption('sellerUploadsPath')) . '/' . $this->view->user->getId() . '-' . uniqid() . '.csv';
                copy($location, $dest);
                Zend_Registry::set('location', $location);
                $this->_forward('confirm', null, null, array('form'=>$form));
            }
        }
        $this->view->headScript()->appendFile('/js/upload.js');
        $this->view->sellerUploadRowLimit = $this->getInvokeArg('bootstrap')->getOption('sellerUploadRowLimit');
        $this->view->form = $form;
    }

    public function confirmAction()
    {
        $rowLimit = intval($this->getInvokeArg('bootstrap')->getOption('sellerUploadRowLimit'));
        $startTime = $this->getMicroTimeFloat();
        $this->getLog()->log('Start confirmAction().', Zend_Log::INFO);

        if (!Zend_Registry::isRegistered('location')) {
            $this->_helper->redirector('upload');
        }
        $location = Zend_Registry::get('location');
        $rows = array();
        ini_set("auto_detect_line_endings", true); // Added for Macintosh CR/LF support
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
        ini_set("auto_detect_line_endings", false); // Added for Macintosh CR/LF support
        $numRows = count($rows);
        if ($numRows < 1) {
            $session = new Zend_Session_Namespace('sell');
            $session->unsetAll();
            $this->_helper->flashMessenger->setNamespace('error');
            $this->_helper->flashMessenger('Your sell list did not contain any items.');
            $this->_helper->redirector('upload');
            return;
        }
        if ($numRows > $rowLimit) {
            $session = new Zend_Session_Namespace('sell');
            $session->unsetAll();
            $this->_helper->flashMessenger->setNamespace('alert');
            $this->_helper->flashMessenger('The system currently has a '.number_format($rowLimit).' row limit. Please break your inventory into multiple files. Thank you.');
            $this->_helper->redirector('upload');
            return;
        }

        $isbnFilter = new Zend_Filter();
        $isbnFilter->addFilter(new Zend_Filter_Alnum());
        $isbnFilter->addFilter(new Zend_Filter_StringToUpper());
        $isbnFilter->addFilter(new Zend_Filter_Callback('str_pad', array(10,'0',STR_PAD_LEFT)));
        //$isbnValidator = new Zend_Validate_Isbn();
        //$quantityFilter = new Zend_Filter_Digits();
        //$quantityValidator = new Zend_Validate_GreaterThan(0);
        $priceFilter = new Zend_Filter_StringTrim('\\\\s$');
        //$priceValidator = new Zend_Validate_Float();
        $results = array();
        $i = 0;
        $errorCount = 0;
        $hasMinimumSellPrices = false;
        if (isset($rows[1][0]) && isset($rows[1][1]) && !is_numeric($rows[1][1])) {
            $i++;
            $results[$i]['originalIsbn'] = $rows[1][0];
            $results[$i]['quantity'] = $rows[1][1];
            if(isset($rows[1][2])) {
                $results[$i]['price'] = $rows[1][2];
            }
            else
            {
                $results[$i]['price'] = '';
            }
            $results[$i]['errors'] = array();
            unset($rows[1]);
        }
        $errors = array();
        foreach ($rows as $row) {
            $i++;
            if (!isset($row[0])) {
                $errors[] = 'No ISBN found.';
                $results[$i]['isbn'] = 'No ISBN found.';
            } else {
                $isbn = $isbnFilter->filter($row[0]);
                $results[$i]['originalIsbn'] = $isbn;
                $len = strlen($isbn);
                if ($len != 13 && $len != 10) {
                    $errors[] = 'ISBN must be 10 or 13 digits';
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
                    $session = new Zend_Session_Namespace('sell');
                    $session->unsetAll();
                    $this->_helper->flashMessenger->setNamespace('error');
                    $this->_helper->flashMessenger('An incorrect value of "'.$quantity.'" was detected in your quantity column, on line '.$i.'. Please correct your file and try again.');
                    $this->_helper->redirector('upload');
                    return;
                }
                $results[$i]['quantity'] = $quantity;
            }

            if (!empty($row[2])) {
                $price = $priceFilter->filter($row[2]);
                if (!preg_match("/^\d*\.?\d*$/", $price)) {
                    $session = new Zend_Session_Namespace('sell');
                    $session->unsetAll();
                    $this->_helper->flashMessenger->setNamespace('error');
                    $this->_helper->flashMessenger('An incorrect value of "'.$price.'" was detected in your [minimum sell price] column, on line '.$i.'. Please correct your file and try again.');
                    $this->_helper->redirector('upload');
                    return;
                }
                $hasMinimumSellPrices = true;
                $results[$i]['price'] = $price;
            } else {
                $results[$i]['price'] = '';
            }
            $results[$i]['errors'] = $errors;
            if (count($errors) > 0) {
                $errorCount++;
                $errors = array();
            }
        }
        $endTime = $this->getMicroTimeFloat();
        $this->getLog()->log('End confirmAction(). Processed '.$i.' rows in '.($endTime-$startTime).' seconds.', Zend_Log::INFO);
        $this->view->results = $results;
        $this->view->errorCount = $errorCount;
        $this->view->hasMinimumSellPrices = $hasMinimumSellPrices;
        $this->view->headScript()->appendFile('/js/confirm.js');
        $session = new Zend_Session_Namespace('sell');
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

    /**
     * Grab the logger
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $log = $bootstrap->getResource('Log');
        return $log;
    }

    /**
     * Get current time as a float.
     *
     * @return float
     */
    public function getMicroTimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function errorsAction()
    {
        $session = new Zend_Session_Namespace('sell');
        if (isset($session->results) && count($session->results) < 1) {
            exit;
        }
        $this->view->results = $session->results;
    }

    public function waitAction()
    {
        $this->view->headMeta()->appendHttpEquiv('Refresh', '5');
    }

    public function generateAction()
    {
        $startTime = $this->getMicroTimeFloat();
        $this->getLog()->log('Start generateAction().', Zend_Log::INFO);
        $this->view->error = array();
        $session = new Zend_Session_Namespace('sell');
        if (isset($session->results) && count($session->results) > 0) {
            $filterByMinimumPrice = false;
            $filterByMinimumPct = false;
            $filterPriceVariance = 0;
            $filterPctVariance = 0;
            if ($this->getRequest()->getParam('filterByMinimumPrice') !== null) {
                $filterByMinimumPrice = true;
                $filterPriceVariance = floatval($this->getRequest()->getParam('filterPriceVariance'));
            }
            if ($this->getRequest()->getParam('filterByMinimumPct') !== null) {
                $filterByMinimumPct = true;
                $filterPctVariance = floatval($this->getRequest()->getParam('filterPctVariance'));
            }

            $results = $session->results;
            $lockfile = sys_get_temp_dir() . '/sell-books-direct-upload.lock';
            if (file_exists($lockfile)) {
                $timeout = file_get_contents($lockfile);
                if ($timeout >= time()) {
                    $this->_forward('wait');
                    return;
                } else {
                    unlink($lockfile);
                }
            }
            file_put_contents($lockfile, (time()+10));
            $buyListMapper = new Application_Model_BuyListMapper();
            $buyList = $buyListMapper->findLatest();
            $buyListItems = $buyList->getItems(); // this row of code, to get the items, is the biggest performance hit!
            $intersect = array();
            foreach ($results as $result) {
                if (isset($result['isbn']) && count($result['errors']) < 1 && isset($buyListItems[$result['isbn']])) {

                    // if the filter by minimum price is on, and it is not within the variance, don't include in the intersect
                    if($filterByMinimumPrice && !empty($result['price']) && (($result['price'] - $filterPriceVariance) > $buyListItems[$result['isbn']]->getPrice())) {
                        // don't include in the intersect, because it is filtered out
                    }
                    elseif($filterByMinimumPct && !empty($result['price']) && (($result['price'] - ($filterPctVariance/100 * $result['price'])) > $buyListItems[$result['isbn']]->getPrice())) {
                        // don't include it in the intersect, because it is filtered out
                    }
                    else {
                        $intersect[] = $result;
                    }
                }
            }
            unset($results);
            if (count($intersect) < 1) {
                $session->unsetAll();
                $this->_helper->flashMessenger->setNamespace('alert');
                $this->_helper->flashMessenger('None of the items on your sell list match the buy list at this time.');
                $this->_helper->redirector('upload');
                return;
            }
            $invoiceMapper = new Application_Model_InvoiceMapper();
            $invoice = new Application_Model_Invoice();
            $original = array();
            foreach ($intersect as $item) {
                $select = $invoiceMapper->getDbTable()->getAdapter()->select();
                $select->from('invoice',array());
                $select->from('invoiceItem', array('sum(quantity)'));
                $select->where('id = invoiceId');
                $select->where('invoiceStatus != 6');
                $select->where('buyListId = ?', $buyList->getId());
                $select->where('isbn13 = ?', $item['isbn']);
                $count = (int) $invoiceMapper->getDbTable()->getAdapter()->fetchOne($select);
                $remaining = ($buyListItems[$item['isbn']]->getQuantity()-$count);
                if ($remaining > 0) {
                    if (isset($item['price']) && $item['price'] > $buyListItems[$item['isbn']]->getPrice()) {
                        $quantity = 0;
                        $item['max'] = $item['quantity'];
                        $buyListPrice = $buyListItems[$item['isbn']]->getPrice();
                        $difference = $item['price'] - $buyListPrice;
                        $pctDifference = number_format(round(($buyListPrice - $item['price'])/$item['price']*100*-1, 1), 1);
                        $item['message'] = 'Does not meet your minimum: $ ' . number_format($item['price'], 2) . '.<br />Difference is: $ '.number_format($difference, 2).' or '.$pctDifference.'&#37;. ';
                    }

                    if ($item['quantity'] > $remaining) {

                        // if the message is empty, it means there is no minimum price problem, so we can set the quantity to "remaining".
                        // otherwise, leave it at 0, from above
                        if(empty($item['message'])) {
                            $quantity = $remaining;
                        }
                        $item['max'] = $remaining;

                        if(isset($item['message'])) {
                            $item['message'] .= 'Quantity lowered from ' . $item['quantity'] . '.';
                        } else {
                            $item['message'] = 'Quantity lowered from ' . $item['quantity'] . '.';
                        }
                    }

                    // if message is empty, that means there are no errors... so let the quantity be what the seller uploaded
                    if(empty($item['message'])) {
                        $quantity = $item['quantity'];
                        $item['max'] = $item['quantity'];
                    }
                    $lineItem = new Application_Model_InvoiceItem();
                    $lineItem->setIsbn13Raw($buyListItems[$item['isbn']]->getIsbn13Raw());
                    $lineItem->setQuantity($quantity);
                    $lineItem->setPrice($buyListItems[$item['isbn']]->getPrice());
                    $invoice->addLineItem($lineItem);
                    unset($item['errors']);
                    $original[$item['isbn']] = $item;
                }
            }
            if ($invoice->getTotalLines() < 1) {
                $session->unsetAll();
                $this->_helper->flashMessenger->setNamespace('alert');
                $this->_helper->flashMessenger('None of the items on your sell list are on the buy list at this time.');
                $this->_helper->redirector('upload');
                return;
            }
            $invoice->setBuyListId($buyList->getId());
            $invoice->setInvoiceStatus($invoice::STATUS_QUOTE);
            $invoice->setSellerInvoiceNumber(uniqid());
            $invoice->setShipDate(Zend_Date::now());
            $invoice->setSellerId($this->view->user->getId());
            $invoiceMapper->save($invoice);
            $session->invoice = $invoice;
            $session->original = $original;
            unset($session->results);
            unlink($lockfile);
            $minQuantity = $this->getInvokeArg('bootstrap')->getOption('minQuantity');
            if($invoice->isMkzInvoice()) {
                $minQuantity = $this->getInvokeArg('bootstrap')->getOption('minMkzQuantity');
            }
            if ($invoice->getTotalItems() < $minQuantity) {
                $this->view->error[] = 'Invoice must contain at least ' . $minQuantity . ' units to be accepted. Current quantity is: ' . $invoice->getTotalItems();
            }
        }
        if (isset($session->invoice) && $session->invoice instanceof Application_Model_Invoice) {
            $invoiceMapper = new Application_Model_InvoiceMapper();
            $invoice = $session->invoice;
            /* @var $invoice Application_Model_Invoice */
            $original = $session->original;
            $this->view->invoice = $invoice;
            $this->view->original = $original;
            $form = new Application_Form_Generate();
            $form->getElement('sellerInvoiceNumber')->getValidator('Db_NoRecordExists')->setExclude('sellerId = ' . $this->view->user->getId());
            if ($this->getRequest()->isPost()) {
                if ($this->_getParam('submit') == 'back') {
                    $invoiceMapper->delete($invoice);
                    $session->unsetAll();
                    $this->_helper->redirector('upload');
                    return;
                }
                $request = $this->getRequest();
                /* @var $request Zend_Controller_Request_Http */
                $post = $request->getPost();
                if ($this->_getParam('submit')) {
                    foreach ($invoice->getLineItems() as $item) {
                        $isbn = $item->getIsbn13();
                        $quantity = $this->_getParam($isbn, 0);
                        if ($quantity > $original[$isbn]['max']) {
                            $quantity = $original[$isbn]['max'];
                            $this->view->error['max'] = 'One or more of your quantities was set higher than the amount we wish to purchase. They have been adjusted down automatically.';
                        } elseif ($quantity < 0) {
                            $quantity = 0;
                            $this->view->error['negative'] = 'One or more of your quantities was set to a negative number. They have been set to 0 automatically.';
                        }
                        $item->setQuantity($quantity);
                        $invoice->addLineItem($item);
                    }
                    $minQuantity = $this->getInvokeArg('bootstrap')->getOption('minQuantity');
                    if($invoice->isMkzInvoice()) {
                        $minQuantity = $this->getInvokeArg('bootstrap')->getOption('minMkzQuantity');
                    }
                    if ($invoice->getTotalItems() < $minQuantity) {
                        $this->view->error[] = 'Invoice must contain at least ' . $minQuantity . ' units to be accepted. Current quantity is: ' . $invoice->getTotalItems();
                    }
                }
                if ($this->_getParam('submit') == 'update') {
                    if (empty($post['sellerInvoiceNumber'])) {
                        unset($post['sellerInvoiceNumber']);
                    }
                    if (empty($post['shipDate'])) {
                        unset($post['shipDate']);
                    }
                    $form->isValidPartial($post);
                    $invoiceMapper->save($invoice);
                    $this->view->success = array('Quantities updated successfully. Current quantity is: ' . $invoice->getTotalItems());
                } elseif ($this->_getParam('submit') == 'submit') {
                    $invoiceMapper->save($invoice);
                    if ($form->isValid($post)) {
                        $minQuantity = $this->getInvokeArg('bootstrap')->getOption('minQuantity');
                        if($invoice->isMkzInvoice()) {
                            $minQuantity = $this->getInvokeArg('bootstrap')->getOption('minMkzQuantity');
                        }
                        if ($invoice->getTotalItems() < $minQuantity) {
                            $this->view->error[] = 'Invoice must contain at least ' . $minQuantity . ' units to be accepted. Current quantity is: ' . $invoice->getTotalItems();
                        } else {
                            $buyListItems = array();
                            $invoiceItemTable = new Application_Model_DbTable_InvoiceItems();
                            foreach ($invoice->getLineItems() as $item) {
                                if ($item->getQuantity() > 0) {
                                    $buyListItems[] = $item;
                                } else {
                                    $invoiceItemTable->delete(array(
                                            'invoiceId = ?' => $invoice->getId(),
                                            'isbn13 = ?' => $item->getIsbn13(),
                                            ));
                                }
                            }
                            $invoice->setLineItems($buyListItems);
                            $values = $form->getValues();
                            $values['shipDate'] = new Zend_Date($values['shipDate'], 'M/d/y');
                            $invoice->exchangeArray($values);
                            if ($this->view->user->getSellerStatus() == Application_Model_Seller::STATUS_ACTIVATED_MANUAL) {
                                $invoiceStatus = $invoice::STATUS_PENDING_APPROVAL;
                                $action = new Application_Model_InvoiceAction();
                                $action->setInvoiceId($invoice->getId());
                                $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                                $action->setWhat($action::WHAT_CREATED_MANUAL);
                                $action->setActionDate(Zend_Date::now());
                                $invoice->addAction($action);
                            } elseif ($this->view->user->getSellerStatus() == Application_Model_Seller::STATUS_ACTIVATED_AUTO) {
                                $invoiceStatus = $invoice::STATUS_APPROVED;
                                $action = new Application_Model_InvoiceAction();
                                $action->setInvoiceId($invoice->getId());
                                $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                                $action->setWhat($action::WHAT_CREATED_AUTO);
                                $action->setActionDate(Zend_Date::now());
                                $invoice->addAction($action);
                                $action = new Application_Model_InvoiceAction();
                                $action->setInvoiceId($invoice->getId());
                                $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                                $action->setWhat($action::WHAT_APPROVED);
                                $action->setActionDate(Zend_Date::now());
                                $invoice->addAction($action);
                            } else {
                                $invoiceMapper->delete($invoice);
                                $session->unsetAll();
                                $this->_helper->redirector('upload');
                                return;
                            }
                            $view = new Zend_View();
                            $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                            $view->invoiceId = $invoice->getId();
                            $view->sellerInvoiceNumber = $invoice->getSellerInvoiceNumber();
                            $sellerMapper = new Application_Model_SellerMapper();
                            $seller = $sellerMapper->find($invoice->getSellerId());
                            $view->companyName = $seller->getName();
                            $view->sellerId = $invoice->getSellerId();
                            $view->dateTimeSubmitted = date('l, F j, Y').' at '.date('h:i A T');
                            $view->invoiceStatus = $invoice->getInvoiceStatusAsText($invoiceStatus);
                            $view->totalItems = $invoice->getTotalItems();
                            $view->totalPrice = $invoice->getTotalPrice();

                            $mail = new Zend_Mail();
                            $mail->addTo($this->getInvokeArg('bootstrap')->getOption('updateNotifications'));
                            $subject = 'New invoice on Sell Books Direct';
                            if($invoiceStatus == $invoice::STATUS_PENDING_APPROVAL) {
                                $subject .= ': awaiting manual approval';
                            }
                            $mail->setSubject($subject);
                            $mail->setBodyText($view->render('new-invoice.phtml'));
                            $mail->send();
                            $invoice->setInvoiceStatus($invoiceStatus);
                            $invoiceMapper->save($invoice);
                            $session->unsetAll();
                            $endTime = $this->getMicroTimeFloat();
                            $this->getLog()->log('End generateAction(), finished. Processed '.count($buyListItems).' rows in '.($endTime-$startTime).' seconds.', Zend_Log::INFO);
                            $this->_helper->redirector('finish',null,null,array('id' => $invoice->getId()));
                        }
                    }
                }
            }
            if ($form->isErrors()) {
                $this->view->error[] = 'There was a problem with your Ship Date or Invoice Number. <a href="#invoiceDetails">Go To Bottom of Page</a>';
            }
            $session->invoice = $invoice;
            $this->view->headScript()->appendFile('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js');
            if (Zend_Date::now()->compareWeekday('Wednesday') === 0) {
                $days = 7;
            } elseif (Zend_Date::now()->compareWeekday('Thursday') === 0) {
                $days = 7;
            } elseif (Zend_Date::now()->compareWeekday('Friday') === 0) {
                $days = 7;
            } elseif (Zend_Date::now()->compareWeekday('Saturday') === 0) {
                $days = 6;
            } else {
                $days = 5;
            }
            $endTime = $this->getMicroTimeFloat();
            $this->getLog()->log('End generateAction(), step 3. Processed '.count($invoice->getLineItems()).' rows in '.($endTime-$startTime).' seconds.', Zend_Log::INFO);
            $this->view->headScript()->appendScript('$(function(){$("#shipDate").datepicker({ minDate: 0, maxDate: "+' . $days . 'D" });});');
            $this->view->headLink()->appendStylesheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/south-street/jquery-ui.css');
            $this->view->form = $form;
        } else {
            $this->_helper->redirector('upload');
        }
    }

    public function finishAction()
    {
        if (is_null(($this->view->id = $this->_getParam('id')))) {
            $this->_helper->redirector('upload');
        }
        $this->view->downloadLink = $this->getInvokeArg('bootstrap')->getOption('shippingInstructions');
        $this->view->downloadLinkForNewSellers = $this->getInvokeArg('bootstrap')->getOption('shippingInstructions');
    }

}
