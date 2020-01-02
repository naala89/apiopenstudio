/**
 * JS for Vars page.
 */
$(document).ready(function () {
    $('#modal-var-create select[name="create-var-accid"]').on('change', function() {
        GATERDATA.setApplicationOptions($(this).val(), '#modal-var-create select[name="create-var-appid"]');
    });

    $('#modal-var-create select[name="create-var-appid"]').on('change', function() {
        GATERDATA.setAccount($(this).val(), '#modal-var-create select[name="create-var-accid"]');
    });

    $('.modal-var-edit-trigger').on('click', function() {
        var self = $(this),
            modal = $('#modal-var-edit');
        modal.find('input[name="edit-var-vid"]').val(self.attr('vid'));
        modal.find('.edit-var-key').html(self.attr('key'));
        modal.find('input[name="edit-var-val"]').val(self.attr('val'));
        M.updateTextFields();
    });

    $('.modal-var-delete-trigger').on('click', function() {
        var self = $(this),
            modal = $('#modal-var-delete');
        modal.find('input[name="delete-var-vid"]').val(self.attr('vid'));
        modal.find('.delete-var-key').html(self.attr('key'));
    });
});
