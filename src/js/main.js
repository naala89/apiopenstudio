/**
 * Generic JS for the admin pages.
 *
 * @package Gaterdata
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
