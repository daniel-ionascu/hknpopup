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

class AdminPopupTableController extends ModuleAdminController
{
    /** @var string Upload directory path */
    protected $upload_dir;

    /** @var array Allowed image extensions */
    protected $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'hkn_popup';
        $this->className = 'HknPopupTableClasses';
        $this->identifier = 'id_popup';
        $this->lang = true;
        $this->allow_export = true;
        $this->_defaultOrderBy = 'priority';
        $this->_defaultOrderWay = 'DESC';

        $this->upload_dir = _PS_MODULE_DIR_ . 'hknpopup/uploads/';

        parent::__construct();

        $this->fields_list = array(
            'id_popup' => array(
                'title' => $this->l('ID'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'align' => 'left',
            ),
            'popup_type' => array(
                'title' => $this->l('Type'),
                'class' => 'fixed-width-md',
                'align' => 'center',
                'callback' => 'displayPopupType',
            ),
            'hook' => array(
                'title' => $this->l('Page'),
                'class' => 'fixed-width-lg',
                'align' => 'center',
                'callback' => 'displayHookName',
            ),
            'trigger_type' => array(
                'title' => $this->l('Trigger'),
                'class' => 'fixed-width-md',
                'align' => 'center',
                'callback' => 'displayTriggerType',
            ),
            'date_start' => array(
                'title' => $this->l('Start'),
                'class' => 'fixed-width-md',
                'align' => 'center',
                'type' => 'datetime',
            ),
            'date_end' => array(
                'title' => $this->l('End'),
                'class' => 'fixed-width-md',
                'align' => 'center',
                'type' => 'datetime',
            ),
            'priority' => array(
                'title' => $this->l('Priority'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selected'),
                'icon' => 'icon-check',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selected'),
                'icon' => 'icon-times',
            ),
        );
    }

    /**
     * Add row actions
     */
    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    /**
     * Render popup form
     */
    public function renderForm()
    {
        // Get current object for image preview
        $obj = $this->loadObject(true);
        $imageDesktopPreview = '';
        $imageMobilePreview = '';

        if ($obj && Validate::isLoadedObject($obj)) {
            if (!empty($obj->image_desktop) && file_exists($this->upload_dir . $obj->image_desktop)) {
                $imageDesktopPreview = '<div class="col-lg-9 col-lg-offset-3"><img src="' . _MODULE_DIR_ . 'hknpopup/uploads/' . $obj->image_desktop . '" style="max-width:300px;max-height:200px;margin-top:10px;border:1px solid #ddd;border-radius:4px;" /><br><a href="' . $this->context->link->getAdminLink('AdminPopupTable') . '&id_popup=' . (int)$obj->id . '&delete_image=desktop" class="btn btn-danger btn-xs" style="margin-top:5px;" onclick="return confirm(\'' . $this->l('Delete this image?') . '\');"><i class="icon-trash"></i> ' . $this->l('Delete') . '</a></div>';
            }
            if (!empty($obj->image_mobile) && file_exists($this->upload_dir . $obj->image_mobile)) {
                $imageMobilePreview = '<div class="col-lg-9 col-lg-offset-3"><img src="' . _MODULE_DIR_ . 'hknpopup/uploads/' . $obj->image_mobile . '" style="max-width:300px;max-height:200px;margin-top:10px;border:1px solid #ddd;border-radius:4px;" /><br><a href="' . $this->context->link->getAdminLink('AdminPopupTable') . '&id_popup=' . (int)$obj->id . '&delete_image=mobile" class="btn btn-danger btn-xs" style="margin-top:5px;" onclick="return confirm(\'' . $this->l('Delete this image?') . '\');"><i class="icon-trash"></i> ' . $this->l('Delete') . '</a></div>';
            }
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Popup Settings'),
                'icon' => 'icon-cog',
            ),
            'input' => array(
                // === GENERAL SECTION ===
                array(
                    'type' => 'html',
                    'name' => 'section_general',
                    'html_content' => '<h3 class="tab" style="background:#f5f5f5;padding:10px 15px;margin:0 -15px 15px;border-bottom:1px solid #ddd;"><i class="icon-cog"></i> ' . $this->l('General Settings') . '</h3>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'required' => true,
                    'hint' => $this->l('Internal name for identification'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Popup Type'),
                    'name' => 'popup_type',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('id' => 'classic', 'name' => $this->l('Classic (HTML + Optional Image)')),
                            array('id' => 'images_only', 'name' => $this->l('Image Only')),
                            array('id' => 'html', 'name' => $this->l('HTML Only')),
                            array('id' => 'overlay', 'name' => $this->l('Full Overlay (no content)')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Choose what type of content the popup will display'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Display On'),
                    'name' => 'hook',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('id' => 'all', 'name' => $this->l('All Pages')),
                            array('id' => 'displayHome', 'name' => $this->l('Homepage Only')),
                            array('id' => 'displayFooterProduct', 'name' => $this->l('Product Pages')),
                            array('id' => 'displayCategoryFooter', 'name' => $this->l('Category Pages')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link URL'),
                    'name' => 'url',
                    'hint' => $this->l('Optional: URL to open when popup/image is clicked'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Priority'),
                    'name' => 'priority',
                    'class' => 'fixed-width-sm',
                    'hint' => $this->l('Higher priority popups show first (0-100)'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'values' => array(
                        array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),

                // === CONTENT SECTION ===
                array(
                    'type' => 'html',
                    'name' => 'section_content',
                    'html_content' => '<h3 class="tab" style="background:#f5f5f5;padding:10px 15px;margin:20px -15px 15px;border-bottom:1px solid #ddd;"><i class="icon-pencil"></i> ' . $this->l('Content') . '</h3>',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML Content'),
                    'name' => 'popup_content',
                    'lang' => true,
                    'autoload_rte' => true,
                    'hint' => $this->l('HTML content of the popup (for Classic and HTML types)'),
                    'form_group_class' => 'hkn-field-html',
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Desktop Image'),
                    'name' => 'image_desktop_file',
                    'hint' => $this->l('Recommended: JPG, PNG, GIF or WebP. Max 2MB.'),
                    'form_group_class' => 'hkn-field-image',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Current Desktop Image'),
                    'name' => 'image_desktop_preview',
                    'html_content' => $imageDesktopPreview ? $imageDesktopPreview : '<p class="text-muted">' . $this->l('No image uploaded') . '</p>',
                    'form_group_class' => 'hkn-field-image',
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Mobile Image'),
                    'name' => 'image_mobile_file',
                    'hint' => $this->l('Optional: Different image for mobile devices'),
                    'form_group_class' => 'hkn-field-image',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Current Mobile Image'),
                    'name' => 'image_mobile_preview',
                    'html_content' => $imageMobilePreview ? $imageMobilePreview : '<p class="text-muted">' . $this->l('No image uploaded') . '</p>',
                    'form_group_class' => 'hkn-field-image',
                ),

                // === SCHEDULING SECTION ===
                array(
                    'type' => 'html',
                    'name' => 'section_scheduling',
                    'html_content' => '<h3 class="tab" style="background:#f5f5f5;padding:10px 15px;margin:20px -15px 15px;border-bottom:1px solid #ddd;"><i class="icon-calendar"></i> ' . $this->l('Scheduling') . '</h3>',
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Start Date'),
                    'name' => 'date_start',
                    'hint' => $this->l('Leave empty to start immediately'),
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('End Date'),
                    'name' => 'date_end',
                    'hint' => $this->l('Leave empty for no end date'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hide for X Days'),
                    'name' => 'cookie_days',
                    'class' => 'fixed-width-sm',
                    'suffix' => $this->l('days'),
                    'hint' => $this->l('After closing, hide popup for this many days (0 = always show)'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Once Per Session'),
                    'name' => 'show_once_session',
                    'values' => array(
                        array('id' => 'session_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'session_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                    'hint' => $this->l('Show popup only once per browser session'),
                ),

                // === TRIGGER SECTION ===
                array(
                    'type' => 'html',
                    'name' => 'section_trigger',
                    'html_content' => '<h3 class="tab" style="background:#f5f5f5;padding:10px 15px;margin:20px -15px 15px;border-bottom:1px solid #ddd;"><i class="icon-flash"></i> ' . $this->l('Trigger & Animation') . '</h3>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Trigger Type'),
                    'name' => 'trigger_type',
                    'options' => array(
                        'query' => array(
                            array('id' => 'delay', 'name' => $this->l('Time Delay')),
                            array('id' => 'scroll', 'name' => $this->l('Scroll Percentage')),
                            array('id' => 'exit_intent', 'name' => $this->l('Exit Intent (mouse leaves page)')),
                            array('id' => 'immediate', 'name' => $this->l('Immediately on page load')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Trigger Value'),
                    'name' => 'trigger_value',
                    'class' => 'fixed-width-sm',
                    'hint' => $this->l('Seconds for delay, percentage (0-100) for scroll'),
                    'form_group_class' => 'hkn-field-trigger-value',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Animation'),
                    'name' => 'animation',
                    'options' => array(
                        'query' => array(
                            array('id' => 'fade', 'name' => $this->l('Fade In')),
                            array('id' => 'slide_up', 'name' => $this->l('Slide Up')),
                            array('id' => 'slide_down', 'name' => $this->l('Slide Down')),
                            array('id' => 'slide_left', 'name' => $this->l('Slide Left')),
                            array('id' => 'slide_right', 'name' => $this->l('Slide Right')),
                            array('id' => 'zoom', 'name' => $this->l('Zoom In')),
                            array('id' => 'bounce', 'name' => $this->l('Bounce')),
                            array('id' => 'none', 'name' => $this->l('None')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Auto Close'),
                    'name' => 'auto_close',
                    'class' => 'fixed-width-sm',
                    'suffix' => $this->l('seconds'),
                    'hint' => $this->l('Automatically close after X seconds (0 = disabled)'),
                ),

                // === TARGETING SECTION ===
                array(
                    'type' => 'html',
                    'name' => 'section_targeting',
                    'html_content' => '<h3 class="tab" style="background:#f5f5f5;padding:10px 15px;margin:20px -15px 15px;border-bottom:1px solid #ddd;"><i class="icon-user"></i> ' . $this->l('Targeting') . '</h3>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Target Visitors'),
                    'name' => 'target_visitor',
                    'options' => array(
                        'query' => array(
                            array('id' => 'all', 'name' => $this->l('All Visitors')),
                            array('id' => 'new', 'name' => $this->l('New Visitors Only')),
                            array('id' => 'returning', 'name' => $this->l('Returning Visitors')),
                            array('id' => 'logged_in', 'name' => $this->l('Logged In Customers')),
                            array('id' => 'guest', 'name' => $this->l('Guests Only')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Target Devices'),
                    'name' => 'target_device',
                    'options' => array(
                        'query' => array(
                            array('id' => 'all', 'name' => $this->l('All Devices')),
                            array('id' => 'desktop', 'name' => $this->l('Desktop Only')),
                            array('id' => 'mobile', 'name' => $this->l('Mobile Only')),
                            array('id' => 'tablet', 'name' => $this->l('Tablet Only')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Target Categories'),
                    'name' => 'target_categories[]',
                    'multiple' => true,
                    'options' => array(
                        'query' => $this->getCategories(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Leave empty for all categories'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Target Product IDs'),
                    'name' => 'target_products',
                    'hint' => $this->l('Comma-separated product IDs (e.g., 1,2,3). Leave empty for all.'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Target Customer Groups'),
                    'name' => 'target_customer_groups[]',
                    'multiple' => true,
                    'options' => array(
                        'query' => Group::getGroups($this->context->language->id),
                        'id' => 'id_group',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Leave empty for all groups'),
                ),

                // === DISPLAY OPTIONS SECTION ===
                array(
                    'type' => 'html',
                    'name' => 'section_display',
                    'html_content' => '<h3 class="tab" style="background:#f5f5f5;padding:10px 15px;margin:20px -15px 15px;border-bottom:1px solid #ddd;"><i class="icon-desktop"></i> ' . $this->l('Display Options') . '</h3>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Position'),
                    'name' => 'placement',
                    'options' => array(
                        'query' => array(
                            array('id' => 'middle_center', 'name' => $this->l('Center (default)')),
                            array('id' => 'top_left', 'name' => $this->l('Top Left')),
                            array('id' => 'top_center', 'name' => $this->l('Top Center')),
                            array('id' => 'top_right', 'name' => $this->l('Top Right')),
                            array('id' => 'middle_left', 'name' => $this->l('Middle Left')),
                            array('id' => 'middle_right', 'name' => $this->l('Middle Right')),
                            array('id' => 'bottom_left', 'name' => $this->l('Bottom Left')),
                            array('id' => 'bottom_center', 'name' => $this->l('Bottom Center')),
                            array('id' => 'bottom_right', 'name' => $this->l('Bottom Right')),
                            array('id' => 'fullscreen', 'name' => $this->l('Fullscreen')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Close Button'),
                    'name' => 'show_close_btn',
                    'values' => array(
                        array('id' => 'close_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'close_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Close on Overlay Click'),
                    'name' => 'close_on_overlay',
                    'values' => array(
                        array('id' => 'overlay_on', 'value' => 1, 'label' => $this->l('Yes')),
                        array('id' => 'overlay_off', 'value' => 0, 'label' => $this->l('No')),
                    ),
                    'hint' => $this->l('Close popup when clicking outside the content'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save_and_stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }

    /**
     * Get categories for select
     */
    private function getCategories()
    {
        $categories = Category::getCategories($this->context->language->id, true, false);
        $result = array();

        foreach ($categories as $category) {
            $result[] = array(
                'id' => $category['id_category'],
                'name' => str_repeat('— ', (int) $category['level_depth']) . $category['name'],
            );
        }

        return $result;
    }

    /**
     * Process GET parameters
     */
    public function initProcess()
    {
        parent::initProcess();

        // Handle image deletion
        if (Tools::getValue('delete_image') && Tools::getValue('id_popup')) {
            $this->processDeletePopupImage();
        }
    }

    /**
     * Delete single popup image
     */
    protected function processDeletePopupImage()
    {
        $id = (int) Tools::getValue('id_popup');
        $type = Tools::getValue('delete_image');

        if (!in_array($type, array('desktop', 'mobile'))) {
            return;
        }

        $popup = new HknPopupTableClasses($id);
        if (!Validate::isLoadedObject($popup)) {
            return;
        }

        $field = 'image_' . $type;
        $image = $popup->$field;

        if (!empty($image) && file_exists($this->upload_dir . $image)) {
            @unlink($this->upload_dir . $image);
        }

        $popup->$field = '';
        $popup->save();

        $this->confirmations[] = $this->l('Image deleted successfully');
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPopupTable') . '&id_popup=' . $id . '&update' . $this->table);
    }

    /**
     * Process form before saving
     */
    public function processAdd()
    {
        $this->processTargetFields();
        $this->processImageUpload();
        return parent::processAdd();
    }

    /**
     * Process form before updating
     */
    public function processUpdate()
    {
        $this->processTargetFields();
        $this->processImageUpload();
        return parent::processUpdate();
    }

    /**
     * Handle image upload
     */
    protected function processImageUpload()
    {
        // Desktop image
        if (isset($_FILES['image_desktop_file']) && $_FILES['image_desktop_file']['error'] == 0) {
            $filename = $this->uploadPopupImage($_FILES['image_desktop_file'], 'desktop');
            if ($filename) {
                $_POST['image_desktop'] = $filename;
            }
        }

        // Mobile image
        if (isset($_FILES['image_mobile_file']) && $_FILES['image_mobile_file']['error'] == 0) {
            $filename = $this->uploadPopupImage($_FILES['image_mobile_file'], 'mobile');
            if ($filename) {
                $_POST['image_mobile'] = $filename;
            }
        }
    }

    /**
     * Upload single popup image
     *
     * @param array $file $_FILES array element
     * @param string $type desktop|mobile
     * @return string|false Filename on success, false on failure
     */
    protected function uploadPopupImage($file, $type)
    {
        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $this->errors[] = $this->l('Image file is too large. Maximum size is 2MB.');
            return false;
        }

        // Validate extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowed_extensions)) {
            $this->errors[] = $this->l('Invalid image format. Allowed: JPG, PNG, GIF, WebP.');
            return false;
        }

        // Create upload directory if not exists
        if (!is_dir($this->upload_dir)) {
            @mkdir($this->upload_dir, 0755, true);
        }

        // Generate unique filename
        $filename = 'popup_' . $type . '_' . time() . '_' . Tools::passwdGen(8) . '.' . $ext;
        $destination = $this->upload_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Delete old image if exists (when updating)
            $id = (int) Tools::getValue('id_popup');
            if ($id) {
                $popup = new HknPopupTableClasses($id);
                if (Validate::isLoadedObject($popup)) {
                    $field = 'image_' . $type;
                    $oldImage = $popup->$field;
                    if (!empty($oldImage) && file_exists($this->upload_dir . $oldImage)) {
                        @unlink($this->upload_dir . $oldImage);
                    }
                }
            }

            return $filename;
        }

        $this->errors[] = $this->l('Failed to upload image.');
        return false;
    }

    /**
     * Convert array fields to JSON
     */
    private function processTargetFields()
    {
        // Categories
        $categories = Tools::getValue('target_categories');
        if (is_array($categories) && !empty($categories)) {
            $_POST['target_categories'] = json_encode(array_map('intval', $categories));
        } else {
            $_POST['target_categories'] = null;
        }

        // Products (comma-separated to JSON)
        $products = Tools::getValue('target_products');
        if (!empty($products)) {
            $productIds = array_map('intval', explode(',', $products));
            $_POST['target_products'] = json_encode(array_filter($productIds));
        } else {
            $_POST['target_products'] = null;
        }

        // Customer groups
        $groups = Tools::getValue('target_customer_groups');
        if (is_array($groups) && !empty($groups)) {
            $_POST['target_customer_groups'] = json_encode(array_map('intval', $groups));
        } else {
            $_POST['target_customer_groups'] = null;
        }
    }

    /**
     * Get form values
     */
    public function getFieldsValue($obj)
    {
        $values = parent::getFieldsValue($obj);

        // Decode JSON fields for display
        if (!empty($values['target_categories'])) {
            $values['target_categories[]'] = json_decode($values['target_categories'], true);
        }
        if (!empty($values['target_products'])) {
            $products = json_decode($values['target_products'], true);
            $values['target_products'] = is_array($products) ? implode(',', $products) : '';
        }
        if (!empty($values['target_customer_groups'])) {
            $values['target_customer_groups[]'] = json_decode($values['target_customer_groups'], true);
        }

        // Info message for content tab
        $values['content_type_info'] = '<div class="alert alert-info">' . $this->l('The fields shown below depend on the Popup Type selected in the General tab.') . '</div>';

        return $values;
    }

    /**
     * Delete popup and its images
     */
    public function processDelete()
    {
        $id = (int) Tools::getValue('id_popup');
        if ($id) {
            $popup = new HknPopupTableClasses($id);
            if (Validate::isLoadedObject($popup)) {
                // Delete associated images
                if (!empty($popup->image_desktop) && file_exists($this->upload_dir . $popup->image_desktop)) {
                    @unlink($this->upload_dir . $popup->image_desktop);
                }
                if (!empty($popup->image_mobile) && file_exists($this->upload_dir . $popup->image_mobile)) {
                    @unlink($this->upload_dir . $popup->image_mobile);
                }
            }
        }

        return parent::processDelete();
    }

    /**
     * Duplicate popup action
     */
    public function processDuplicate()
    {
        $id = (int) Tools::getValue('id_popup');
        if (!$id) {
            return false;
        }

        $popup = new HknPopupTableClasses($id);
        if (!Validate::isLoadedObject($popup)) {
            return false;
        }

        $newPopup = $popup->duplicateObject();
        if ($newPopup) {
            $newPopup->title = $popup->title . ' (Copy)';
            $newPopup->active = 0;

            // Copy images with new filenames
            if (!empty($popup->image_desktop) && file_exists($this->upload_dir . $popup->image_desktop)) {
                $ext = pathinfo($popup->image_desktop, PATHINFO_EXTENSION);
                $newFilename = 'popup_desktop_' . time() . '_' . Tools::passwdGen(8) . '.' . $ext;
                if (copy($this->upload_dir . $popup->image_desktop, $this->upload_dir . $newFilename)) {
                    $newPopup->image_desktop = $newFilename;
                }
            }
            if (!empty($popup->image_mobile) && file_exists($this->upload_dir . $popup->image_mobile)) {
                $ext = pathinfo($popup->image_mobile, PATHINFO_EXTENSION);
                $newFilename = 'popup_mobile_' . time() . '_' . Tools::passwdGen(8) . '.' . $ext;
                if (copy($this->upload_dir . $popup->image_mobile, $this->upload_dir . $newFilename)) {
                    $newPopup->image_mobile = $newFilename;
                }
            }

            $newPopup->save();

            $this->confirmations[] = $this->l('Popup duplicated successfully');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminPopupTable'));
        }

        return false;
    }

    // === CALLBACK METHODS FOR LIST ===

    public function displayPopupType($value)
    {
        $types = array(
            'classic' => '<span class="badge badge-info">Classic</span>',
            'images_only' => '<span class="badge badge-primary">Image</span>',
            'html' => '<span class="badge badge-warning">HTML</span>',
            'overlay' => '<span class="badge badge-danger">Overlay</span>',
        );

        return isset($types[$value]) ? $types[$value] : $value;
    }

    public function displayHookName($value)
    {
        $hooks = array(
            'all' => 'All Pages',
            'displayHome' => 'Homepage',
            'displayFooterProduct' => 'Product',
            'displayCategoryFooter' => 'Category',
        );

        return isset($hooks[$value]) ? $hooks[$value] : $value;
    }

    public function displayTriggerType($value)
    {
        $triggers = array(
            'delay' => '<i class="icon-clock-o"></i> Delay',
            'scroll' => '<i class="icon-arrow-down"></i> Scroll',
            'exit_intent' => '<i class="icon-sign-out"></i> Exit',
            'immediate' => '<i class="icon-bolt"></i> Instant',
        );

        return isset($triggers[$value]) ? $triggers[$value] : $value;
    }
}
