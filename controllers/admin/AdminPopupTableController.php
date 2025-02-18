<?php
/**
 * PrestaShop Module: Pop-up Manager
 *
 * This module is developed for use with PrestaShop. Redistribution or modification
 * is permitted without restriction. No warranties or support are provided.
 *
 * @author    Daniel Ionaşcu <danielhekn@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)>
 */

class AdminPopupTableController extends ModuleAdminController
{
    public function __construct()
    {

        parent::__construct();

        $this->bootstrap = true;

        $this->table = 'hkn_popup';
        $this->className = 'HknPopupTableClasses';
        $this->identifier = 'id_popup';
        $this->allow_export = false;
        $this->_defaultOrderBy = 'a.id_popup';
        $this->_defaultOrderWay = 'ASC';
        $this->lang = true;

        $this->_select = '
        a.id_popup as page_pos,
        a.id_popup as hook';

        $this->fields_list = array(
            'id_popup' => array(
                'title' => 'ID',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'filter_key' => 'a!',
                'remove_onclick' => true,
            ),
            'title' => array(
                'title' => 'Name',
                'align' => 'center',
                'search' => true,
                'type' => 'search',
            ),
            'hook' => array(
                'title' => $this->l('Hook'),
                'class' => 'fixed-width-lg',
                'align' => 'center',
                'search' => false,
                'callback' => 'getHookName',
            ),
            'placement' => array(
                'title' => $this->l('Placement'),
                'class' => 'fixed-width-lg',
                'align' => 'center',
                'search' => false,
                'callback' => 'getPopupName',
            ),
            'delay' => array(
                'title' => $this->l('Delay'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'suffix' => 's',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'active' => 'status',
                'search' => false,
                'type' => 'bool',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete'),
                'confirm' => $this->l('Delete selected rows?'),
            )
        );
    }

    public function init()
    {
        parent::init();
    }

    public function postProcess()
    {
        return parent::postProcess();
    }


    public function renderList()
    {

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $isEdit = (int)Tools::getValue('id_popup') > 0;

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Landing Page Settings'),
                'icon' => 'icon-list-ul',
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_popup',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Popup Type'),
                    'name' => 'popup_type',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('type' => 'classic', 'name' => $this->l('Classic')),
                            array('type' => 'images_only', 'name' => $this->l('Images Only')),
                            array('type' => 'html', 'name' => $this->l('HTML')),
                            array('type' => 'overlay', 'name' => $this->l('Overlay')),
                        ),
                        'id' => 'type',
                        'name' => 'name'
                    ),
                    'hint' => $this->l("Select the type of the popup. Classic - normal pop-up. Overlay - overlay pop-up with accept button."),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'required' => true,
                    'hint' => $this->l("Set your popup name."),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'popup_content',
                    'required' => true,
                    'lang' => true, // Make the field multilingual
                    'hint' => $this->l("HTML Pop-up Content"),
                    'autoload_rte' => true, // If you want to enable the rich text editor
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link'),
                    'name' => 'url',
//                    'required' => true,
                    'hint' => $this->l("Set a url for your pop-up (Optional)"),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Delay:'),
                    'name' => 'delay',
                    'default_value' => 2,
                    'class' => 'fixed-width-sm',
                    'suffix' => 's',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Pop-up Placement'),
                    'name' => 'placement',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('placement' => 'top_left', 'name' => $this->l('Top Left')),
                            array('placement' => 'top_center', 'name' => $this->l('Top Center')),
                            array('placement' => 'top_right', 'name' => $this->l('Top Right')),
                            array('placement' => 'middle_left', 'name' => $this->l('Middle Left')),
                            array('placement' => 'middle_center', 'name' => $this->l('Center')),
                            array('placement' => 'middle_right', 'name' => $this->l('Middle Right')),
                            array('placement' => 'bottom_left', 'name' => $this->l('Bottom Left')),
                            array('placement' => 'bottom_center', 'name' => $this->l('Bottom Center')),
                            array('placement' => 'bottom_right', 'name' => $this->l('Bottom Right')),
                        ),
                        'id' => 'placement',
                        'name' => 'name'
                    ),
                    'hint' => $this->l("Select the placement on the page where the pop-up will appear."),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Hook'),
                    'name' => 'hook',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('hook' => 'all', 'name' => $this->l('All Website')),
                            array('hook' => 'hookDisplayHome', 'name' => $this->l('Homepage')),
                            array('hook' => 'hookDisplayCategoryFooter', 'name' => $this->l('Category Page')),
                            array('hook' => 'hookDisplayFooterProduct', 'name' => $this->l('Product Page')),
                            // Add more hooks as needed
                        ),
                        'id' => 'hook',
                        'name' => 'name',
                    ),
                    'hint' => $this->l("Select the hook for your module."),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'hint' => $this->l("Enable or disable this option."),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
            ),
//            'submit' => array(
//                'title' => $this->l('Save'),
//            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        // Load the object if editing an existing record
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->controller_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
//        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? $lang['id_lang'] : 0),
            ];
        }
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.

        // Generate the form
        return parent::renderForm();
    }


    public function getHookName($id_popup)
    {
        $query = 'SELECT `hook` FROM `' . _DB_PREFIX_ . 'hkn_popup` WHERE `id_popup` = ' . $id_popup;
        $hook = Db::getInstance()->getValue($query);
        $hook_name = '-';
        if ($hook == 'hookDisplayHome') {
            $hook_name = $this->l('Homepage');
        } elseif ($hook == 'hookDisplayFooterProduct'){
            $hook_name = $this->l('Product Page');
        } elseif ($hook == 'hookDisplayCategoryFooter'){
            $hook_name = $this->l('Category Page');
        } else {
            $hook_name = $this->l('All Website');
        }

        return $hook_name;
    }

    public function getPopupName($id_popup)
    {
        $query = 'SELECT `placement` FROM `' . _DB_PREFIX_ . 'hkn_popup` WHERE `id_popup` = ' . $id_popup;
        $popup_pos = Db::getInstance()->getValue($query);
        $popup_name = '-';

        if ($popup_pos == 'pop_tl') {
            $popup_name = $this->l('Top Left');
        } elseif ($popup_pos == 'pop_tr') {
            $popup_name = $this->l('Top Left');
        } elseif ($popup_pos == 'pop_bl') {
            $popup_name = $this->l('Bottom Left');
        } elseif ($popup_pos == 'pop_br') {
            $popup_name = $this->l('Bottom Right');
        } elseif ($popup_pos == 'pop_mc') {
            $popup_name = $this->l('Center');
        }

        return $popup_name;
    }
}
