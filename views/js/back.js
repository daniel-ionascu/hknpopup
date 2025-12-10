/**
 * HKN Popup Manager - Back Office JavaScript
 * Version 3.0.0
 *
 * @author    Daniel Ionascu <danielionascudev@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize popup type visibility
        initPopupTypeToggle();

        // Initialize trigger type visibility
        initTriggerTypeToggle();

        // Initialize date pickers enhancement
        initDatePickers();

        // Initialize file input preview
        initFilePreview();
    });

    /**
     * Toggle content/image fields based on popup type
     */
    function initPopupTypeToggle() {
        var popupTypeSelect = document.querySelector('select[name="popup_type"]');
        if (!popupTypeSelect) return;

        function updateFieldsVisibility() {
            var popupType = popupTypeSelect.value;

            // Get all HTML content fields (they have class hkn-field-html)
            var htmlFields = document.querySelectorAll('.hkn-field-html');
            // Get all image fields (they have class hkn-field-image)
            var imageFields = document.querySelectorAll('.hkn-field-image');

            // Also try to find by form group containing the field names
            var htmlContentRow = findFormGroupByFieldName('popup_content');
            var imageDesktopRow = findFormGroupByFieldName('image_desktop_file');
            var imageMobileRow = findFormGroupByFieldName('image_mobile_file');
            var imageDesktopPreview = findFormGroupByFieldName('image_desktop_preview');
            var imageMobilePreview = findFormGroupByFieldName('image_mobile_preview');

            // Combine found elements
            var htmlElements = [];
            var imageElements = [];

            htmlFields.forEach(function(el) { htmlElements.push(el); });
            if (htmlContentRow) htmlElements.push(htmlContentRow);

            imageFields.forEach(function(el) { imageElements.push(el); });
            if (imageDesktopRow) imageElements.push(imageDesktopRow);
            if (imageMobileRow) imageElements.push(imageMobileRow);
            if (imageDesktopPreview) imageElements.push(imageDesktopPreview);
            if (imageMobilePreview) imageElements.push(imageMobilePreview);

            // Toggle visibility based on popup type
            switch (popupType) {
                case 'images_only':
                    // Hide HTML, show images
                    htmlElements.forEach(function(el) { if (el) el.style.display = 'none'; });
                    imageElements.forEach(function(el) { if (el) el.style.display = ''; });
                    break;

                case 'html':
                    // Show HTML, hide images
                    htmlElements.forEach(function(el) { if (el) el.style.display = ''; });
                    imageElements.forEach(function(el) { if (el) el.style.display = 'none'; });
                    break;

                case 'overlay':
                    // Hide both
                    htmlElements.forEach(function(el) { if (el) el.style.display = 'none'; });
                    imageElements.forEach(function(el) { if (el) el.style.display = 'none'; });
                    break;

                default: // classic
                    // Show both
                    htmlElements.forEach(function(el) { if (el) el.style.display = ''; });
                    imageElements.forEach(function(el) { if (el) el.style.display = ''; });
            }
        }

        popupTypeSelect.addEventListener('change', updateFieldsVisibility);
        // Run on page load
        updateFieldsVisibility();
    }

    /**
     * Toggle trigger value field based on trigger type
     */
    function initTriggerTypeToggle() {
        var triggerTypeSelect = document.querySelector('select[name="trigger_type"]');
        if (!triggerTypeSelect) return;

        var triggerValueRow = findFormGroupByFieldName('trigger_value');
        var triggerValueInput = document.querySelector('input[name="trigger_value"]');
        var triggerValueLabel = triggerValueRow ? triggerValueRow.querySelector('label') : null;

        function updateTriggerValueVisibility() {
            var triggerType = triggerTypeSelect.value;

            if (!triggerValueRow) return;

            // Show trigger value for delay and scroll types only
            if (triggerType === 'delay' || triggerType === 'scroll') {
                triggerValueRow.style.display = '';

                // Update label and hint based on type
                if (triggerValueLabel) {
                    if (triggerType === 'delay') {
                        triggerValueLabel.innerHTML = 'Delay <small class="text-muted">(seconds)</small>';
                        if (triggerValueInput) {
                            triggerValueInput.placeholder = 'e.g., 3';
                        }
                    } else if (triggerType === 'scroll') {
                        triggerValueLabel.innerHTML = 'Scroll percentage <small class="text-muted">(0-100)</small>';
                        if (triggerValueInput) {
                            triggerValueInput.placeholder = 'e.g., 50';
                        }
                    }
                }
            } else {
                // Hide for exit_intent and immediate
                triggerValueRow.style.display = 'none';
            }
        }

        triggerTypeSelect.addEventListener('change', updateTriggerValueVisibility);
        // Run on page load
        updateTriggerValueVisibility();
    }

    /**
     * Find form group row by field name
     */
    function findFormGroupByFieldName(fieldName) {
        // Try to find input/select/textarea with that name
        var field = document.querySelector('[name="' + fieldName + '"]') ||
                    document.querySelector('[name="' + fieldName + '[]"]') ||
                    document.querySelector('[name^="' + fieldName + '"]');

        if (field) {
            // Walk up to find .form-group
            var parent = field.parentElement;
            while (parent) {
                if (parent.classList && parent.classList.contains('form-group')) {
                    return parent;
                }
                parent = parent.parentElement;
            }
        }

        return null;
    }

    /**
     * Enhance date picker fields with clear buttons
     */
    function initDatePickers() {
        var dateInputs = document.querySelectorAll('input[name="date_start"], input[name="date_end"]');

        dateInputs.forEach(function (input) {
            // Check if clear button already exists
            if (input.nextElementSibling && input.nextElementSibling.classList.contains('hkn-clear-date')) {
                return;
            }

            // Create clear button
            var clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'btn btn-default btn-xs hkn-clear-date';
            clearBtn.innerHTML = '<i class="icon-times"></i> Clear';
            clearBtn.style.marginLeft = '5px';
            clearBtn.style.verticalAlign = 'middle';

            clearBtn.addEventListener('click', function () {
                input.value = '';
            });

            input.parentNode.insertBefore(clearBtn, input.nextSibling);
        });
    }

    /**
     * Initialize file input preview
     */
    function initFilePreview() {
        var fileInputs = document.querySelectorAll('input[type="file"][name$="_file"]');

        fileInputs.forEach(function (input) {
            input.addEventListener('change', function (e) {
                var file = e.target.files[0];
                if (!file) return;

                // Validate file type
                var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload JPG, PNG, GIF or WebP images.');
                    e.target.value = '';
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File is too large. Maximum size is 2MB.');
                    e.target.value = '';
                    return;
                }

                // Show preview
                var reader = new FileReader();
                reader.onload = function (readerEvent) {
                    var previewId = input.name.replace('_file', '_new_preview');
                    var existingPreview = document.getElementById(previewId);

                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    var preview = document.createElement('div');
                    preview.id = previewId;
                    preview.style.marginTop = '10px';
                    preview.innerHTML = '<strong>New image:</strong><br><img src="' + readerEvent.target.result + '" style="max-width:200px;max-height:150px;border:1px solid #5cb85c;border-radius:4px;margin-top:5px;" />';

                    input.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            });
        });
    }

})();
