/**
 * JS for the admin user page.
 *
 * @package Gaterdata
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
