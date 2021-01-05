/**
 * JS for the admin account page.
 *
 * @package   Apiopenstudio
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 ApiOpenStudio
 * @link      https://www.apiopenstudio.com
 */

$(document).ready(function () {

    /**
     * Edit account modal.
     */
    $('.modal-acc-edit-trigger').click(function () {
        var modal = $('#modal-acc-edit');
        var accid = $(this).attr('accid');
        var accName = $(this).attr('acc-name');
        modal.find('input[name="name"]').val(accName);
        modal.find('input[name="accid"]').val(accid);
        modal.modal('open');
    });

    /**
     * Delete account modal.
     */
    $('.modal-acc-delete-trigger').click(function () {
        var modal = $('#modal-acc-delete');
        var accid = $(this).attr('accid');
        var name = $(this).attr('acc-name');
        modal.find('input[name="accid"]').val(accid);
        modal.find('.delete-acc-name').html(name);
        modal.modal('open');
    });
});
