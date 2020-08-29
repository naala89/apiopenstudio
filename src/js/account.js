/**
 * JS for the admin account page.
 *
 * @package Gaterdata
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
