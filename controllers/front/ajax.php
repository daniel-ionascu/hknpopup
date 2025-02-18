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
class ThpopupAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $data = array();
        $errors = false;
        $action = Tools::getValue('action');

        if($action == 'getDelay'){
            $id_template = Tools::getValue('id_template');

            $query = 'SELECT delay FROM ' . _DB_PREFIX_ .'thpopup_templates WHERE id_template = ' .$id_template;
            $delay = (int)(Db::getInstance()->getValue($query) . '000');

            $data = [
                'errors' => $errors,
                'delay' => $delay,
            ];
            $this->ajaxDie(Tools::jsonEncode($data));
        }

    }
}