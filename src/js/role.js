/**
 * JS for the admin role page.
 *
 * @package Gaterdata
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
