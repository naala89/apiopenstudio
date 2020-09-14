/**
 * Generic JS for the admin pages.
 *
 * @package Gaterdata
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @link https://gaterdata.com
 */

$(document).ready(function () {

    M.AutoInit();

    /**
     * Reset options in applications to all.
     * @param string selector
     *   Applications element selector.
     */
    GATERDATA.resetApplications = function (selector) {
        var selectApp = $(selector);
        selectApp.find('option').remove();
        selectApp.append($('<option>', {value: "", text: "Please select"}));
        GATERDATA.accAppMap.forEach(function (application, appid) {
            $(selector).append($('<option>', {value: appid, text: application.name}));
        });
        selectApp.val("");
        selectApp.formSelect();
    };

    /**
     * Update an account selector based on application ID
     * @param integer appid
     *   Application ID.
     * @param string selector
     *   JQuery selector for the account select element.
     */
    GATERDATA.setAccount = function (appid, selector) {
        var selectAcc = $(selector);
        if (typeof GATERDATA.accAppMap[appid] == 'undefined') {
            selectAcc.val('');
        } else {
            selectAcc.val(GATERDATA.accAppMap[appid].accid);
        }
        selectAcc.formSelect();
    };

    /**
     * Update an application selector based on account ID
     *
     * @param integer accid
     *   Account ID
     * @param string selector
     *   JQuery selector for the application select element.
     */
    GATERDATA.setApplicationOptions = function (accid, selector) {
        var selectApp = $(selector);
        selectApp.find('option').remove();
        selectApp.append($('<option>', {value: "", text: "Please select"}));
        GATERDATA.accAppMap.forEach(function (application, appid) {
            if (accid == application.accid) {
                selectApp.append($('<option>', {value: appid, text: application.name}));
            }
        });
        selectApp.val("");
        selectApp.formSelect();
    };

    /**
     * Close alert panel.
     */
    $('.close-gaterdata-alert').click(function () {
        $(this).closest('.gaterdata-alert').fadeOut("slow", function () {});
    });
});
