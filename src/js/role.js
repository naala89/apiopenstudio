/**
 * JS for the admin role page.
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
    $('.modal-role-create-trigger').click(function () {
        var modal = $('#modal-role-create');
        modal.modal('open');
    });

    $('.modal-role-edit-trigger').click(function () {
        var modal = $('#modal-role-update'),
            self = $(this);
        modal.find('input[name="rid"]').val(self.attr('rid'));
        modal.find('input[name="name"]').val(self.attr('role-name'));
        M.updateTextFields();
        modal.modal('open');
    });

    $('.modal-role-delete-trigger').click(function () {
        var modal = $('#modal-role-delete'),
            self = $(this);
        modal.find('input[name="rid"]').val(self.attr('rid'));
        modal.find('.role-name').html(self.attr('role-name'));
        modal.modal('open');
    });
});
