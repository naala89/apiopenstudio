$(document).ready(function () {

    /**
     * User role create - role select.
     */
    $("#modal-user-role-create select[name='rid']").on('change', function () {
        var selected = $(this).find('option:selected').text();
        var modal = $('#modal-user-role-create');
        var selectAccid = modal.find("select[name='accid']");
        var selectAppid = modal.find("select[name='appid']");
        if (selected == 'Administrator') {
            selectAccid.val('').prop('disabled', true);
            selectAccid.formSelect();
            selectAppid.val('').prop('disabled', true);
            selectAppid.formSelect();
        } else if (selected == 'Account manager') {
            selectAccid.prop('disabled', false);
            selectAccid.formSelect();
            selectAppid.val('').prop('disabled', true);
            selectAppid.formSelect();
        } else {
            selectAccid.prop('disabled', false);
            selectAccid.formSelect();
            selectAppid.prop('disabled', false);
            selectAppid.formSelect();
        }
    });

    /**
     * User role create - account select.
     */
    $("#modal-user-role-create select[name='accid']").on('change', function () {
        GATERDATA.setApplicationOptions($(this).val(), '#modal-user-role-create select[name="appid"]');
    });

    /**
     * User role delete.
     */
    $(".modal-user-role-delete-trigger").on('click', function () {
        var urid = $(this).attr('urid'),
            user = $(this).attr('user'),
            account = $(this).attr('acc'),
            application = $(this).attr('app'),
            role = $(this).attr('role'),
            modal = $('#modal-user-role-delete');
        modal.find("input[name='urid']").val(urid);
        modal.find('.user').html(user);
        modal.find('.acc').html(account);
        modal.find('.app').html(application);
        modal.find('.role').html(role);
        modal.modal('open');
    });
});
