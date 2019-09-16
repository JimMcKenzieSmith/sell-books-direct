<?php

/**
 * Invoice Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @version $Id: 712f1879473f10b18714d9d0c70bcf301716a577 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Invoice Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_InvoiceMapper extends My_ModelMapperAbstract
{

    protected $_dbTableString = 'Application_Model_DbTable_Invoices';

    public function find($id)
    {
        $results = $this->getDbTable()->find($id);
        if (count($results) < 1) {
            return null;
        }
        return $this->modelFromRow($results->current());
    }

    public function modelFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        $invoice = new Application_Model_Invoice($row->toArray());
        $invoiceItemTable = new Application_Model_DbTable_InvoiceItems();
        $itemRows = $row->findDependentRowset($invoiceItemTable);
        foreach ($itemRows->toArray() as $item) {
            $itemModel = new Application_Model_InvoiceItem($item);
            $invoice->addLineItem($itemModel);
        }
        $invoiceActionTable = new Application_Model_DbTable_InvoiceActions();
        $actionRows = $row->findDependentRowset($invoiceActionTable);
        foreach ($actionRows->toArray() as $actionRow) {
            $action = new Application_Model_InvoiceAction($actionRow);
            $invoice->addAction($action);
        }
        $invoiceNoteTable = new Application_Model_DbTable_InvoiceNotes();
        $noteRows = $row->findDependentRowset($invoiceNoteTable);
        foreach ($noteRows->toArray() as $noteRow) {
            $note = new Application_Model_InvoiceNote($noteRow);
            $invoice->addNote($note);
        }
        return $invoice;
    }

    public function save(Application_Model_Invoice $invoice)
    {
        $data = array(
                'id' => $invoice->getId(),
                'sellerId' => $invoice->getSellerId(),
                'sellerInvoiceNumber' => $invoice->getSellerInvoiceNumber(),
                'shipDate' => $invoice->getShipDate()->toString('y-M-d'),
                'invoiceStatus' => $invoice->getInvoiceStatus(),
                'buyListId' => $invoice->getBuyListId(),
                );
        $invoiceItemTable = new Application_Model_DbTable_InvoiceItems();
        if (is_null($invoice->getId())) {
            $invoice->setId(uniqid());
            $data['id'] = $invoice->getId();
            $this->getDbTable()->insert($data);
            foreach ($invoice->getLineItems() as $item) {
                /* @var $item Application_Model_InvoiceItem */
                $data = array(
                        'invoiceId' => $item->getInvoiceId(),
                        'isbn13' => $item->getIsbn13(),
                        'quantity' => $item->getQuantity(),
                        'price' => $item->getPrice(),
                        );
                $invoiceItemTable->insert($data);
            }
        } else {
            $rows = $this->getDbTable()->update($data, array('id = ?' => $invoice->getId()));
            foreach ($invoice->getLineItems() as $item) {
                /* @var $item Application_Model_InvoiceItem */
                $data = array(
                        'invoiceId' => $item->getInvoiceId(),
                        'isbn13' => $item->getIsbn13(),
                        'quantity' => $item->getQuantity(),
                        'price' => $item->getPrice(),
                );
                $invoiceItemTable->update($data, array('invoiceId = ?' => $data['invoiceId'], 'isbn13 = ?' => $data['isbn13']));
            }
        }
        $invoiceActionTable = new Application_Model_DbTable_InvoiceActions();
        foreach ($invoice->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            $data = array(
                    'invoiceId' => $action->getInvoiceId(),
                    'actionDate' => $action->getActionDate()->toString('y-M-d H:m:s'),
                    'who' => $action->getWho(),
                    'what' => $action->getWhat(),
                    );
            if (is_null($action->getId())) {
                $id = $invoiceActionTable->insert($data);
                $action->setId($id);
            } else {
                $invoiceActionTable->update($data, array('id = ?' => $action->getId()));
            }
        }
        $invoiceNoteTable = new Application_Model_DbTable_InvoiceNotes();
        foreach ($invoice->getNotes() as $note) {
            /* @var $note Application_Model_InvoiceNote */
            $data = array(
                    'invoiceId' => $note->getInvoiceId(),
                    'date' => $note->getDate()->toString('y-M-d H:m:s'),
                    'note' => $note->getNote(),
                    'who' => $note->getWho(),
                    );
            if (is_null($note->getId())) {
                $id = $invoiceNoteTable->insert($data);
                $note->setId($id);
            } else {
                $invoiceNoteTable->update($data, array('id = ?' => $note->getId()));
            }
        }
        return $invoice;
    }

    public function fetchAllBySeller($sellerId)
    {
        $results = $this->getDbTable()->fetchAll(array('invoiceStatus != ?'=>Application_Model_Invoice::STATUS_QUOTE,'sellerId = ?' => $sellerId));
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }

    public function delete(Application_Model_Invoice $invoice)
    {
        return $this->getDbTable()->delete(array('id = ?' => $invoice->getId()));
    }

    public function fetchAll()
    {
        $results = $this->getDbTable()->fetchAll();
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }

    public function fetchAllCreated()
    {
        $results = $this->getDbTable()->fetchAll(array('invoiceStatus != ?'=>Application_Model_Invoice::STATUS_QUOTE));
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }
    /**
     * @param int $days
     */
    public function fetchAllCreatedPastNDays($days)
    {
        $results = $this->getDbTable()->fetchAll(
            array('invoiceStatus != ?'=>Application_Model_Invoice::STATUS_QUOTE,
                'createTs > ?' => date('Y-m-d', strtotime('-'.$days.' days'))),
            array('createTs desc'));
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }

    public function fetchAllCreatedAfter5PmYesterday()
    {
        $results = $this->getDbTable()->fetchAll(
            array('invoiceStatus != ?'=>Application_Model_Invoice::STATUS_QUOTE,
                'createTs > ?' => date('Y-m-d 19:00:00', strtotime('-1 day'))),  // we run this cutoff at 19:00:00 Texas time to correspond with a 17:00:00 Pacific time email report that goes to Amazon
            array('createTs desc'));
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }

    public function fetchAllByStatus($status)
    {
        $results = $this->getDbTable()->fetchAll(array('invoiceStatus = ?'=>$status));
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }

    public function searchByInvoiceNumber($term)
    {
        $results = $this->getDbTable()->fetchAll(array('sellerInvoiceNumber LIKE ?' => '%'.$term.'%'));
        if (count($results) < 1) {
            return array();
        }
        $invoices = array();
        foreach ($results as $row) {
            $invoices[] = $this->modelFromRow($row);
        }
        return $invoices;
    }

}
