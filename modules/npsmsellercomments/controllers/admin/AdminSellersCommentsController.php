<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmsellercomments/classes/SellerComment.php');

class AdminSellersCommentsController extends AdminController
{
    protected $delete_mode;

    protected $_defaultOrderBy = 'date_add';
    protected $_defaultOrderWay = 'DESC';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'seller_comment';
        $this->className = 'SellerComment';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;

        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            ),
            'validate' => array(
                'text' => $this->l('Validate selected'),
                'confirm' => $this->l('Validate selected items?'),
                'icon' => 'icon-plus'
            )
        );

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        
        $this->fields_list = array(
            'id_seller_comment' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'id_seller' => array(
                'title' => $this->l('Seller ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'customer_name' => array(
                'title' => $this->l('Customer Name')
            ),
            'title' => array(
                'title' => $this->l('Title')
            ),
            'content' => array(
                'title' => $this->l('Content')
            ),
            'validate' => array(
                'title' => $this->l('Visible'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printValidateIcon',
                'orderby' => false
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'type' => 'date',
                'align' => 'text-right'
            ),
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function printValidateIcon($value, $comment)
    {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?tab=AdminSellersComments&id_seller_comment='
            .(int)$comment['id_seller_comment'].'&changeValidateVal&token='.Tools::getAdminTokenLite('AdminSellersComments').'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    public function processChangeValidateVal()
    {
        $object = new $this->className($this->id_object);
        if (!Validate::isLoadedObject($object))
            $this->errors[] = $this->l('An error occurred while updating seller comment information.');
        $object->validate = $object->validate ? 0 : 1;
        if (!$object->update())
            $this->errors[] = $this->l('An error occurred while updating seller comment information.');
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
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

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        switch ($this->display)
        {
            case '':
            case 'list':
                $this->toolbar_title[] = $this->l('Manage Sellers Comments');
                break;
            case 'view':
                $this->toolbar_title[] = $this->l('Comment Details');
                break;
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('changeValidateVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'change_validate_val';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        } elseif (Tools::isSubmit('submitBulkvalidate'.$this->table) || Tools::isSubmit('submitvalidate'.$this->table)) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $result = true;
                foreach ($this->boxes as $id) {
                    $object = new $this->className($id);
                    $object->validate = 1;
                    if (!$object->update())
                        $result = false;
                }
                if ($result)
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                $this->errors[] = $this->l('An error occurred while deleting this selection.');
            } else {
                $this->errors[] = $this->l('You must select at least one element to change visibility status.');
            }
        }
    }
}