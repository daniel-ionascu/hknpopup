/**
 * HKN Popup Manager - Frontend JavaScript
 * Version 3.0.0
 *
 * @author    Daniel Ionascu <danielionascudev@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(function () {
    'use strict';

    /**
     * HknPopupManager - Handles all popup display logic
     */
    var HknPopupManager = {
        popups: [],
        exitIntentTriggered: false,

        /**
         * Initialize popup manager
         */
        init: function () {
            var self = this;
            document.querySelectorAll('.hkn-popup').forEach(function (element) {
                var popup = self.parsePopupData(element);
                if (popup && self.shouldShow(popup)) {
                    self.popups.push(popup);
                    self.setupTrigger(popup);
                }
            });

            // Setup global exit intent listener if needed
            if (this.hasExitIntentPopups()) {
                this.setupExitIntent();
            }
        },

        /**
         * Parse popup data from DOM element
         * @param {HTMLElement} element
         * @returns {Object|null}
         */
        parsePopupData: function (element) {
            try {
                return {
                    id: parseInt(element.dataset.popupId, 10),
                    element: element,
                    overlay: element.querySelector('.hkn-popup-overlay'),
                    container: element.querySelector('.hkn-popup-container'),
                    triggerType: element.dataset.triggerType || 'delay',
                    triggerValue: parseInt(element.dataset.triggerValue, 10) || 2,
                    animation: element.dataset.animation || 'fade',
                    showCloseBtn: element.dataset.showCloseBtn === '1',
                    closeOnOverlay: element.dataset.closeOnOverlay === '1',
                    autoClose: parseInt(element.dataset.autoClose, 10) || 0,
                    cookieDays: parseInt(element.dataset.cookieDays, 10) || 0,
                    showOnceSession: element.dataset.showOnceSession === '1',
                    url: element.dataset.url || null,
                    shown: false
                };
            } catch (e) {
                console.error('HknPopup: Error parsing popup data', e);
                return null;
            }
        },

        /**
         * Check if popup should be displayed
         * @param {Object} popup
         * @returns {boolean}
         */
        shouldShow: function (popup) {
            // Check cookie
            if (popup.cookieDays > 0 && this.getCookie('hknpopup_' + popup.id)) {
                return false;
            }

            // Check session storage
            if (popup.showOnceSession && sessionStorage.getItem('hknpopup_' + popup.id)) {
                return false;
            }

            return true;
        },

        /**
         * Check if any popup uses exit intent trigger
         * @returns {boolean}
         */
        hasExitIntentPopups: function () {
            return this.popups.some(function (popup) {
                return popup.triggerType === 'exit_intent';
            });
        },

        /**
         * Setup trigger for a popup
         * @param {Object} popup
         */
        setupTrigger: function (popup) {
            var self = this;

            switch (popup.triggerType) {
                case 'immediate':
                    this.showPopup(popup);
                    break;

                case 'delay':
                    setTimeout(function () {
                        self.showPopup(popup);
                    }, popup.triggerValue * 1000);
                    break;

                case 'scroll':
                    this.setupScrollTrigger(popup);
                    break;

                case 'exit_intent':
                    // Handled globally in setupExitIntent
                    break;

                case 'click':
                    this.setupClickTrigger(popup);
                    break;

                default:
                    // Default to delay of 2 seconds
                    setTimeout(function () {
                        self.showPopup(popup);
                    }, 2000);
            }
        },

        /**
         * Setup scroll-based trigger
         * @param {Object} popup
         */
        setupScrollTrigger: function (popup) {
            var self = this;
            var triggered = false;

            var scrollHandler = function () {
                if (triggered) return;

                var scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;

                if (scrollPercent >= popup.triggerValue) {
                    triggered = true;
                    window.removeEventListener('scroll', scrollHandler);
                    self.showPopup(popup);
                }
            };

            window.addEventListener('scroll', scrollHandler, { passive: true });
        },

        /**
         * Setup exit intent detection
         */
        setupExitIntent: function () {
            var self = this;

            var exitHandler = function (e) {
                if (self.exitIntentTriggered) return;

                // Only trigger when mouse leaves through top of viewport
                if (e.clientY <= 0) {
                    self.exitIntentTriggered = true;
                    document.removeEventListener('mouseout', exitHandler);

                    // Show all exit intent popups
                    self.popups.forEach(function (popup) {
                        if (popup.triggerType === 'exit_intent' && !popup.shown) {
                            self.showPopup(popup);
                        }
                    });
                }
            };

            document.addEventListener('mouseout', exitHandler);
        },

        /**
         * Setup click trigger (for specific elements)
         * @param {Object} popup
         */
        setupClickTrigger: function (popup) {
            var self = this;
            var triggerSelector = popup.element.dataset.clickSelector;

            if (triggerSelector) {
                document.querySelectorAll(triggerSelector).forEach(function (trigger) {
                    trigger.addEventListener('click', function (e) {
                        e.preventDefault();
                        self.showPopup(popup);
                    });
                });
            }
        },

        /**
         * Show a popup with animation
         * @param {Object} popup
         */
        showPopup: function (popup) {
            if (popup.shown) return;

            var self = this;
            popup.shown = true;

            // Add animation class
            popup.element.classList.add('hkn-animation-' + popup.animation);

            // Show popup
            popup.element.classList.add('hkn-popup-visible');

            // Prevent body scroll
            document.body.classList.add('hkn-popup-open');

            // Setup close handlers
            this.setupCloseHandlers(popup);

            // Setup auto-close if configured
            if (popup.autoClose > 0) {
                setTimeout(function () {
                    self.closePopup(popup);
                }, popup.autoClose * 1000);
            }

            // Setup URL click handler
            if (popup.url) {
                var contentArea = popup.container.querySelector('.hkn-popup-content');
                if (contentArea) {
                    contentArea.style.cursor = 'pointer';
                    contentArea.addEventListener('click', function (e) {
                        if (!e.target.closest('.hkn-popup-close')) {
                            window.location.href = popup.url;
                        }
                    });
                }
            }

            // Mark as shown via AJAX (for server-side tracking)
            this.markShown(popup);
        },

        /**
         * Setup close handlers for popup
         * @param {Object} popup
         */
        setupCloseHandlers: function (popup) {
            var self = this;

            // Close button
            if (popup.showCloseBtn) {
                var closeBtn = popup.element.querySelector('.hkn-popup-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        self.closePopup(popup);
                    });
                }
            }

            // Close on overlay click
            if (popup.closeOnOverlay && popup.overlay) {
                popup.overlay.addEventListener('click', function (e) {
                    if (e.target === popup.overlay) {
                        self.closePopup(popup);
                    }
                });
            }

            // Close on Escape key
            document.addEventListener('keydown', function escapeHandler(e) {
                if (e.key === 'Escape' && popup.element.classList.contains('hkn-popup-visible')) {
                    self.closePopup(popup);
                    document.removeEventListener('keydown', escapeHandler);
                }
            });
        },

        /**
         * Close a popup with animation
         * @param {Object} popup
         */
        closePopup: function (popup) {
            var self = this;

            // Add closing animation
            popup.element.classList.add('hkn-popup-closing');

            // Remove popup after animation
            setTimeout(function () {
                popup.element.classList.remove('hkn-popup-visible', 'hkn-popup-closing');
                document.body.classList.remove('hkn-popup-open');

                // Set cookie if configured
                if (popup.cookieDays > 0) {
                    self.setCookie('hknpopup_' + popup.id, '1', popup.cookieDays);
                }

                // Set session storage if configured
                if (popup.showOnceSession) {
                    sessionStorage.setItem('hknpopup_' + popup.id, '1');
                }
            }, 300);
        },

        /**
         * Mark popup as shown via AJAX
         * @param {Object} popup
         */
        markShown: function (popup) {
            if (typeof hknpopup_ajax_url === 'undefined') return;

            var formData = new FormData();
            formData.append('action', 'markShown');
            formData.append('id_popup', popup.id);
            formData.append('cookie_days', popup.cookieDays);
            formData.append('show_once_session', popup.showOnceSession ? 1 : 0);

            fetch(hknpopup_ajax_url, {
                method: 'POST',
                body: formData
            }).catch(function (error) {
                console.error('HknPopup: Error marking popup as shown', error);
            });
        },

        /**
         * Set cookie
         * @param {string} name
         * @param {string} value
         * @param {number} days
         */
        setCookie: function (name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
        },

        /**
         * Get cookie value
         * @param {string} name
         * @returns {string|null}
         */
        getCookie: function (name) {
            var nameEQ = name + '=';
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim();
                if (cookie.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(cookie.substring(nameEQ.length));
                }
            }
            return null;
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            HknPopupManager.init();
        });
    } else {
        HknPopupManager.init();
    }

    // Expose for external access
    window.HknPopupManager = HknPopupManager;

})();
