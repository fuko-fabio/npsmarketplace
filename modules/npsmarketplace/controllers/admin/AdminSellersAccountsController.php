<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSellersAccountsController extends AdminController
{
	protected $delete_mode;

	protected $_defaultOrderBy = 'date_add';
	protected $_defaultOrderWay = 'DESC';
	protected $can_add_customer = true;

	public function __construct()
	{
		$this->bootstrap = true;
		$this->required_database = true;
		$this->table = 'seller';
		$this->className = 'Seller';
		$this->lang = false;
		$this->deleted = true;
		$this->explicitSelect = true;

		$this->allow_export = true;

		$this->addRowAction('edit');
		$this->addRowAction('view');
		$this->addRowAction('delete');
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		$this->context = Context::getContext();

		$this->default_form_language = $this->context->language->id;

		$titles_array = array();
		$genders = Gender::getGenders($this->context->language->id);
		foreach ($genders as $gender)
			$titles_array[$gender->id_gender] = $gender->name;

		$this->fields_list = array(
			'id_customer' => array(
				'title' => $this->l('ID'),
				'align' => 'text-center',
				'class' => 'fixed-width-xs'
			),
			'title' => array(
				'title' => $this->l('Title'),
				'filter_key' => 'a!id_gender',
				'type' => 'select',
				'list' => $titles_array,
				'filter_type' => 'int',
				'order_key' => 'gl!name'
			),
			'lastname' => array(
				'title' => $this->l('Last name')
			),
			'firstname' => array(
				'title' => $this->l('First Name')
			),
			'email' => array(
				'title' => $this->l('Email address')
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'text-center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'a!active'
			),
			'date_add' => array(
				'title' => $this->l('Registration'),
				'type' => 'date',
				'align' => 'text-right'
			)
		);

		$this->shopLinkType = 'shop';
		$this->shopShareDatas = Shop::SHARE_CUSTOMER;

		parent::__construct();
	}

	public function initContent()
	{
		if ($this->action == 'select_delete')
			$this->context->smarty->assign(array(
				'delete_form' => true,
				'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
				'boxes' => $this->boxes,
			));

		parent::initContent();
	}

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

        if ($this->_list)
            foreach ($this->_list as &$row)
                $row['badge_success'] = $row['total_spent'] > 0;
    }


    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        switch ($this->display)
        {
            case '':
            case 'list':
                $this->toolbar_title[] = $this->l('Manage your Sellers');
                break;
            case 'view':
                if (($customer = $this->loadObject(true)) && Validate::isLoadedObject($customer))
                    $this->toolbar_title[] = sprintf('Seller: %s', Tools::substr($customer->firstname, 0, 1).'. '.$customer->lastname);
                break;
            case 'edit':
                if (($customer = $this->loadObject(true)) && Validate::isLoadedObject($customer))
                    $this->toolbar_title[] = sprintf($this->l('Editing Seller: %s'), Tools::substr($customer->firstname, 0, 1).'. '.$customer->lastname);
                break;
        }
    }

	public function initProcess()
	{
		parent::initProcess();

		if (Tools::isSubmit('submitGuestToCustomer') && $this->id_object)
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'guest_to_customer';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}

		// When deleting, first display a form to select the type of deletion
		if ($this->action == 'delete' || $this->action == 'bulkdelete')
			if (Tools::getValue('deleteMode') == 'real' || Tools::getValue('deleteMode') == 'deleted')
				$this->delete_mode = Tools::getValue('deleteMode');
			else
				$this->action = 'select_delete';
	}

	public function renderList()
	{
		if (Tools::isSubmit('submitBulkdelete'.$this->table) || Tools::isSubmit('delete'.$this->table))
			$this->tpl_list_vars = array(
				'delete_customer' => true,
				'REQUEST_URI' => $_SERVER['REQUEST_URI'],
				'POST' => $_POST
			);

		return parent::renderList();
	}

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true)))
            return;

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Seller'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->l('Status'),
                    'name' => 'id_status',
                    'required' => true,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'account_requested',
                            'value' => 1,
                            'label' => $this->l('Requested')
                        ),
                        array(
                            'id' => 'disabled',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),
                        array(
                            'id' => 'locked',
                            'value' => 2,
                            'label' => $this->l('Locked')
                        ),
                        array(
                            'id' => 'active',
                            'value' => 2,
                            'label' => $this->l('Active')
                        )
                    ),
                    'hint' => $this->l('Change seller account status')
                ),
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        return parent::renderForm();
    }

    public function beforeAdd($customer)
    {
        $customer->id_shop = $this->context->shop->id;
    }

	public function renderKpis()
	{
		$time = time();
		$kpis = array();

		/* The data generation is located in AdminStatsControllerCore */

		$helper = new HelperKpi();
		$helper->id = 'box-gender';
		$helper->icon = 'icon-male';
		$helper->color = 'color1';
		$helper->title = $this->l('Customers', null, null, false);
		$helper->subtitle = $this->l('All Time', null, null, false);
		if (ConfigurationKPI::get('CUSTOMER_MAIN_GENDER', $this->context->language->id) !== false)
			$helper->value = ConfigurationKPI::get('CUSTOMER_MAIN_GENDER', $this->context->language->id);
		if (ConfigurationKPI::get('CUSTOMER_MAIN_GENDER_EXPIRE', $this->context->language->id) < $time)
			$helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=customer_main_gender';
		$kpis[] = $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-age';
		$helper->icon = 'icon-calendar';
		$helper->color = 'color2';
		$helper->title = $this->l('Average Age', 'AdminTab', null, false);
		$helper->subtitle = $this->l('All Time', null, null, false);
		if (ConfigurationKPI::get('AVG_CUSTOMER_AGE', $this->context->language->id) !== false)
			$helper->value = ConfigurationKPI::get('AVG_CUSTOMER_AGE', $this->context->language->id);
		if (ConfigurationKPI::get('AVG_CUSTOMER_AGE_EXPIRE', $this->context->language->id) < $time)
			$helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=avg_customer_age';
		$kpis[] = $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-orders';
		$helper->icon = 'icon-retweet';
		$helper->color = 'color3';
		$helper->title = $this->l('Orders per Customer', null, null, false);
		$helper->subtitle = $this->l('All Time', null, null, false);
		if (ConfigurationKPI::get('ORDERS_PER_CUSTOMER') !== false)
			$helper->value = ConfigurationKPI::get('ORDERS_PER_CUSTOMER');
		if (ConfigurationKPI::get('ORDERS_PER_CUSTOMER_EXPIRE') < $time)
			$helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=orders_per_customer';
		$kpis[] = $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-newsletter';
		$helper->icon = 'icon-envelope';
		$helper->color = 'color4';
		$helper->title = $this->l('Newsletter Registrations', null, null, false);
		$helper->subtitle = $this->l('All Time', null, null, false);
		if (ConfigurationKPI::get('NEWSLETTER_REGISTRATIONS') !== false)
			$helper->value = ConfigurationKPI::get('NEWSLETTER_REGISTRATIONS');
		if (ConfigurationKPI::get('NEWSLETTER_REGISTRATIONS_EXPIRE') < $time)
			$helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=newsletter_registrations';
		$kpis[] = $helper->generate();

		$helper = new HelperKpiRow();
		$helper->kpis = $kpis;
		return $helper->generate();
	}

	public function renderView()
	{
		if (!($customer = $this->loadObject()))
			return;

		$this->context->customer = $customer;
		$gender = new Gender($customer->id_gender, $this->context->language->id);
		$gender_image = $gender->getImage();

		$customer_stats = $customer->getStats();
		$sql = 'SELECT SUM(total_paid_real) FROM '._DB_PREFIX_.'orders WHERE id_customer = %d AND valid = 1';
		if ($total_customer = Db::getInstance()->getValue(sprintf($sql, $customer->id)))
		{
			$sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM '._DB_PREFIX_.'orders WHERE valid = 1 AND id_customer != '.(int)$customer->id.' GROUP BY id_customer HAVING SUM(total_paid_real) > %d';
			Db::getInstance()->getValue(sprintf($sql, (int)$total_customer));
			$count_better_customers = (int)Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
		}
		else
			$count_better_customers = '-';

		$orders = Order::getCustomerOrders($customer->id, true);
		$total_orders = count($orders);
		for ($i = 0; $i < $total_orders; $i++)
		{
			$orders[$i]['total_paid_real_not_formated'] = $orders[$i]['total_paid_real'];
			$orders[$i]['total_paid_real'] = Tools::displayPrice($orders[$i]['total_paid_real'], new Currency((int)$orders[$i]['id_currency']));
		}

		$messages = CustomerThread::getCustomerMessages((int)$customer->id);
		$total_messages = count($messages);
		for ($i = 0; $i < $total_messages; $i++)
		{
			$messages[$i]['message'] = substr(strip_tags(html_entity_decode($messages[$i]['message'], ENT_NOQUOTES, 'UTF-8')), 0, 75);
			$messages[$i]['date_add'] = Tools::displayDate($messages[$i]['date_add'], null, true);
		}

		$groups = $customer->getGroups();
		$total_groups = count($groups);
		for ($i = 0; $i < $total_groups; $i++)
		{
			$group = new Group($groups[$i]);
			$groups[$i] = array();
			$groups[$i]['id_group'] = $group->id;
			$groups[$i]['name'] = $group->name[$this->default_form_language];
		}

		$total_ok = 0;
		$orders_ok = array();
		$orders_ko = array();
		foreach ($orders as $order)
		{
			if (!isset($order['order_state']))
				$order['order_state'] = $this->l('There is no status defined for this order.');

			if ($order['valid'])
			{
				$orders_ok[] = $order;
				$total_ok += $order['total_paid_real_not_formated'];
			}
			else
				$orders_ko[] = $order;
		}

		$products = $customer->getBoughtProducts();
		$total_products = count($products);
		for ($i = 0; $i < $total_products; $i++)
			$products[$i]['date_add'] = Tools::displayDate($products[$i]['date_add'], null, true);

		$carts = Cart::getCustomerCarts($customer->id);
		$total_carts = count($carts);
		for ($i = 0; $i < $total_carts; $i++)
		{
			$cart = new Cart((int)$carts[$i]['id_cart']);
			$this->context->cart = $cart;
			$summary = $cart->getSummaryDetails();
			$currency = new Currency((int)$carts[$i]['id_currency']);
			$carrier = new Carrier((int)$carts[$i]['id_carrier']);
			$carts[$i]['id_cart'] = sprintf('%06d', $carts[$i]['id_cart']);
			$carts[$i]['date_add'] = Tools::displayDate($carts[$i]['date_add'], null, true);
			$carts[$i]['total_price'] = Tools::displayPrice($summary['total_price'], $currency);
			$carts[$i]['name'] = $carrier->name;
		}

		$sql = 'SELECT DISTINCT cp.id_product, c.id_cart, c.id_shop, cp.id_shop AS cp_id_shop
				FROM '._DB_PREFIX_.'cart_product cp
				JOIN '._DB_PREFIX_.'cart c ON (c.id_cart = cp.id_cart)
				JOIN '._DB_PREFIX_.'product p ON (cp.id_product = p.id_product)
				WHERE c.id_customer = '.(int)$customer->id.'
					AND cp.id_product NOT IN (
							SELECT product_id
							FROM '._DB_PREFIX_.'orders o
							JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
							WHERE o.valid = 1 AND o.id_customer = '.(int)$customer->id.'
						)';
		$interested = Db::getInstance()->executeS($sql);
		$total_interested = count($interested);
		for ($i = 0; $i < $total_interested; $i++)
		{
			$product = new Product($interested[$i]['id_product'], false, $this->default_form_language, $interested[$i]['id_shop']);
			if (!Validate::isLoadedObject($product))
				continue;
			$interested[$i]['url'] = $this->context->link->getProductLink(
				$product->id,
				$product->link_rewrite,
				Category::getLinkRewrite($product->id_category_default, $this->default_form_language),
				null,
				null,
				$interested[$i]['cp_id_shop']
			);
			$interested[$i]['id'] = (int)$product->id;
			$interested[$i]['name'] = Tools::htmlentitiesUTF8($product->name);
		}

		$connections = $customer->getLastConnections();
		if (!is_array($connections))
			$connections = array();
		$total_connections = count($connections);
		for ($i = 0; $i < $total_connections; $i++)
			$connections[$i]['http_referer'] = $connections[$i]['http_referer'] ? preg_replace('/^www./', '', parse_url($connections[$i]['http_referer'], PHP_URL_HOST)) : $this->l('Direct link');
		
		$referrers = Referrer::getReferrers($customer->id);
		$total_referrers = count($referrers);
		for ($i = 0; $i < $total_referrers; $i++)
			$referrers[$i]['date_add'] = Tools::displayDate($referrers[$i]['date_add'],null , true);

		$customerLanguage = new Language($customer->id_lang);
		$shop = new Shop($customer->id_shop);
		$this->tpl_view_vars = array(
			'customer' => $customer,
			'gender' => $gender,
			'gender_image' => $gender_image,
			// General information of the customer
			'registration_date' => Tools::displayDate($customer->date_add,null , true),
			'customer_stats' => $customer_stats,
			'last_visit' => Tools::displayDate($customer_stats['last_visit'],null , true),
			'count_better_customers' => $count_better_customers,
			'shop_is_feature_active' => Shop::isFeatureActive(),
			'name_shop' => $shop->name,
			'customer_birthday' => Tools::displayDate($customer->birthday),
			'last_update' => Tools::displayDate($customer->date_upd,null , true),
			'customer_exists' => Customer::customerExists($customer->email),
			'id_lang' => $customer->id_lang,
			'customerLanguage' => $customerLanguage,
			// Add a Private note
			'customer_note' => Tools::htmlentitiesUTF8($customer->note),
			// Messages
			'messages' => $messages,
			// Groups
			'groups' => $groups,
			// Orders
			'orders' => $orders,
			'orders_ok' => $orders_ok,
			'orders_ko' => $orders_ko,
			'total_ok' => Tools::displayPrice($total_ok, $this->context->currency->id),
			// Products
			'products' => $products,
			// Addresses
			'addresses' => $customer->getAddresses($this->default_form_language),
			// Discounts
			'discounts' => CartRule::getCustomerCartRules($this->default_form_language, $customer->id, false, false),
			// Carts
			'carts' => $carts,
			// Interested
			'interested' => $interested,
			// Connections
			'connections' => $connections,
			// Referrers
			'referrers' => $referrers,
			'show_toolbar' => true
		);

		return parent::renderView();
	}

	public function processDelete()
	{
		$this->_setDeletedMode();
		parent::processDelete();
	}
	
	protected function _setDeletedMode()
	{
		if ($this->delete_mode == 'real')
			$this->deleted = false;
		elseif ($this->delete_mode == 'deleted')
			$this->deleted = true;
		else
		{
			$this->errors[] = Tools::displayError('Unknown delete mode:').' '.$this->deleted;
			return;
		}
	}
		
	protected function processBulkDelete()
	{
		$this->_setDeletedMode();
		parent::processBulkDelete();
	}

	public function processAdd()
	{
		if (Tools::getValue('submitFormAjax'))
			$this->redirect_after = false;
		// Check that the new email is not already in use
		$customer_email = strval(Tools::getValue('email'));
		$customer = new Customer();
		if (Validate::isEmail($customer_email))
			$customer->getByEmail($customer_email);
		if ($customer->id)
		{
			$this->errors[] = Tools::displayError('An account already exists for this email address:').' '.$customer_email;
			$this->display = 'edit';
			return $customer;
		}
		elseif (trim(Tools::getValue('passwd')) == '')
		{
			$this->validateRules();
			$this->errors[] = Tools::displayError('Password can not be empty.');
			$this->display = 'edit';
		}
		elseif ($customer = parent::processAdd())
		{
			$this->context->smarty->assign('new_customer', $customer);
			return $customer;
		}
		return false;
	}

	public function processUpdate()
	{
		if (Validate::isLoadedObject($this->object))
		{
			$customer_email = strval(Tools::getValue('email'));

			// check if e-mail already used
			if ($customer_email != $this->object->email)
			{
				$customer = new Customer();
				if (Validate::isEmail($customer_email))
					$customer->getByEmail($customer_email);
				if (($customer->id) && ($customer->id != (int)$this->object->id))
					$this->errors[] = Tools::displayError('An account already exists for this email address:').' '.$customer_email;
			}

			return parent::processUpdate();
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while loading the object.').'
				<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
	}

	public function processSave()
	{
		// Check that default group is selected
		if (!is_array(Tools::getValue('groupBox')) || !in_array(Tools::getValue('id_default_group'), Tools::getValue('groupBox')))
			$this->errors[] = Tools::displayError('A default customer group must be selected in group box.');

		// Check the requires fields which are settings in the BO
		$customer = new Customer();
		$this->errors = array_merge($this->errors, $customer->validateFieldsRequiredDatabase());

		return parent::processSave();
	}

	protected function afterDelete($object, $old_id)
	{
		$customer = new Customer($old_id);
		$addresses = $customer->getAddresses($this->default_form_language);
		foreach ($addresses as $k => $v)
		{
			$address = new Address($v['id_address']);
			$address->id_customer = $object->id;
			$address->save();
		}
		return true;
	}
	/**
	 * Transform a guest account into a registered customer account
	 */
	public function processGuestToCustomer()
	{
		$customer = new Customer((int)Tools::getValue('id_customer'));
		if (!Validate::isLoadedObject($customer))
			$this->errors[] = Tools::displayError('This customer does not exist.');
		if (Customer::customerExists($customer->email))
			$this->errors[] = Tools::displayError('This customer already exists as a non-guest.');
		else if ($customer->transformToCustomer(Tools::getValue('id_lang', $this->context->language->id)))
			Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$customer->id.'&conf=3&token='.$this->token);
		else
			$this->errors[] = Tools::displayError('An error occurred while updating customer information.');
	}

	/**
	 * Toggle the newsletter flag
	 */
	public function processChangeNewsletterVal()
	{
		$customer = new Customer($this->id_object);
		if (!Validate::isLoadedObject($customer))
			$this->errors[] = Tools::displayError('An error occurred while updating customer information.');
		$customer->newsletter = $customer->newsletter ? 0 : 1;
		if (!$customer->update())
			$this->errors[] = Tools::displayError('An error occurred while updating customer information.');
		Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
	}

	/**
	 * Toggle newsletter optin flag
	 */
	public function processChangeOptinVal()
	{
		$customer = new Customer($this->id_object);
		if (!Validate::isLoadedObject($customer))
			$this->errors[] = Tools::displayError('An error occurred while updating customer information.');
		$customer->optin = $customer->optin ? 0 : 1;
		if (!$customer->update())
			$this->errors[] = Tools::displayError('An error occurred while updating customer information.');
		Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
	}

	public function printNewsIcon($value, $customer)
	{
		return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminCustomers&id_customer='
			.(int)$customer['id_customer'].'&changeNewsletterVal&token='.Tools::getAdminTokenLite('AdminCustomers').'">
				'.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
			'</a>';
	}

	public function printOptinIcon($value, $customer)
	{
		return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminCustomers&id_customer='
			.(int)$customer['id_customer'].'&changeOptinVal&token='.Tools::getAdminTokenLite('AdminCustomers').'">
				'.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
			'</a>';
	}

	/**
	 * @param string $token
	 * @param integer $id
	 * @param string $name
	 * @return mixed
	 */
	public function displayDeleteLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

		$customer = new Customer($id);
		$name = $customer->lastname.' '.$customer->firstname;
		$name = '\n\n'.$this->l('Name:', 'helper').' '.$name;

		$tpl->assign(array(
			'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
			'confirm' => $this->l('Delete the selected item?').$name,
			'action' => $this->l('Delete'),
			'id' => $id,
		));

		return $tpl->fetch();
	}

	/**
	 * add to $this->content the result of Customer::SearchByName
	 * (encoded in json)
	 *
	 * @return void
	 */
	public function ajaxProcessSearchCustomers()
	{
		$searches = explode(' ', Tools::getValue('customer_search'));
		$customers = array();
		$searches = array_unique($searches);
		foreach ($searches as $search)
			if (!empty($search) && $results = Customer::searchByName($search))
				foreach ($results as $result)
					$customers[$result['id_customer']] = $result;

		if (count($customers))
			$to_return = array(
				'customers' => $customers,
				'found' => true
			);
		else
			$to_return = array('found' => false);

		$this->content = Tools::jsonEncode($to_return);
	}
	
	/**
	 * Uodate the customer note
	 * 
	 * @return void
	 */
	public function ajaxProcessUpdateCustomerNote()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$note = Tools::htmlentitiesDecodeUTF8(Tools::getValue('note'));
			$customer = new Customer((int)Tools::getValue('id_customer'));
			if (!Validate::isLoadedObject($customer))
				die ('error:update');
			if (!empty($note) && !Validate::isCleanHtml($note))
				die ('error:validation');
			$customer->note = $note;
			if (!$customer->update())
				die ('error:update');
			die('ok');
		}
	}
}