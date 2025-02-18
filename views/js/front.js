/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
// function setCookie(name, value, days) {
//     const expirationDate = new Date();
//     expirationDate.setDate(expirationDate.getDate() + days);
//     const cookieValue = encodeURIComponent(value) + "; expires=" + expirationDate.toUTCString();
//     document.cookie = name + "=" + cookieValue + "; path=/";
// }
//
// // Function to get the value of a cookie by name
// function getCookie(name) {
//     const cookies = document.cookie.split(';');
//     for (let i = 0; i < cookies.length; i++) {
//         const cookie = cookies[i].trim();
//         if (cookie.startsWith(name + '=')) {
//             return decodeURIComponent(cookie.substring(name.length + 1));
//         }
//     }
//     return null;
// }

$(document).ready(function () {
    var thpopupElements = $('.th_popup');

    const popupHidden = sessionStorage.getItem('thpopup_hidden');
    const allPageCookie = sessionStorage.getItem('th_allpage_cookie');

    if (thpopupElements.length > 1) {
        thpopupElements.each(function () {
            // Get the value of the "data-template" attribute for each element
            var dataTemplateValue = $(this).attr('data-template');

            $.ajax({
                data: 'post',
                url: thpopup_url, // Replace with your API endpoint URL
                dataType: 'JSON',
                data: {
                    action: 'getDelay',
                    id_template: dataTemplateValue,
                },
                success: function (data) {
                    // Remove the 'hidden' class after 4 seconds
                    setTimeout(function () {
                        if(!popupHidden){
                            $(this).removeClass('hidden');
                        }
                    }, data.delay); // 4 seconds = 4000 milliseconds
                },
                error: function () {
                    // Handle errors here if the AJAX request fails
                    console.error('Error occurred during the AJAX request');
                }
            });
        });
    } else if (thpopupElements.length === 1) {
        // If there is only one element, you can directly access its data-template attribute
        var dataTemplateValue = thpopupElements.attr('data-template');
        $.ajax({
            data: 'post',
            url: thpopup_url, // Replace with your API endpoint URL
            dataType: 'JSON',
            data: {
                action: 'getDelay',
                id_template: dataTemplateValue,
            },
            success: function (data) {
                // Remove the 'hidden' class after 4 seconds
                setTimeout(function () {
                    if(!popupHidden || typeof popupHidden == null) {
                        thpopupElements.show();
                        $('.th_popup_shadow').show();
                    }
                }, data.delay); // 4 seconds = 4000 milliseconds
            },
            error: function () {
                // Handle errors here if the AJAX request fails
                console.error('Error occurred during the AJAX request');
            }
        });
    }

    $('.th_allpage').on('click', function () {
        $('.th_delivery_hidden').removeClass('hidden');
        $('.th_delivery_hidden').css({
            "color": "red",
            "text-shadow": "1px 1px 1px rgba(255, 0, 0, 0.50)"
        });
        setTimeout(function() {
            $(".th_delivery_hidden").css({
                "color": "white",
                "text-shadow": "none"
            });
        }, 500);
    });
    $('.th_button_ok').click(function(){
        $('.th_allpage').hide(); // You can use .remove() if you want to remove the element instead of hiding it.
        sessionStorage.setItem('th_allpage_cookie', 'true');
    });

    if(!allPageCookie){
        setTimeout(function () {
            $('.th_allpage').removeClass('hidden');
        }, 500);
    }

    // Handle the click event for the close button
    $('.thpopup_close').on('click', function () {
        // Add the 'hidden' class to hide the popup
        $('.th_popup').hide();
        $('.th_popup_shadow').hide();
        // Set the 'thpopup_hidden' cookie to 'true' with a 30-day expiration
        sessionStorage.setItem('thpopup_hidden', 'true');

    });

});
