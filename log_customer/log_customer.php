<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'classes/LogCustomerClasses.php';

class log_customer extends Module
{

    public function __construct()
    {
    
        $this->name = 'log_customer';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'José Solorzano';
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        //~ $this->displayName = $this->l('Customer log module', 'log_customer');
        //~ $this->description = $this->l('Module to register customer user logs', 'log_customer');
        $this->displayName = $this->trans('Customer log module', [], 'Modules.Logcustomer.Log_customer');
        $this->description = $this->trans('Module to register customer user logs', [], 'Modules.Logcustomer.Log_customer');

        parent::__construct();
        
    }
    
    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;
        return $tab->add();
    }
    
    public function createBackendTab()
    {
        //~ $this->installTab('AdminLogCustomer', $this->l('Records/Client Logs', 'log_customer'), 'AdminAdvancedParameters');
        $this->installTab('AdminLogCustomer', $this->trans('Records/%Logs% Clients', ['%Logs%' => 'Logs'], 'Modules.Logcustomer.Log_customer'), 'AdminAdvancedParameters');
        return true;
    }

	public function install()
    {
		$logcustomerhelper = new LogCustomerHelper();
		$currentDir = dirname(__FILE__);
		if (!parent::install() 
			|| !$this->createBackendTab()
            || !$logcustomerhelper->installTable()
            || !$logcustomerhelper->installTranslations($currentDir, array("es"))
			|| !$this->registerHook(array('backOfficeHeader', 'actionValidateOrder', 'actionOrderStatusUpdate'))
		) {
			return false;
		}
		return true;
    }
    
    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

	public function uninstall()
    {
		$logcustomerhelper = new LogCustomerHelper();
        if (!parent::uninstall() 
			|| !$this->unregisterHook('backOfficeHeader')
            || !$this->dropTable()
			|| !$this->uninstallTab()
			|| !$logcustomerhelper->uninstallTranslations("ModulesLogcustomer")
		) {
            return false;
        }
        return true;
    }
    
    public function dropTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'log_customer`'
        );
    }
	
    /*public function getContent()
	{
		$shop = new Shop((int)$this->context->shop->id);
		$base_url = $shop->getBaseURL();
		$admin_dir = $this->getAdminFolderName(_PS_ADMIN_DIR_)."/";
		//$current_token = $this->token;
		//$current_token = Tools::getAdminTokenLite('AdminProducts');
		$current_token = Tools::getAdminToken((int)$this->context->employee->id);
		$products_url = 'index.php/sell/catalog/products/';
		$ajax = $base_url.'modules/'.$this->name.'/ajax.php?token='.Tools::encrypt($this->name.'/ajax.php');
		//$language = $this->getLanguageIso("es");
		//$translations = $this->getTranslationsCsv('es');
		$this->context->smarty->assign(array(
			'url_ajax' => $ajax,
			'admin_dir' => $admin_dir,
			'current_token' => $current_token,
			'products_url' => $products_url,
			'base_url' => $base_url,
			//'language' => $language,
			//'translations' => $translations
		));
		return $this->context->smarty->fetch($this->local_path.'views/templates/admin/admin.tpl');
	}*/
	
	public function getAdminFolderName($admin_dir)
	{
		$admin_folder_name = explode('/', $admin_dir);
		return $admin_folder_name[count($admin_folder_name)-1];
	}
	
	public function searchProducts($search)
	{
		$search = str_replace(' ', '%', $search);
		return Db::getInstance()->executeS('SELECT id_product, `name`
			FROM '._DB_PREFIX_.'product_lang
			WHERE id_lang = '.(int)$this->context->language->id.'
			AND `name` LIKE "%'.$search.'%"');
	}
    
    public function getGiftsOrder($idOrder)
    {
        return Db::getInstance()->executeS(
            'SELECT bgc.*, pl.name FROM '._DB_PREFIX_.'bestkit_gift_cart bgc
				INNER JOIN '._DB_PREFIX_.'product_lang pl ON bgc.id_product = pl.id_product
                WHERE bgc.id_order = '.(int) $idOrder.' AND pl.id_lang = '.(int)$this->context->language->id
        );
    }
    
    public function HookBackOfficeHeader($params)
	{
		if (Tools::getValue('configure') == $this->name) {
			$this->context->controller->addJquery();
			$this->context->controller->addJS($this->local_path.'/views/js/admin.js');
		}
	}
	
	public function hookActionValidateOrder($params)
    {
		// Add new resource
		$date = date('Y-m-d H:i:s');
        $log_customerObj = new LogCustomer();
        $log_customerObj->severity = 1;
        $log_customerObj->error_code = null;
        //~ $log_customerObj->message = "Nueva orden ".$params['order']->reference." creada. ".$params['order']->payment.".";
        //~ $message = sprintf($this->l('New order %s created. %s.', 'log_customer'), $params['order']->reference, $params['order']->payment);
        $message = $this->trans('New order %reference% created. %payment%.', ['%reference%' => $params['order']->reference, '%payment%' => $params['order']->payment], 'Modules.Logcustomer.Log_customer');
        $log_customerObj->message = $message;
        $log_customerObj->object_type = "Orders";
        $log_customerObj->object_id = $params['order']->id;
        $log_customerObj->id_customer = $params['order']->id_customer;
        $log_customerObj->date_add = $date;
        $log_customerObj->date_upd = $date;
        
        $log_customerObj->add();
        
        // Add gift as new resource
        $module = Module::getInstanceByName('bestkit_gifts');
		if ($module->active) {
			$gifts = $this->getGiftsOrder($params['order']->id);
			if(count($gifts) > 0){
				foreach($gifts as $gift){
					$log_customerObj = new LogCustomer();
					$log_customerObj->severity = 1;
					$log_customerObj->error_code = null;
					//~ $log_customerObj->message = "Regalo elegido en orden ".$params['order']->reference.". ".$gift['name'].".";
					//~ $message = sprintf($this->l('Gift chosen in order %s. %s.', 'log_customer'), $params['order']->reference, $gift['name']);
					$message = $this->trans('Gift chosen in order %reference%. %name%.', ['%reference%' => $params['order']->reference, '%name%' => $gift['name']], 'Modules.Logcustomer.Log_customer');
					$log_customerObj->message = $message;
					$log_customerObj->object_type = "Orders";
					$log_customerObj->object_id = $params['order']->id;
					$log_customerObj->id_customer = $params['order']->id_customer;
					$log_customerObj->date_add = $date;
					$log_customerObj->date_upd = $date;
					
					$log_customerObj->add();
				}
			}
		}

		return true;
	}
	
	public function hookActionOrderStatusUpdate($params)
    {
		$orderObj = new Order($params['id_order']);
		//~ echo "<pre>";
		//~ print_r($params);
		//~ exit();
		if($params['newOrderStatus']->id == 2){
			// Add new resource
			$log_customerObj = new LogCustomer();
			$log_customerObj->severity = 1;
			$log_customerObj->error_code = null;
			//~ $log_customerObj->message = "Orden ".$orderObj->reference." pagada.";
			//~ $message = sprintf($this->l('Paid %s order.', 'log_customer'), $orderObj->reference);
			$message = $this->trans('Order %reference% paid.', ['%reference%' => $orderObj->reference], 'Modules.Logcustomer.Log_customer');
			$log_customerObj->message = $message;
			$log_customerObj->object_type = "Orders";
			$log_customerObj->object_id = $params['id_order'];
			$log_customerObj->id_customer = $orderObj->id_customer;
			$log_customerObj->date_add = date('Y-m-d H:i:s');
			$log_customerObj->date_upd = date('Y-m-d H:i:s');

			return $log_customerObj->add();
		}
	}
	
	/**
     * Enable use of the new Back Office translation interface.
     * 
     * @return boolean
     */
	public function isUsingNewTranslationSystem()
	{
		return true;
	}
   
}


