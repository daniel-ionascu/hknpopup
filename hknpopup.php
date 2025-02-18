<?php
/**
 * PrestaShop Module: Pop-up Manager
 *
 * This module is developed for use with PrestaShop. Redistribution or modification
 * is permitted without restriction. No warranties or support are provided.
 *
 * @author    Daniel Ionaşcu <danielhekn@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)>
 * 
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . 'hknpopup/classes/HknPopupTableClasses.php');

class hknpopup extends Module
{
    public function __construct()
    {
        $this->name = 'hknpopup';
        $this->tab = 'AdminThemes'; //Test if works - others
        $this->version = '2.0.0';
        $this->author = 'Daniel Ionaşcu';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->displayName = $this->l('Pop-up Manager');
        $this->description = $this->l('This module allows you to display a customizable pop-up on various pages, including the homepage and product pages. It offers configurable settings such as position, desktop and mobile images, display delay, and supports multiple hooks for placement.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        parent::__construct();
    }

    public function install()
    {
        Configuration::updateValue('HKNPOPUP_LIVE_MODE', false);

        if (!parent::install() ||
            !$this->installTab('AdminParentThemes', 'AdminPopupTable', $this->l('Pop-up Manager'), 'branding_watermark') ||
            !$this->_installTable() ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('customViewProductCode') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('displayHome')){
            return false;
        };

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('HKNPOPUP_LIVE_MODE');

        return $this->removeTab('AdminPopupConfiguration') &&
            parent::uninstall();
    }

    public function _installTable()
    {
        $sql_array = array();

        $sql_array[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'hkn_popup (
                id_popup int(11) NOT NULL AUTO_INCREMENT,
                popup_type VARCHAR(255) NOT NULL,
                title_bo VARCHAR(255) NOT NULL,
                url VARCHAR(100) NOT NULL,
                hook VARCHAR(100) NOT NULL,
                placement VARCHAR(50) NOT NULL,
                delay int(11) NOT NULL,
                active tinyint(1) NOT NULL,
                PRIMARY KEY (id_popup)
                ) ENGINE = InnoDB  DEFAULT CHARSET = utf8';

        $sql_array[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'hkn_popup_lang (
                id_popup int(11) NOT NULL,
                id_lang int(11) NOT NULL,
                popup_content text CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id_popup`, `id_lang`)
                ) ENGINE = InnoDB  DEFAULT CHARSET = utf8';

        foreach($sql_array as $sql){
            if (!Db::getInstance()->execute($sql)) return false;
        }

        return true;
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitHknpopupModule')) == true) {
            $this->postProcess();
        }
        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    private function installTab($parent_class, $class_name, $title, $icon = false)
    {
        $tab = new Tab();
        if (!$parent_class) {
            $main_id = Tab::getIdFromClassName('SELL');
            $tab->id_parent = $main_id;
        } else {
            $tab->id_parent = (int)Tab::getIdFromClassName($parent_class);
        }
        $tab->name = [];
        foreach (Language::getLanguages() as $language) {
            $tab->name[$language['id_lang']] = $title;
        }
        $tab->icon = $icon;
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        if (!$tab->add()) {
            return false;
        }

        return true;
    }

    private function removeTab($class)
    {
        $id_tab = Tab::getIdFromClassName($class);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitHknpopupModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'HKNPOPUP_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'HKNPOPUP_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'HKNPOPUP_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'HKNPOPUP_LIVE_MODE' => Configuration::get('HKNPOPUP_LIVE_MODE', true),
            'HKNPOPUP_ACCOUNT_EMAIL' => Configuration::get('HKNPOPUP_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'HKNPOPUP_ACCOUNT_PASSWORD' => Configuration::get('HKNPOPUP_ACCOUNT_PASSWORD', null),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        
        // black friday
        $bf_start_date = '2024-11-08 08:00:00';
        $bf_end_date = '2024-11-29 23:59:59';
        $bf_current_datetime = date('Y-m-d H:i:s');
        if ($bf_current_datetime >= $bf_start_date && $bf_current_datetime <= $bf_end_date ) {
            $this->context->smarty->assign(array(
                'th_bf_url' => 'https://luxfer.ro/black-friday',
            ));

            return $this->display(__FILE__, 'views/templates/hook/black_friday.tpl');
        }
        // black friday

        $context = Context::getContext();
        $id_product = Tools::getValue('id_product');

        $current_datetime = date('Y-m-d H:i:s');
        if(Tools::getValue('controller') != 'order'){

            if(Tools::getValue('controller') == 'product' && $id_product == 2581){
                $start_date = '2024-03-12 01:00:00';
                $end_date = '2024-03-22 23:50:00';

                $product = true;
//                $message = '<span>În această perioadă <span style="font-weight:700">SERVICIUL DE DEBITARE RAME</span> este oprit pentru revizie utilaje. Iți putem livra comanda de rame debitate începand cu 10 ianuarie 2024. </span><span>Mulțumim pentru înțelegere!</span>';
                $message = '<span>În această perioada serviciul de debitare rame este oprit . Îți putem livra comandă de rame debitate începând cu 02 aprilie 2024. </span><span>Mulțumim pentru înțelegere. Cu stimă Echipa Luxfer !</span>';

            }
    //            else {
    //                $start_date = '2023-12-29 01:00:00';
    //                $end_date = '2024-01-09 23:50:00';
    //
    //                    $message = '<span>În această perioadă suntem în inventar. </span><span>Îți putem livra comanda începand cu 10 ianuarie 2024. </span><span>Mulțumim!</span>';
    //            }
            if(isset($start_date) && isset($end_date))
            if($current_datetime >= $start_date && $current_datetime <= $end_date ) {
                $this->smarty->assign(array(
                    'product' => $product,
                    'message_all' => $message,
                ));

                return $this->display(__FILE__, 'views/templates/hook/delivery_msg.tpl');
            }
        }

        return false;
    }

    public function hookDisplayMsgCheckout()
    {
//         return false;
        $message_checkout = 'Salut! Te informăm că în perioada 20.12.2024 - 07.01.2025, depozitul <a href="/">Luxfer.ro</a> este închis. Comanda ta va fi prelucrată începând cu data de 08.01.2025. Sărbători fericite și An Nou Fericit!';
//        $context = Context::getContext();
//        $cart_id = (int)$context->cart->id;
//        $cart = new Cart($cart_id);
//        $cart_details = $cart->getProducts();
//
        $current_datetime = date('Y-m-d H:i:s');
        $start_date = '2024-12-20 10:00:00';
        $end_date = '2025-01-08 06:00:00';
//
//        foreach ($cart_details as $cart_detail) {
//            if($cart_detail['id_product'] == 2581){
//                $start_date = '2024-03-12 01:00:00';
//                $end_date = '2024-03-22 23:50:00';
//                $id_product = true;
//                $message_checkout = '<span>În această perioada serviciul de debitare rame este oprit . Îți putem livra comandă de rame debitate începând cu 02 aprilie 2024. </span><br><span>Mulțumim pentru înțelegere. Cu stimă Echipa Luxfer !</span>';
//                break;
//            }
//        }
//
//        if(isset($start_date) && isset($end_date))
        if($current_datetime >= $start_date && $current_datetime <= $end_date) {
            $this->smarty->assign(array(
                'message_checkout' => $message_checkout,
            ));
            return $this->display(__FILE__, 'views/templates/hook/delivery_checkout.tpl');
        }

        return false;
    }

    public function hookActionFrontControllerSetMedia()
    {
        Media::addJsDef(array('hknpopup_url' => $this->context->link->getModuleLink('hknpopup','ajax')));

    }

    public function hookDisplayHome($params)
    {

        $get_tpl = $this->getContentTemplate('hookDisplayHome', $this->context->language->id);
        $get_all = $this->getContentTemplate('all', $this->context->language->id);

        if($get_tpl !== false){
            $this->smarty->assign(array(
                'id_popup' => $get_tpl['id_popup'],
                'content' => $get_tpl['popup_content'],
                'url' => $get_tpl['url'],
                'position' => $get_tpl['page_pos'],
            ));
            return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
        }
        if($get_all !== false){
            $this->smarty->assign(array(
                'id_popup' => $get_all['id_popup'],
                'content' => $get_all['popup_content'],
                'url' => $get_all['url'],
                'position' => $get_all['page_pos'],
            ));
            return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
        }

        return false;
    }

    public function hookDisplayFooterProduct($params)
    {
        $get_tpl = $this->getContentTemplate('hookDisplayFooterProduct', $this->context->language->id);
        $get_all = $this->getContentTemplate('all', $this->context->language->id);

        if($get_tpl !== false){
            $this->smarty->assign(array(
                'id_popup' => $get_tpl['id_popup'],
                'content' => $get_tpl['popup_content'],
                'url' => $get_tpl['url'],
                'position' => $get_tpl['page_pos'],
            ));
            return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
        }
        if($get_all !== false){
            $this->smarty->assign(array(
                'id_popup' => $get_all['id_popup'],
                'content' => $get_all['popup_content'],
                'url' => $get_all['url'],
                'position' => $get_all['page_pos'],
            ));
            return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
        }
        return false;
    }

    public function hookDisplayCategoryFooter($params)
    {
        $get_tpl = $this->getContentTemplate('hookDisplayCategoryFooter', $this->context->language->id);
        $get_all = $this->getContentTemplate('all', $this->context->language->id);

        if($get_tpl !== false){
            $this->smarty->assign(array(
                'id_popup' => $get_tpl['id_popup'],
                'content' => $get_tpl['popup_content'],
                'url' => $get_tpl['url'],
                'position' => $get_tpl['page_pos'],
            ));
            return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
        }
        if($get_all !== false){
            $this->smarty->assign(array(
                'id_popup' => $get_all['id_popup'],
                'content' => $get_all['popup_content'],
                'url' => $get_all['url'],
                'position' => $get_all['page_pos'],
            ));
            return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
        }

        return false;
    }
    public function getContentTemplate($hook, $id_lang)
    {
        $query = 'SELECT h.`id_popup`, hl.`popup_content`, h.`page_pos`, h.`url`
            FROM `' . _DB_PREFIX_ .'hknpopup_lang` hl
            LEFT JOIN `' . _DB_PREFIX_ .'hknpopup` h
            ON h.`id_popup` = hl.`id_popup`
            WHERE h.`hook` = "' . $hook . '"
            AND h.active = 1
            AND hl.`id_lang` = ' .$id_lang;

        return Db::getInstance()->getRow($query);
    }

    public function hookCustomViewProductCode()
    {
      $cookies = new Cookie('psAdmin');
        if ($cookies->id_employee) {
            return true;
        }
      return false;
    }
}
