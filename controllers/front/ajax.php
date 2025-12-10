<?php
/**
 * PrestaShop Module: Pop-up Manager
 *
 * AJAX Controller for frontend popup interactions
 *
 * @author    Daniel Ionascu <danielionascudev@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'hknpopup/classes/HknPopupTableClasses.php';

class HknpopupAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * @var bool Disable layout for AJAX responses
     */
    public $ajax = true;

    /**
     * Initialize and route AJAX requests
     */
    public function initContent()
    {
        parent::initContent();

        $action = Tools::getValue('action');

        switch ($action) {
            case 'markShown':
                $this->markPopupShown();
                break;

            case 'getPopup':
                $this->getPopup();
                break;

            default:
                $this->ajaxResponse(false, 'Invalid action');
        }
    }

    /**
     * Mark a popup as shown (set cookie/session on server side)
     */
    protected function markPopupShown()
    {
        $idPopup = (int) Tools::getValue('id_popup');
        $cookieDays = (int) Tools::getValue('cookie_days');
        $showOnceSession = (bool) Tools::getValue('show_once_session');

        if (!$idPopup) {
            $this->ajaxResponse(false, 'Missing popup ID');
            return;
        }

        HknPopupTableClasses::markPopupShown($idPopup, $cookieDays, $showOnceSession);

        $this->ajaxResponse(true, 'Popup marked as shown');
    }

    /**
     * Get popup data by ID
     */
    protected function getPopup()
    {
        $idPopup = (int) Tools::getValue('id_popup');
        $idLang = (int) $this->context->language->id;

        if (!$idPopup) {
            $this->ajaxResponse(false, 'Missing popup ID');
            return;
        }

        $popup = HknPopupTableClasses::getPopupById($idPopup, $idLang);

        if (!$popup) {
            $this->ajaxResponse(false, 'Popup not found');
            return;
        }

        $this->ajaxResponse(true, 'Success', $popup);
    }

    /**
     * Send JSON response
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     */
    protected function ajaxResponse($success, $message = '', $data = null)
    {
        $response = array(
            'success' => $success,
            'message' => $message,
        );

        if ($data !== null) {
            $response['data'] = $data;
        }

        header('Content-Type: application/json');
        die(json_encode($response));
    }
}
