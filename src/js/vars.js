/**
 * JS for Vars page.
 */
$(document).ready(function () {
    $('#modal-var-create select[name="accid"]').on('change', function() {
        GATERDATA.setApplicationOptions($(this).val(), '#modal-var-create select[name="appid"]');
    });

    $('#modal-var-create select[name="appid"]').on('change', function() {
        GATERDATA.setAccount($(this).val(), '#modal-var-create select[name="accid"]');
    });
});
