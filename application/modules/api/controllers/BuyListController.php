<?php
/**
 * Api_BuyListController
 *
 * @package API
 * @author Jim M. Smith (jim@cash4books.net)
 * @copyright Copyright (c) 2013 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

class Api_BuyListController extends Zend_Rest_Controller
{

    public function indexAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        $json = array('error'=>'403','message'=>'Listing Not Allowed');
        $this->_helper->json($json);
    }

    public function getAction()
    {
        $buyListMapper = new Application_Model_BuyListMapper();
        $buyList = $buyListMapper->findLatestHeaders();

        $vCurrentBuyListItemMapper = new Application_Model_VCurrentBuyListItemMapper();

        $items = array();

        foreach ($vCurrentBuyListItemMapper->fetchAll() as $item) {
            /* @var $item Application_Model_VCurrentBuyListItem */
            if($item->getQuantity() > 0) {
                $items[] = array(
                    'isbn' => $item->getIsbn13(),
                    'qty' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                );
            }
        }
        $json = array(
            'id' => $buyList->getId(),
            'uploadDate' => $buyList->getUploadDate()->getIso(),
            'name' => $buyList->getName(),
            'buyListItems' => $items,
        );
        $this->_helper->json($json);
    }

    public function postAction()
    {

    }

    public function putAction()
    {

    }

    public function deleteAction()
    {

    }
}