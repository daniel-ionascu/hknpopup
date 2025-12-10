<?php
/**
 * Upgrade script for hknpopup module v3.0.0
 * Adds new fields: scheduling, targeting, triggers, animations, cookie settings
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_0($module)
{
    $sql = array();

    // Add new columns to hkn_popup table
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'hkn_popup`
        ADD COLUMN IF NOT EXISTS `date_start` DATETIME DEFAULT NULL AFTER `active`,
        ADD COLUMN IF NOT EXISTS `date_end` DATETIME DEFAULT NULL AFTER `date_start`,
        ADD COLUMN IF NOT EXISTS `image_desktop` VARCHAR(255) DEFAULT NULL AFTER `date_end`,
        ADD COLUMN IF NOT EXISTS `image_mobile` VARCHAR(255) DEFAULT NULL AFTER `image_desktop`,
        ADD COLUMN IF NOT EXISTS `trigger_type` VARCHAR(50) DEFAULT "delay" AFTER `image_mobile`,
        ADD COLUMN IF NOT EXISTS `trigger_value` INT(11) DEFAULT 0 AFTER `trigger_type`,
        ADD COLUMN IF NOT EXISTS `animation` VARCHAR(50) DEFAULT "fade" AFTER `trigger_value`,
        ADD COLUMN IF NOT EXISTS `show_close_btn` TINYINT(1) DEFAULT 1 AFTER `animation`,
        ADD COLUMN IF NOT EXISTS `close_on_overlay` TINYINT(1) DEFAULT 1 AFTER `show_close_btn`,
        ADD COLUMN IF NOT EXISTS `auto_close` INT(11) DEFAULT 0 AFTER `close_on_overlay`,
        ADD COLUMN IF NOT EXISTS `cookie_days` INT(11) DEFAULT 0 AFTER `auto_close`,
        ADD COLUMN IF NOT EXISTS `show_once_session` TINYINT(1) DEFAULT 0 AFTER `cookie_days`,
        ADD COLUMN IF NOT EXISTS `target_visitor` VARCHAR(50) DEFAULT "all" AFTER `show_once_session`,
        ADD COLUMN IF NOT EXISTS `target_device` VARCHAR(50) DEFAULT "all" AFTER `target_visitor`,
        ADD COLUMN IF NOT EXISTS `target_categories` TEXT DEFAULT NULL AFTER `target_device`,
        ADD COLUMN IF NOT EXISTS `target_products` TEXT DEFAULT NULL AFTER `target_categories`,
        ADD COLUMN IF NOT EXISTS `target_customer_groups` TEXT DEFAULT NULL AFTER `target_products`,
        ADD COLUMN IF NOT EXISTS `priority` INT(11) DEFAULT 0 AFTER `target_customer_groups`,
        ADD COLUMN IF NOT EXISTS `date_add` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `priority`,
        ADD COLUMN IF NOT EXISTS `date_upd` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `date_add`';

    // Rename old column if exists (title_bo -> title)
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'hkn_popup`
        CHANGE COLUMN IF EXISTS `title_bo` `title` VARCHAR(255) NOT NULL';

    // Execute all SQL queries
    foreach ($sql as $query) {
        try {
            if (!Db::getInstance()->execute($query)) {
                // Log error but continue
                PrestaShopLogger::addLog('hknpopup upgrade 3.0.0 SQL error: ' . $query, 3);
            }
        } catch (Exception $e) {
            // Column might already exist, continue
            PrestaShopLogger::addLog('hknpopup upgrade 3.0.0 exception: ' . $e->getMessage(), 2);
        }
    }

    // Register new hooks
    $hooks = array(
        'displayHeader',
        'actionFrontControllerSetMedia',
        'displayFooterProduct',
        'displayCategoryFooter',
    );

    foreach ($hooks as $hook) {
        if (!$module->isRegisteredInHook($hook)) {
            $module->registerHook($hook);
        }
    }

    return true;
}
