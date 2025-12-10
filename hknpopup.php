<?php
/**
 * PrestaShop Module: Pop-up Manager
 *
 * Advanced popup manager with scheduling, targeting, animations, and triggers.
 *
 * @author    Daniel Ionascu <danielionascudev@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version   3.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'hknpopup/classes/HknPopupTableClasses.php';

class Hknpopup extends Module
{
    /**
     * Module constructor
     */
    public function __construct()
    {
        $this->name = 'hknpopup';
        $this->tab = 'front_office_features';
        $this->version = '3.0.0';
        $this->author = 'Daniel Ionascu';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pop-up Manager');
        $this->description = $this->l('Advanced popup manager with scheduling, targeting, animations, and multiple trigger options.');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    /**
     * Module installation
     */
    public function install()
    {
        // Create uploads directory
        $uploadDir = _PS_MODULE_DIR_ . $this->name . '/uploads/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        return parent::install()
            && $this->installDb()
            && $this->installTab()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayCategoryFooter');
    }

    /**
     * Module uninstallation
     */
    public function uninstall()
    {
        return $this->uninstallDb()
            && $this->uninstallTab()
            && $this->deleteUploadedFiles()
            && parent::uninstall();
    }

    /**
     * Drop database tables
     */
    private function uninstallDb()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'hkn_popup_lang`';
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'hkn_popup`';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete all uploaded images
     */
    private function deleteUploadedFiles()
    {
        $uploadDir = _PS_MODULE_DIR_ . $this->name . '/uploads/';

        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep') {
                    @unlink($file);
                }
            }
        }

        return true;
    }

    /**
     * Create database tables
     */
    private function installDb()
    {
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'hkn_popup` (
            `id_popup` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `popup_type` VARCHAR(50) NOT NULL DEFAULT "classic",
            `title` VARCHAR(255) NOT NULL,
            `url` VARCHAR(255) DEFAULT NULL,
            `hook` VARCHAR(100) NOT NULL DEFAULT "all",
            `placement` VARCHAR(50) NOT NULL DEFAULT "middle_center",
            `delay` INT(11) NOT NULL DEFAULT 2,
            `active` TINYINT(1) NOT NULL DEFAULT 0,
            `date_start` DATETIME DEFAULT NULL,
            `date_end` DATETIME DEFAULT NULL,
            `image_desktop` VARCHAR(255) DEFAULT NULL,
            `image_mobile` VARCHAR(255) DEFAULT NULL,
            `trigger_type` VARCHAR(50) NOT NULL DEFAULT "delay",
            `trigger_value` INT(11) NOT NULL DEFAULT 2,
            `animation` VARCHAR(50) NOT NULL DEFAULT "fade",
            `show_close_btn` TINYINT(1) NOT NULL DEFAULT 1,
            `close_on_overlay` TINYINT(1) NOT NULL DEFAULT 1,
            `auto_close` INT(11) NOT NULL DEFAULT 0,
            `cookie_days` INT(11) NOT NULL DEFAULT 0,
            `show_once_session` TINYINT(1) NOT NULL DEFAULT 0,
            `target_visitor` VARCHAR(50) NOT NULL DEFAULT "all",
            `target_device` VARCHAR(50) NOT NULL DEFAULT "all",
            `target_categories` TEXT DEFAULT NULL,
            `target_products` TEXT DEFAULT NULL,
            `target_customer_groups` TEXT DEFAULT NULL,
            `priority` INT(11) NOT NULL DEFAULT 0,
            `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `date_upd` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_popup`),
            KEY `active` (`active`),
            KEY `hook` (`hook`),
            KEY `date_start` (`date_start`),
            KEY `date_end` (`date_end`),
            KEY `priority` (`priority`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'hkn_popup_lang` (
            `id_popup` INT(11) UNSIGNED NOT NULL,
            `id_lang` INT(11) UNSIGNED NOT NULL,
            `popup_content` TEXT DEFAULT NULL,
            PRIMARY KEY (`id_popup`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Install admin tab
     */
    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminPopupTable';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $tab->icon = 'message';

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Pop-up Manager';
        }

        return $tab->add();
    }

    /**
     * Uninstall admin tab
     */
    private function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminPopupTable');
        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return true;
    }

    /**
     * Module configuration page
     */
    public function getContent()
    {
        $output = '';

        // Link to popup manager
        $adminLink = $this->context->link->getAdminLink('AdminPopupTable');
        $output .= '<div class="panel">
            <h3><i class="icon-cog"></i> ' . $this->l('Pop-up Manager') . '</h3>
            <p>' . $this->l('Manage your popups from the dedicated admin page.') . '</p>
            <a href="' . $adminLink . '" class="btn btn-primary">
                <i class="icon-external-link"></i> ' . $this->l('Go to Pop-up Manager') . '
            </a>
        </div>';

        return $output;
    }

    /**
     * Add CSS/JS to back office
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') === 'AdminPopupTable') {
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
        }
    }

    /**
     * Add CSS/JS to front office header
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
    }

    /**
     * Add JS definitions for AJAX
     */
    public function hookActionFrontControllerSetMedia()
    {
        Media::addJsDef(array(
            'hknpopup_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax'),
            'hknpopup_module_path' => $this->_path,
        ));
    }

    /**
     * Display popup on homepage
     */
    public function hookDisplayHome($params)
    {
        return $this->renderPopups('displayHome');
    }

    /**
     * Display popup on product pages
     */
    public function hookDisplayFooterProduct($params)
    {
        return $this->renderPopups('displayFooterProduct');
    }

    /**
     * Display popup on category pages
     */
    public function hookDisplayCategoryFooter($params)
    {
        return $this->renderPopups('displayCategoryFooter');
    }

    /**
     * Render popups for a specific hook
     *
     * @param string $hookName
     * @return string|false
     */
    private function renderPopups($hookName)
    {
        $idLang = $this->context->language->id;
        $context = $this->buildTargetingContext();

        // Get active popups for this hook
        $popups = HknPopupTableClasses::getActivePopups($hookName, $idLang, $context);

        if (!$popups) {
            return false;
        }

        // Filter by cookie/session
        $visiblePopups = array();
        foreach ($popups as $popup) {
            $cookieDays = (int) $popup['cookie_days'];
            $showOnce = (bool) $popup['show_once_session'];

            if (HknPopupTableClasses::shouldShowPopup($popup['id_popup'], $cookieDays, $showOnce)) {
                $visiblePopups[] = $this->preparePopupData($popup);
            }
        }

        if (empty($visiblePopups)) {
            return false;
        }

        $this->context->smarty->assign(array(
            'popups' => $visiblePopups,
            'module_path' => $this->_path,
        ));

        return $this->display(__FILE__, 'views/templates/hook/hknpopup.tpl');
    }

    /**
     * Build targeting context from current page
     *
     * @return array
     */
    private function buildTargetingContext()
    {
        $context = array();

        // Visitor type
        if ($this->context->customer->isLogged()) {
            $context['visitor_type'] = 'logged_in';
            $context['customer_groups'] = $this->context->customer->getGroups();
        } else {
            $context['visitor_type'] = 'guest';
            $context['customer_groups'] = array((int) Configuration::get('PS_GUEST_GROUP'));
        }

        // Check if returning visitor (cookie based)
        if (isset($_COOKIE['hknpopup_visitor'])) {
            $context['visitor_type'] = $this->context->customer->isLogged() ? 'logged_in' : 'returning';
        } else {
            if ($context['visitor_type'] === 'guest') {
                $context['visitor_type'] = 'new';
            }
            setcookie('hknpopup_visitor', '1', time() + (365 * 86400), '/');
        }

        // Device detection
        $context['device'] = $this->detectDevice();

        // Current category
        $idCategory = (int) Tools::getValue('id_category');
        if ($idCategory) {
            $context['id_category'] = $idCategory;
        }

        // Current product
        $idProduct = (int) Tools::getValue('id_product');
        if ($idProduct) {
            $context['id_product'] = $idProduct;
        }

        return $context;
    }

    /**
     * Simple device detection
     *
     * @return string desktop|mobile|tablet
     */
    private function detectDevice()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        // Tablet detection
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        // Mobile detection
        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Prepare popup data for template
     *
     * @param array $popup
     * @return array
     */
    private function preparePopupData($popup)
    {
        return array(
            'id' => (int) $popup['id_popup'],
            'type' => $popup['popup_type'],
            'content' => $popup['popup_content'],
            'url' => $popup['url'],
            'placement' => $popup['placement'],
            'image_desktop' => $popup['image_desktop'],
            'image_mobile' => $popup['image_mobile'],
            'trigger_type' => $popup['trigger_type'],
            'trigger_value' => (int) $popup['trigger_value'],
            'animation' => $popup['animation'],
            'show_close_btn' => (bool) $popup['show_close_btn'],
            'close_on_overlay' => (bool) $popup['close_on_overlay'],
            'auto_close' => (int) $popup['auto_close'],
            'cookie_days' => (int) $popup['cookie_days'],
            'show_once_session' => (bool) $popup['show_once_session'],
        );
    }
}
