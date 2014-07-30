<?php

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerComment.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerCommentCriterion.php');

class NpsMarketplaceSellerCommentsModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('action'))
        {
            switch(Tools::getValue('action'))
            {
                case 'add_comment':
                    $this->ajaxProcessAddComment();
                    break;
                case 'report_abuse':
                    $this->ajaxProcessReportAbuse();
                    break;
                case 'comment_is_usefull':
                    $this->ajaxProcessCommentIsUsefull();
                    break;
            }
        }
    }

    protected function ajaxProcessAddComment()
    {
        $module_instance = new NpsMarketplace();

        $result = true;
        $id_guest = 0;
        $id_customer = $this->context->customer->id;
        if (!$id_customer)
            $id_guest = $this->context->cookie->id_guest;

        $errors = array();
        // Validation
        if (!Validate::isInt(Tools::getValue('id_seller')))
            $errors[] = $module_instance->l('Seller ID is incorrect', 'default');
        if (!Tools::getValue('title') || !Validate::isGenericName(Tools::getValue('title')))
            $errors[] = $module_instance->l('Title is incorrect', 'default');
        if (!Tools::getValue('content') || !Validate::isMessage(Tools::getValue('content')))
            $errors[] = $module_instance->l('Comment is incorrect', 'default');
        if (!$id_customer && (!Tools::isSubmit('customer_name') || !Tools::getValue('customer_name') || !Validate::isGenericName(Tools::getValue('customer_name'))))
            $errors[] = $module_instance->l('Customer name is incorrect', 'default');
        if (!$this->context->customer->id && !Configuration::get('SELLER_COMMENTS_ALLOW_GUESTS'))
            $errors[] = $module_instance->l('You must be connected in order to send a comment', 'default');
        if (!count(Tools::getValue('criterion')))
            $errors[] = $module_instance->l('You must give a rating', 'default');

        $seller = new Seller(Tools::getValue('id_seller'));
        if (!$seller->id)
            $errors[] = $module_instance->l('Seller not found', 'default');

        if (!count($errors))
        {
            $customer_comment = SellerComment::getByCustomer(Tools::getValue('id_seller'), $id_customer, true, $id_guest);
            if (!$customer_comment || ($customer_comment && (strtotime($customer_comment['date_add']) + (int)Configuration::get('SELLER_COMMENTS_MINIMAL_TIME')) < time()))
            {

                $comment = new SellerComment();
                $comment->content = strip_tags(Tools::getValue('content'));
                $comment->id_seller = (int)Tools::getValue('id_seller');
                $comment->id_customer = (int)$id_customer;
                $comment->id_guest = $id_guest;
                $comment->customer_name = Tools::getValue('customer_name');
                if (!$comment->customer_name)
                    $comment->customer_name = pSQL($this->context->customer->firstname.' '.$this->context->customer->lastname);
                $comment->title = Tools::getValue('title');
                $comment->grade = 0;
                $comment->validate = 0;
                $comment->save();

                $grade_sum = 0;
                foreach(Tools::getValue('criterion') as $id_seller_comment_criterion => $grade)
                {
                    $grade_sum += $grade;
                    $seller_comment_criterion = new SellerCommentCriterion($id_seller_comment_criterion);
                    if ($seller_comment_criterion->id)
                        $seller_comment_criterion->addGrade($comment->id, $grade);
                }

                if (count(Tools::getValue('criterion')) >= 1)
                {
                    $comment->grade = $grade_sum / count(Tools::getValue('criterion'));
                    // Update Grade average of comment
                    $comment->save();
                }
                $result = true;
                Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('sellercomments-reviews.tpl'));
            }
            else
            {
                $result = false;
                $errors[] = $module_instance->l('Please wait before posting another comment').' '.Configuration::get('SELLER_COMMENTS_MINIMAL_TIME').' '.$module_instance->l('seconds before posting a new comment');
            }
        }
        else
            $result = false;

        die(Tools::jsonEncode(array(
            'result' => $result,
            'errors' => $errors
        )));
    }

    protected function ajaxProcessReportAbuse()
    {
        if (!Tools::isSubmit('id_seller_comment'))
            die('0');

        if (SellerComment::isAlreadyReport(Tools::getValue('id_seller_comment'), $this->context->cookie->id_customer))
            die('0');

        if (SellerComment::reportComment((int)Tools::getValue('id_seller_comment'), $this->context->cookie->id_customer))
            die('1');

        die('0');
    }

    protected function ajaxProcessCommentIsUsefull()
    {
        if (!Tools::isSubmit('id_seller_comment') || !Tools::isSubmit('value'))
            die('0');

        if (SellerComment::isAlreadyUsefulness(Tools::getValue('id_seller_comment'), $this->context->cookie->id_customer))
            die('0');

        if (SellerComment::setCommentUsefulness((int)Tools::getValue('id_seller_comment'), (bool)Tools::getValue('value'), $this->context->cookie->id_customer))
            die('1');

        die('0');
    }
}
