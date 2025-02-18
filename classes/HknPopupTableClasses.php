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
class HknPopupTableClasses extends ObjectModel
{
    public $id_popup;
    public $title_bo;
    public $popup_content;
    public $link;
    public $hook;
    public $placement;
    public $delay;
    public $active;

    public static $definition = array(
        'table' => 'hkn_popup',
        'multilang' => true,
        'primary' => 'id_popup',
        'fields' => array(
            'popup_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => false, 'comment' => 'Type of popup'),
            'title_bo' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => false, 'comment' => 'Template Name (Back Office)'),
            'url' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false, 'comment' => 'Link of popup'),
            'hook' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false, 'comment' => 'List of hooks for the template'),
            'placement' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false, 'comment' => 'Placement on page.'),
            'delay' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false, 'comment' => 'Set when the popup to appear'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false, 'comment' => 'Active status (0 or 1)'),
            'popup_content' => array('type' => self::TYPE_HTML, 'validate' => 'isAnything', 'required' => true, 'comment' => 'HTML', 'lang' => true),
        ),
    );

}