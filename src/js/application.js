/**
 * JS for the admin application page.
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
     * Edit application modal.
     */
    $('.modal-app-edit-trigger').click(function () {
        var modal = $('#modal-app-edit');
        var accid = $(this).attr('accid');
        var appid = $(this).attr('appid');
        var name = $(this).attr('name');
        var select_accid = modal.find('select[name="edit-app-accid"]');

        select_accid.find('option[value="' + accid + '"]').prop('selected', true);
        select_accid.formSelect();
        modal.find('input[name="edit-app-appid"]').val(appid);
        modal.find('input[name="edit-app-name"]').val(name);
        modal.modal('open');
    });

    /**
     * Delete application modal.
     */
    $('.modal-app-delete-trigger').click(function () {
        var modal = $('#modal-app-delete');
        var appid = $(this).attr('appid');
        var name = $(this).attr('name');
        modal.find('input[name="delete-app-appid"]').val(appid);
        modal.find('#delete-app-name').html(name);
        modal.modal('open');
    });
});
