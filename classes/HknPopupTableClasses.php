<?php
/**
 * PrestaShop Module: Pop-up Manager
 *
 * @author    Daniel Ionascu <danielionascudev@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class HknPopupTableClasses extends ObjectModel
{
    /** @var int */
    public $id_popup;

    /** @var string Popup type: classic, images_only, html, overlay */
    public $popup_type;

    /** @var string Title for Back Office */
    public $title;

    /** @var string Popup content (multilang) */
    public $popup_content;

    /** @var string Optional URL link */
    public $url;

    /** @var string Hook name for display */
    public $hook;

    /** @var string Position on page */
    public $placement;

    /** @var int Display delay in seconds (legacy - use trigger_value) */
    public $delay;

    /** @var bool Active status */
    public $active;

    /** @var string Start date for scheduling */
    public $date_start;

    /** @var string End date for scheduling */
    public $date_end;

    /** @var string Desktop image path */
    public $image_desktop;

    /** @var string Mobile image path */
    public $image_mobile;

    /** @var string Trigger type: delay, scroll, exit_intent, click */
    public $trigger_type;

    /** @var int Trigger value (seconds for delay, percentage for scroll) */
    public $trigger_value;

    /** @var string Animation: fade, slide_up, slide_down, slide_left, slide_right, zoom */
    public $animation;

    /** @var bool Show close button */
    public $show_close_btn;

    /** @var bool Close popup when clicking overlay */
    public $close_on_overlay;

    /** @var int Auto close after X seconds (0 = disabled) */
    public $auto_close;

    /** @var int Don't show again for X days (0 = always show) */
    public $cookie_days;

    /** @var bool Show only once per session */
    public $show_once_session;

    /** @var string Target visitor: all, new, returning, logged_in, guest */
    public $target_visitor;

    /** @var string Target device: all, desktop, mobile, tablet */
    public $target_device;

    /** @var string JSON array of category IDs */
    public $target_categories;

    /** @var string JSON array of product IDs */
    public $target_products;

    /** @var string JSON array of customer group IDs */
    public $target_customer_groups;

    /** @var int Priority (higher = shows first) */
    public $priority;

    /** @var string Date added */
    public $date_add;

    /** @var string Date updated */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'hkn_popup',
        'primary' => 'id_popup',
        'multilang' => true,
        'fields' => array(
            // Basic settings
            'popup_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => false,
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 255,
                'required' => true,
            ),
            'url' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl',
                'size' => 255,
                'required' => false,
            ),
            'hook' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 100,
                'required' => true,
            ),
            'placement' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => true,
            ),
            'delay' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ),

            // Scheduling
            'date_start' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => false,
            ),
            'date_end' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => false,
            ),

            // Images
            'image_desktop' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 255,
                'required' => false,
            ),
            'image_mobile' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 255,
                'required' => false,
            ),

            // Trigger settings
            'trigger_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => false,
            ),
            'trigger_value' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ),

            // Animation
            'animation' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => false,
            ),

            // Close button settings
            'show_close_btn' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ),
            'close_on_overlay' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ),
            'auto_close' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ),

            // Cookie/Session settings
            'cookie_days' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ),
            'show_once_session' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ),

            // Targeting
            'target_visitor' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => false,
            ),
            'target_device' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => false,
            ),
            'target_categories' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
            ),
            'target_products' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
            ),
            'target_customer_groups' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
            ),

            // Priority & dates
            'priority' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => false,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => false,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => false,
            ),

            // Multilang content
            'popup_content' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'required' => false,
                'lang' => true,
            ),
        ),
    );

    /**
     * Get all active popups for a specific hook
     *
     * @param string $hook Hook name
     * @param int $idLang Language ID
     * @param array $context Additional context for targeting
     * @return array|false
     */
    public static function getActivePopups($hook, $idLang, $context = array())
    {
        $now = date('Y-m-d H:i:s');

        $sql = new DbQuery();
        $sql->select('p.*, pl.popup_content');
        $sql->from('hkn_popup', 'p');
        $sql->leftJoin('hkn_popup_lang', 'pl', 'p.id_popup = pl.id_popup AND pl.id_lang = ' . (int)$idLang);
        $sql->where('p.active = 1');
        $sql->where('(p.hook = "' . pSQL($hook) . '" OR p.hook = "all")');
        $sql->where('(p.date_start IS NULL OR p.date_start <= "' . pSQL($now) . '")');
        $sql->where('(p.date_end IS NULL OR p.date_end >= "' . pSQL($now) . '")');
        $sql->orderBy('p.priority DESC, p.id_popup ASC');

        $results = Db::getInstance()->executeS($sql);

        if (!$results) {
            return false;
        }

        // Filter by targeting rules
        $filtered = array();
        foreach ($results as $popup) {
            if (self::checkTargeting($popup, $context)) {
                $filtered[] = $popup;
            }
        }

        return !empty($filtered) ? $filtered : false;
    }

    /**
     * Get single popup by ID
     *
     * @param int $idPopup
     * @param int $idLang
     * @return array|false
     */
    public static function getPopupById($idPopup, $idLang)
    {
        $sql = new DbQuery();
        $sql->select('p.*, pl.popup_content');
        $sql->from('hkn_popup', 'p');
        $sql->leftJoin('hkn_popup_lang', 'pl', 'p.id_popup = pl.id_popup AND pl.id_lang = ' . (int)$idLang);
        $sql->where('p.id_popup = ' . (int)$idPopup);

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Check if popup matches targeting rules
     *
     * @param array $popup Popup data
     * @param array $context Context data (visitor, device, category, product, groups)
     * @return bool
     */
    public static function checkTargeting($popup, $context)
    {
        // Check visitor type
        if (!empty($popup['target_visitor']) && $popup['target_visitor'] !== 'all') {
            $visitorType = isset($context['visitor_type']) ? $context['visitor_type'] : 'all';
            if ($popup['target_visitor'] !== $visitorType) {
                return false;
            }
        }

        // Check device
        if (!empty($popup['target_device']) && $popup['target_device'] !== 'all') {
            $device = isset($context['device']) ? $context['device'] : 'desktop';
            if ($popup['target_device'] !== $device) {
                return false;
            }
        }

        // Check categories
        if (!empty($popup['target_categories'])) {
            $targetCats = json_decode($popup['target_categories'], true);
            if (is_array($targetCats) && !empty($targetCats)) {
                $currentCat = isset($context['id_category']) ? (int)$context['id_category'] : 0;
                if ($currentCat && !in_array($currentCat, $targetCats)) {
                    return false;
                }
            }
        }

        // Check products
        if (!empty($popup['target_products'])) {
            $targetProds = json_decode($popup['target_products'], true);
            if (is_array($targetProds) && !empty($targetProds)) {
                $currentProd = isset($context['id_product']) ? (int)$context['id_product'] : 0;
                if ($currentProd && !in_array($currentProd, $targetProds)) {
                    return false;
                }
            }
        }

        // Check customer groups
        if (!empty($popup['target_customer_groups'])) {
            $targetGroups = json_decode($popup['target_customer_groups'], true);
            if (is_array($targetGroups) && !empty($targetGroups)) {
                $customerGroups = isset($context['customer_groups']) ? $context['customer_groups'] : array();
                if (!empty($customerGroups) && empty(array_intersect($targetGroups, $customerGroups))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if popup should be shown based on cookie/session
     *
     * @param int $idPopup
     * @param int $cookieDays
     * @param bool $showOnceSession
     * @return bool
     */
    public static function shouldShowPopup($idPopup, $cookieDays = 0, $showOnceSession = false)
    {
        $cookieName = 'hknpopup_' . (int)$idPopup;

        // Check cookie
        if ($cookieDays > 0 && isset($_COOKIE[$cookieName])) {
            return false;
        }

        // Check session
        if ($showOnceSession && isset($_SESSION['hknpopup_shown_' . (int)$idPopup])) {
            return false;
        }

        return true;
    }

    /**
     * Mark popup as shown (set cookie/session)
     *
     * @param int $idPopup
     * @param int $cookieDays
     * @param bool $showOnceSession
     */
    public static function markPopupShown($idPopup, $cookieDays = 0, $showOnceSession = false)
    {
        $cookieName = 'hknpopup_' . (int)$idPopup;

        if ($cookieDays > 0) {
            setcookie($cookieName, '1', time() + ($cookieDays * 86400), '/');
        }

        if ($showOnceSession) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['hknpopup_shown_' . (int)$idPopup] = true;
        }
    }
}
