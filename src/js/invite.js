/**
 * JS for the admin invite page.
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
     * Delete invite modal.
     */
    $('.modal-invite-delete-trigger').click(function () {
        var self = $(this);
        var modal = $('#modal-invite-delete');
        modal.find('#user-email').html(self.attr('invite-email'));
        modal.find('a#delete-invite').attr('href', '/invite/delete/' + self.attr('invite-iid'));
        modal.modal('open');
    });
});
