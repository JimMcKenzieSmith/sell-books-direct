<?php

/**
 * Index Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 83cc0bbe05b91b4e88a0ba2be47e774f159c8f3c $
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
class Admin_IndexController extends Zend_Controller_Action
{

    /**
     * Index action placeholder
     */
    public function indexAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $pending = $invoiceMapper->fetchAllByStatus(Application_Model_Invoice::STATUS_PENDING_APPROVAL);
        $approved = $invoiceMapper->fetchAllByStatus(Application_Model_Invoice::STATUS_APPROVED);
        $received = $invoiceMapper->fetchAllByStatus(Application_Model_Invoice::STATUS_RECEIVED);
        $processed = $invoiceMapper->fetchAllByStatus(Application_Model_Invoice::STATUS_PROCESSED);
        $sellerMapper = new Application_Model_SellerMapper();
        $sellers = array();
        foreach (array($pending, $approved, $received, $processed) as $invoices) {
            foreach ($invoices as $invoice) {
                if (!isset($sellers[$invoice->getSellerId()])) {
                    $sellers[$invoice->getSellerId()] = $sellerMapper->find($invoice->getSellerId());
                }
            }
        }
        $this->view->sellers = $sellers;
        $this->view->pending = $pending;
        $this->view->approved = $approved;
        $this->view->received = $received;
        $this->view->processed = $processed;
    }

}
