<?php
/**
 * 2008-2021 José Solorzano
 *
 * All right is reserved,
 *
 * @author  jsolorzano <solorzano202009@gmail.com>
 */

class AdminLogCustomerController extends ModuleAdminController
{
    public function __construct()
    {
        $this->identifier = 'id_log_customer';
        parent::__construct();

        $this->bootstrap = true;
        $this->allow_export = false;
        $this->list_no_link = true;
        $this->table = 'log_customer';
        $this->className = 'LogCustomer';
        $this->_select = '
        c.firstname as customer_name,
        c.lastname as customer_lastname,
        c.email as customer_email';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = a.id_customer)';
        $this->_where = ' AND c.`id_lang` = '.(int) $this->context->language->id;
        $this->module = 'log_customer';
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function renderList()
    {
		$idEvent = Tools::getValue('id_log_customer');
        if ($idEvent) {
            $this->eventDetail();
        } else {
			$this->eventList();
        }
        
        return parent::renderList();
    }
    
    public function eventDetail($objLog)
    {		
		$orderObj = new Order($objLog->object_id);
		$products = $this->getProducts($orderObj);
		$gifts = $this->getGifts($objLog->object_id);
		$protocol = $this->getCurrentProtocol();
		$link = new Link($protocol);
		$link->protocol_content = $protocol;
		
		$tpl = $this->context->smarty->createTemplate(dirname(__FILE__). '/../../views/templates/admin/view.tpl');
		$tpl->assign('order', $orderObj);
		$tpl->assign('products', $products);
		$tpl->assign('gifts', $gifts);
		$tpl->assign('num_products', count($products));
		$tpl->assign('num_gifts', count($gifts));
		$tpl->assign('link', $link);
		
		return $tpl->fetch();
    }

    public function eventList()
    {
        $this->addRowAction('view');
        //~ $this->addRowAction('delete');

        $this->fields_list = array(
            'id_log_customer' => array(
                //~ 'title' => $this->l('ID', 'adminlogcustomercontroller'),
                'title' => $this->trans('ID', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                'align' => 'left',
                'class' => 'fixed-width-xs',
                'order_key' => 'id_log_customer',
            ),
            'message' => array(
                //~ 'title' => $this->l('Message', 'adminlogcustomercontroller'),
                'title' => $this->trans('Message', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                'align' => 'left',
                'orderby' => false,
                'havingFilter' => true,
                'search' => true
            ),
            //~ 'customer_name' => array(
                //~ //'title' => $this->l('Customer Name', 'adminlogcustomercontroller'),
                //~ 'title' => $this->trans('Customer Name', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                //~ 'align' => 'center',
                //~ 'havingFilter' => true,
            //~ ),
            //~ 'customer_lastname' => array(
                //~ //'title' => $this->l('Customer Lastname', 'adminlogcustomercontroller'),
                //~ 'title' => $this->trans('Customer Lastname', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                //~ 'align' => 'center',
                //~ 'havingFilter' => true,
            //~ ),
            'object_type' => array(
                //~ 'title' => $this->l('Object type', 'adminlogcustomercontroller'),
                'title' => $this->trans('Object type', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'object_id' => array(
                //~ 'title' => $this->l('Object id', 'adminlogcustomercontroller'),
                'title' => $this->trans('Object id', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'customer_email' => array(
                //~ 'title' => $this->l('Customer email', 'adminlogcustomercontroller'),
                'title' => $this->trans('Customer email', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'date_add' => array(
                //~ 'title' => $this->l('Date', 'adminlogcustomercontroller'),
                'title' => $this->trans('Date', [], 'Modules.Logcustomer.Adminlogcustomercontroller'),
                'align' => 'center',
                'havingFilter' => true,
            ),
        );
    }

    public function renderView()
    {
		$id = Tools::getValue('id_log_customer');
        if ($id) {
			$objLog = new $this->className($id);
			return $this->eventDetail($objLog);
        }
        return parent::renderList();
    }
    
    protected function filterToField($key, $filter)
    {
        if ($this->display == 'view') {
            $id = Tools::getValue('id_log_customer');
            $objLog = new $this->className($id);
            $this->eventDetail($objLog);
        } else {
            $this->eventList();
        }
        return parent::filterToField($key, $filter);
    }
    
    public function getGiftsOrder($idOrder)
    {
        return Db::getInstance()->executeS(
            'SELECT bgc.*, pl.name FROM '._DB_PREFIX_.'bestkit_gift_cart bgc
				INNER JOIN '._DB_PREFIX_.'product_lang pl ON bgc.id_product = pl.id_product
                WHERE bgc.id_order = '.(int) $idOrder.' AND pl.id_lang = '.(int)$this->context->language->id
        );
    }
    
    /**
     * @param Order $order
     *
     * @return array
     */
    protected function getProducts($order)
    {
        $products = $order->getProducts();

        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }

        ksort($products);

        return $products;
    }
    
    /**
     * @param int $idOrder
     *
     * @return array
     */
    protected function getGifts($idOrder)
    {
		$gifts_order = $this->getGiftsOrder($idOrder);
		
		$gifts = array();

        foreach ($gifts_order as $gift) {
			$product = new Product($gift['id_product']);
			$product->image = Image::getCover($product->id);
			$gifts[] = $product;
        }

        ksort($gifts);

        return $gifts;
    }
    
    /**
     * Detect the type of protocol used and returns it
     * 
     * @return string
     */
    public function getCurrentProtocol()
	{		
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
				
		return $protocol;
	}

}
