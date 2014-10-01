<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');

class NpsMarketplaceAccountRequestModuleFrontController extends ModuleFrontController {

    const __MA_MAIL_DELIMITOR__ = ',';

    public function setMedia() {
        parent::setMedia();
        $this->addjQueryPlugin('autosize');
        $this -> addJS(array(
                _PS_JS_DIR_.'validate.js',
                _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_.'tinymce.inc.js',
            ));
    }

    public function postProcess() {
        if (Tools::isSubmit('submitSeller')) {

            $seller = new Seller(null, $this->context->customer->id);
            if ($seller->id == null) 
                Tools::redirect('index.php?controller=my-account');
            $nps_instance = new NpsMarketplace();

            $company_name = trim(Tools::getValue('company_name'));
            $name = trim(Tools::getValue('seller_name'));
            $phone = trim(Tools::getValue('seller_phone'));
            $email = trim(Tools::getValue('seller_email'));
            $nip = Tools::getValue('seller_nip');
            $regon = Tools::getValue('seller_regon');
            $company_description = $_POST['company_description'];
            $companyLogo = trim(Tools::getValue('company_logo'));
            $regulations_active = Tools::getIsset('regulations_active');
            $regulations = Tools::getValue('regulations');
            $link_rewrite = array();

            if (empty($name))
                $this -> errors[] = $nps_instance->l('Seller name is required');
            else if (!Validate::isGenericName($name))
                $this -> errors[] = $nps_instance->l('Invalid seller name');
            else if (Seller::sellerExists($name))
                $this -> errors[] = $nps_instance->l('Seller name is not unique');

            if (empty($phone))
                $this -> errors[] = $nps_instance->l('Phone number is required');
            else if (!Validate::isPhoneNumber($phone))
                $this -> errors[] = $nps_instance->l('Invalid phone number');

            if (empty($email))
                $this -> errors[] = $nps_instance->l('Buisness email is required');
            else if (!Validate::isEmail($email))
                $this -> errors[] = $nps_instance->l('Invalid email addres');

            if (empty($company_name))
                $this -> errors[] = $nps_instance->l('Company name is required');
            else if (!Validate::isGenericName($company_name))
                $this -> errors[] = $nps_instance->l('Invalid company name');

            if (!empty($nip) && !Validate::isNip($nip))
                $this -> errors[] = $nps_instance->l('Invalid NIP number');

            if (!empty($regon) && !Validate::isRegon($regon))
                $this -> errors[] = $nps_instance->l('Invalid REGON number');

            foreach (Language::getLanguages() as $key => $lang) {
                if (!Validate::isCleanHtml($company_description[$lang['id_lang']]))
                    $this -> errors[] = $nps_instance->l('Invalid company description');
                if (!Validate::isCleanHtml($regulations[$lang['id_lang']]))
                    $this -> errors[] = $nps_instance->l('Invalid regulations content');

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
            }

            if(empty($this->errors)) {
                $seller -> company_name = $company_name;
                $seller -> company_description = $company_description;
                $seller -> name = $name;
                $seller -> phone = $phone;
                $seller -> email = $email;
                $seller -> nip = $nip;
                $seller -> regon = $regon;
                $seller -> link_rewrite = $link_rewrite;
                $seller -> regulations = $regulations;
                $seller -> regulations_active = $regulations_active;
                $seller -> id_customer = $this -> context -> customer -> id;
                $seller -> commision = Configuration::get('NPS_GLOBAL_COMMISION');
                $seller -> request_date = date("Y-m-d H:i:s");
                $seller -> requested = true;
                $seller->save();
                $this->postImage($seller);
                $this->mailToSeller($seller);
                $this->mailToAdmin($seller);
                Tools::redirect('index.php?controller=my-account' );
            }
        }
    }

    private function mailToSeller($seller) {
        $customer = $this->context->customer;
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
            '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
            '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
        );
        return Mail::Send($this->context->language->id,
            'account_request',
            Mail::l('Seller account request'),
            $mail_params,
            $seller->email,
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
    }

