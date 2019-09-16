<?php

/**
 * Application Bootstrap
 *
 * @package SellBooksDirect
 * @subpackage Bootstrap
 * @version $Id: 2f6a59db48cdcae37fdf754db44379a49e746aa3 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Application Bootstrap
 *
 * @package SellBooksDirect
 * @subpackage Bootstrap
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Register our Zend_Log resource as a PHP error handler
     */
    public function _initLogPhpErrors()
    {
        $this->bootstrap('log');
        if ($this->hasResource('log')) {
            $log = $this->getResource('log');
            /* @var $log Zend_Log */
            $log->registerErrorHandler();
            Zend_Registry::set('Log', $log);
        }
    }

    /**
     * Set the headTitle
     */
    public function _initHeadTitle()
    {
        $this->bootstrap('view');
        $viewOptions = $headTitle = $this->getOption('view');
        if (isset($viewOptions['headTitle'])) {
            $headTitle = $viewOptions['headTitle'];
            $this->getResource('view')->headTitle($headTitle);
        }
    }

    public function _initAcl()
    {
        $acl = new Zend_Acl();
        // roles
        $guest = new Zend_Acl_Role('guest');
        $acl->addRole($guest);
        $sellerUnactivated = new Zend_Acl_Role('unactivated');
        $acl->addRole($sellerUnactivated, $guest);
        $sellerActivatedManual = new Zend_Acl_Role('activatedManual');
        $acl->addRole($sellerActivatedManual, $guest);
        $sellerActivatedAuto = new Zend_Acl_Role('activatedAuto');
        $acl->addRole($sellerActivatedAuto, $sellerActivatedManual);
        $sellerDeactivated = new Zend_Acl_Role('deactivated');
        $acl->addRole($sellerDeactivated, $sellerActivatedManual);
        $adminRole = new Zend_Acl_Role('admin');
        $acl->addRole($adminRole);
        $adminProcessor = new Zend_Acl_Role('processor');
        $acl->addRole($adminProcessor, $adminRole);
        $adminBuyer = new Zend_Acl_Role('buyer');
        $acl->addRole($adminBuyer, $adminRole);
        $adminSuper = new Zend_Acl_Role('superAdmin');
        $acl->addRole($adminSuper, array($adminProcessor, $adminBuyer));
        // resources
        $login = new Zend_Acl_Resource('mvc:default.login.index');
        $acl->addResource($login);
        $loginLogin = new Zend_Acl_Resource('mvc:default.login.login');
        $acl->addResource($loginLogin, $login);
        $loginLogout = new Zend_Acl_Resource('mvc:default.login.logout');
        $acl->addResource($loginLogout, $login);
        $loginForgot = new Zend_Acl_Resource('mvc:default.login.forgot');
        $acl->addResource($loginForgot, $login);
        $loginRegister = new Zend_Acl_Resource('mvc:default.login.register');
        $acl->addResource($loginRegister, $login);
        $loginClickwrap = new Zend_Acl_Resource('mvc:default.login.clickwrap');
        $acl->addResource($loginClickwrap, $login);
        $loginChange = new Zend_Acl_Resource('mvc:default.login.change');
        $acl->addResource($loginChange, $login);
        $index = new Zend_Acl_Resource('mvc:default.index.index');
        $acl->addResource($index);
        $contact = new Zend_Acl_Resource('mvc:default.index.contact');
        $acl->addResource($contact, $index);
        $terms = new Zend_Acl_Resource('mvc:default.index.terms');
        $acl->addResource($terms, $index);
        $buyList = new Zend_Acl_Resource('mvc:default.index.buy-list');
        $acl->addResource($buyList, $index);
        $invoice = new Zend_Acl_Resource('mvc:default.invoice.index');
        $acl->addResource($invoice, $index);
        $invoiceList = new Zend_Acl_Resource('mvc:default.invoice.list');
        $acl->addResource($invoiceList, $invoice);
        $invoiceView = new Zend_Acl_Resource('mvc:default.invoice.view');
        $acl->addResource($invoiceView, $invoice);
        $invoiceDownload = new Zend_Acl_Resource('mvc:default.invoice.download');
        $acl->addResource($invoiceDownload, $invoice);
        $invoiceCancel = new Zend_Acl_Resource('mvc:default.invoice.cancel');
        $acl->addResource($invoiceCancel, $invoice);
        $details = new Zend_Acl_Resource('mvc:default.index.details');
        $acl->addResource($details, $index);
        $sell = new Zend_Acl_Resource('mvc:default.sell.index');
        $acl->addResource($sell, $index);
        $sellUpload = new Zend_Acl_Resource('mvc:default.sell.upload');
        $acl->addResource($sellUpload, $sell);
        $sellConfirm = new Zend_Acl_Resource('mvc:default.sell.confirm');
        $acl->addResource($sellConfirm, $sell);
        $sellErrors = new Zend_Acl_Resource('mvc:default.sell.errors');
        $acl->addResource($sellErrors, $sell);
        $sellGenerate = new Zend_Acl_Resource('mvc:default.sell.generate');
        $acl->addResource($sellGenerate, $sell);
        $sellWait = new Zend_Acl_Resource('mvc:default.sell.wait');
        $acl->addResource($sellWait, $sellGenerate);
        $sellFinish = new Zend_Acl_Resource('mvc:default.sell.finish');
        $acl->addResource($sellFinish, $sell);
        $admin = new Zend_Acl_Resource('mvc:admin');
        $acl->addResource($admin);
        $adminLogin = new Zend_Acl_Resource('mvc:admin.login.index');
        $acl->addResource($adminLogin, $admin);
        $adminLoginLogin = new Zend_Acl_Resource('mvc:admin.login.login');
        $acl->addResource($adminLoginLogin, $adminLogin);
        $adminLoginLogout = new Zend_Acl_Resource('mvc:admin.login.logout');
        $acl->addResource($adminLoginLogout, $adminLogin);
        $adminIndex = new Zend_Acl_Resource('mvc:admin.index.index');
        $acl->addResource($adminIndex, $admin);
        $adminSellers = new Zend_Acl_Resource('mvc:admin.sellers.index');
        $acl->addResource($adminSellers, $admin);
        $adminSellersList = new Zend_Acl_Resource('mvc:admin.sellers.list');
        $acl->addResource($adminSellersList, $adminSellers);
        $adminSellersView = new Zend_Acl_Resource('mvc:admin.sellers.view');
        $acl->addResource($adminSellersView, $adminSellers);
        $adminSellersEdit = new Zend_Acl_Resource('mvc:admin.sellers.edit');
        $acl->addResource($adminSellersEdit, $adminSellers);
        $adminSellersAdd = new Zend_Acl_Resource('mvc:admin.sellers.add');
        $acl->addResource($adminSellersAdd, $adminSellers);
        $adminSellersSearch = new Zend_Acl_Resource('mvc:admin.sellers.search');
        $acl->addResource($adminSellersSearch, $adminSellers);
        $adminInvoices = new Zend_Acl_Resource('mvc:admin.invoices.index');
        $acl->addResource($adminInvoices, $admin);
        $adminInvoicesList = new Zend_Acl_Resource('mvc:admin.invoices.list');
        $acl->addResource($adminInvoicesList, $adminInvoices);
        $adminInvoicesSearch = new Zend_Acl_Resource('mvc:admin.invoices.search');
        $acl->addResource($adminInvoicesSearch, $adminInvoices);
        $adminInvoicesView = new Zend_Acl_Resource('mvc:admin.invoices.view');
        $acl->addResource($adminInvoicesView, $adminInvoices);
        $adminInvoicesUpdate = new Zend_Acl_Resource('mvc:admin.invoices.update');
        $acl->addResource($adminInvoicesUpdate, $adminInvoices);
        $adminInvoicesDownload = new Zend_Acl_Resource('mvc:admin.invoices.download');
        $acl->addResource($adminInvoicesDownload, $adminInvoices);
        $adminUsers = new Zend_Acl_Resource('mvc:admin.users.index');
        $acl->addResource($adminUsers, $admin);
        $adminUsersUpdate = new Zend_Acl_Resource('mvc:admin.users.update');
        $acl->addResource($adminUsersUpdate, $admin);
        $adminUsersList = new Zend_Acl_Resource('mvc:admin.users.list');
        $acl->addResource($adminUsersList, $adminUsers);
        $adminUsersView = new Zend_Acl_Resource('mvc:admin.users.view');
        $acl->addResource($adminUsersView, $adminUsers);
        $adminUsersEdit = new Zend_Acl_Resource('mvc:admin.users.edit');
        $acl->addResource($adminUsersEdit, $adminUsers);
        $adminUsersAdd = new Zend_Acl_Resource('mvc:admin.users.add');
        $acl->addResource($adminUsersAdd, $adminUsers);
        $adminUpload = new Zend_Acl_Resource('mvc:admin.upload.index');
        $acl->addResource($adminUpload, $admin);
        $adminUploadUpload = new Zend_Acl_Resource('mvc:admin.upload.upload');
        $acl->addResource($adminUploadUpload, $adminUpload);
        $adminUploadErrors = new Zend_Acl_Resource('mvc:admin.upload.errors');
        $acl->addResource($adminUploadErrors, $adminUpload);
        $adminUploadFinish = new Zend_Acl_Resource('mvc:admin.upload.finish');
        $acl->addResource($adminUploadFinish, $adminUpload);
        // permissions
        $acl->allow($guest, $login); // guests can login as sellers
        $acl->deny($guest, $loginRegister); // guests cannot register
        $acl->allow(array($sellerActivatedManual, $sellerActivatedAuto), $loginRegister);
        $acl->deny($sellerActivatedManual, array($loginLogin, $loginForgot));
        $acl->allow($guest, $adminLogin); // guests can login as admins
        $acl->allow($sellerActivatedManual, $index); // sellers can see index
        $acl->deny($sellerDeactivated, $sell);
        $acl->allow($adminRole, $admin); // admins can see admin module
        $acl->deny($adminRole, $adminUsers);
        $acl->deny($adminProcessor, $adminUpload);
        $acl->allow($adminBuyer, $adminUpload);
        $acl->allow($adminSuper, $adminUsers); // superadmins can see everything
        Zend_Registry::set('acl', $acl);
        return $acl;
    }

    public function _initRole()
    {
        $role = 'guest';
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $namespace = new Zend_Session_Namespace('User');
            if (isset($namespace->role)) {
                $role = $namespace->role;
            } else {
                // they are logged in but do not have a role, so boot them
                Zend_Auth::getInstance()->clearIdentity();
            }
        }
        Zend_Registry::set('role', $role);
        return $role;
    }

    public function _initViewAcl()
    {
        $this->bootstrap('view');
        $this->bootstrap('navigation');
        $this->bootstrap('acl');
        $this->bootstrap('role');
        $view = $this->getResource('view');
        /* @var $view Zend_View */
        $acl = $this->getResource('acl');
        $view->navigation()->setAcl($acl)->setRole($this->getResource('role'));
    }

    public function _initJavascript()
    {
        $this->bootstrap('view');
        $this->bootstrap('frontcontroller');
        $view = $this->getResource('view');
        /* @var $view Zend_View */
        $view->headScript()->appendFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js');
    }

    public function _initAuthAdapter()
    {
        $this->bootstrap('db');
        $authAdapter = new Zend_Auth_Adapter_DbTable(
                $this->getResource('db'),
                'seller',
                'contactEmail',
                'passwordHash',
                'SHA1(CONCAT(?,passwordSalt))'
        );
        return $authAdapter;
    }

    public function _initViewUser()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->bootstrap('view');
            $view = $this->getResource('view');
            $namespace = new Zend_Session_Namespace('User');
            $view->user = $namespace->user;
        }
    }

}

