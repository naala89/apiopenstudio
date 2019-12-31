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