    private function mailToAdmin($seller) {
        $lang_id = intval(Configuration::get('PS_LANG_DEFAULT'));
        $name = strval(Configuration::get('PS_SHOP_NAME'));
        $email = strval(Configuration::get('PS_SHOP_EMAIL'));
        $mail_params = array(
            '{name}' => $seller->name,
            '{company_name}' => $seller->company_name,
            '{company_description}' => $seller->company_description[$lang_id],
            '{email}' => $seller->email,
            '{phone}' => $seller->phone,
            '{nip}' => $seller->nip,
            '{regon}' => $seller->regon,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{admin_link}' => Tools::getHttpHost(true).__PS_BASE_URI__.'backoffice/'.$this->context->link->getAdminLink('AdminSellers'),
        );
        $merchant_emails = Configuration::get('NPS_MERCHANT_EMAILS');
        if (!is_null($merchant_emails) && !empty($merchant_emails)) 
            $emails = $merchant_emails;
        else
            $emails = Configuration::get('PS_SHOP_EMAIL');

        return Mail::Send($lang_id,
            'memberalert',
            Mail::l('New seller registration!'),
            $mail_params,
            explode(self::__MA_MAIL_DELIMITOR__, $emails),
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
    }

    public function initContent() {
        $this -> page_name = 'accountrequest';
        $this -> display_column_right = false;
        parent::initContent();

        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password')
            Tools::redirect('index.php?controller=authentication?back=my-account'); 
        $id_customer = $this -> context -> customer -> id;
        if ($id_customer)
        {
            $query = new DbQuery();
            $query
                -> select('*')
                -> from('seller')
                -> where('`id_customer` = '.$id_customer);
            $account_state = 'none';
            $date = null;
            if ($result = Db::getInstance() -> executeS($query))
            {
                $date = $result[0]['request_date'];
                $active = $result[0]['active'];
                $locked = $result[0]['locked'];
                $requested = $result[0]['requested'];
                if ($requested == 1 && $active == 0 && $locked == 0)
                    $account_state = 'requested';
                else if ($requested == 1 && $active == 1 && $locked == 0)
                    $account_state = 'active';
                else if ($requested == 1 && $locked == 1)
                    $account_state = 'locked';
            }
        }

        $this -> context -> smarty -> assign(
            array(
                'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
                'seller' => array('image' => '', 'regulations_active' => false),
                'account_state' => $account_state,
                'account_request_date' => $date,
                'current_id_lang' => (int)$this->context->language->id,
                'languages' => Language::getLanguages(),
                'user_agreement_url' =>  Configuration::get('NPS_SELLER_AGREEMENT_URL'),
                'processing_data_url' => '#',
                'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
            )
        );

        $this -> setTemplate('account_request.tpl');
    }

    protected function postImage($seller)
    {
        $ret = $this->uploadImage($seller);

        if (isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_NPS_SEL_IMG_DIR_.$seller->id.'.'.$seller->getImgFormat()))
        {
            $images_types = ImageType::getImagesTypes('sellers');
            foreach ($images_types as $k => $image_type)
            {
                ImageManager::resize(
                    _NPS_SEL_IMG_DIR_.$seller->id.'.'.$seller->getImgFormat(),
                    _NPS_SEL_IMG_DIR_.$seller->id.'-'.stripslashes($image_type['name']).'.'.$seller->getImgFormat(),
                    (int)$image_type['width'], (int)$image_type['height']
                );
            }
        }
        return $ret;
    }

    protected function uploadImage($seller)
    {
        $name = 'image';
        $dir = 'seller/';
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
        {
            // Delete old image
            $seller->deleteImage();

            // Check image validity
            $max_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size)))
                $this->errors[] = $error;

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name)
                return false;

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name))
                return false;

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name))
                $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$seller->id.'.'.$seller->getImgFormat()))
                $this->errors[] = Tools::displayError('An error occurred while uploading the image.');

            if (count($this->errors))
                return false;
            unlink($tmp_name);
            return true;
        }
        return true;
    }
 }
?>