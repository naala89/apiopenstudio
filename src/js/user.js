/**
 * JS for the admin user page.
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
     * Delete user modal.
     */
    $('.modal-user-delete-trigger').click(function () {
        var self = $(this);
        var modal = $('#modal-user-delete');
        modal.find('#user-name').html(self.attr('delete-user-username'));
        modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('delete-user-uid'));
        modal.modal('open');
    });
});
