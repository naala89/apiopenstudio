/**
 * JS for the admin invite page.
 *
 * @package Gaterdata
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
