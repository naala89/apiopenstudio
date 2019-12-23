$(document).ready(function () {

    M.AutoInit();

    /**
     * Reset options in applications to all.
     * @param string selector
     *   Applications element selector.
     */
    GATERDATA.resetApplications = function (selector) {
        var selectApp = $(selector);
        selectApp.find('option').remove();
        selectApp.append($('<option>', {value: "", text: "Please select"}));
        GATERDATA.accAppMap.forEach(function (application, appid) {
            $(selector).append($('<option>', {value: appid, text: application.name}));
        });
        selectApp.val("");
        selectApp.formSelect();
    };

    /**
     * Update an account selector based on application ID
     * @param integer appid
     *   Application ID.
     * @param string selector
     *   JQuery selector for the account select element.
     */
    GATERDATA.setAccount = function (appid, selector) {
        var selectAcc = $(selector);
        selectAcc.val(GATERDATA.accAppMap[appid].accid);
        selectAcc.formSelect();
    };

    /**
     * Update an application selector based on account ID
     * @param integer accid
     *   Account ID
     * @param string selector
     *   JQuery selector for the application select element.
     */
    GATERDATA.setApplicationOptions = function (accid, selector) {
        var selectApp = $(selector);
        selectApp.find('option').remove();
        selectApp.append($('<option>', {value: "", text: "Please select"}));
        GATERDATA.accAppMap.forEach(function (application, appid) {
            if (accid == application.accid) {
                selectApp.append($('<option>', {value: appid, text: application.name}));
            }
        });
        selectApp.val("");
        selectApp.formSelect();
    };

    /**
     * Close alert panel.
     */
    $('.close-gaterdata-alert').click(function () {
        $(this).closest('.gaterdata-alert').fadeOut("slow", function () {
        });
    });

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

    /**
     * Upload a resource file into the editor.
     */
    $("#upload-resource-file").on('change', function () {
        var inputFiles = this.files,
            inputFile = inputFiles[0],
            reader = new FileReader();
        if (inputFiles == undefined || inputFiles.length == 0) {
            return;
        }

        reader.onload = function (event) {
            try {
                GATERDATA.doc = jsyaml.safeLoad(this.result);
            } catch (e) {
                M.toast({html: e});
            }

            ['name', 'description', 'uri', 'ttl'].forEach(function (item) {
                if (typeof GATERDATA.doc[item] != 'undefined') {
                    $('#' + item).val(GATERDATA.doc[item]);
                } else {
                    $('#' + item).val('');
                }
            });

            GATERDATA.resetApplications('#appid');
            ['appid', 'method'].forEach(function (item) {
                if (typeof GATERDATA.doc[item] != 'undefined') {
                    $('#' + item).val(GATERDATA.doc[item]);
                } else {
                    $('#' + item).val('');
                }
                $('#' + item).formSelect();
            });

            GATERDATA.setAccount($('#appid').val(), '#accid');
            $('#accid').formSelect();

            $('ul.tabs').tabs('select', 'yaml');
            ['security', 'process', 'output'].forEach(function (item) {
                if (typeof GATERDATA.doc[item] != 'undefined') {
                    $("textarea[name='" + item + "']").val(jsyaml.dump(GATERDATA.doc[item]));
                } else {
                    $("textarea[name='" + item + "']").val('');
                }
                $("#json textarea[name='" + item + "']").val('');
                M.textareaAutoResize($("textarea[name='" + item + "']"));
                M.textareaAutoResize($("textarea[name='" + item + "']"));
            });
        };

        reader.readAsText(inputFile);
    });

    /**
     * resource create - account select.
     */
    $("#create-resource select[name='accid']").on('change', function () {
        GATERDATA.setApplicationOptions($(this).val(), '#appid')
    });

    /**
     * resource create - application select.
     */
    $("#create-resource select[name='appid']").on('change', function () {
        GATERDATA.setAccount($(this).val(), '#accid')
    });

    /**
     * resource create - YAML view.
     */
    $("#create-resource a[href='#yaml']").on('click', function () {
        if (!$(this).hasClass('active')) {
            try {
                ['security', 'process', 'output'].forEach(function (item) {
                    var obj = jsyaml.safeLoad($('textarea[name="' + item + '"]').val());
                    if (typeof obj != 'undefined') {
                        $('textarea[name="' + item + '"]').val(jsyaml.dump(obj));
                        $('input[name="format"]').val('yaml');
                    } else {
                        $('textarea[name="' + item + '"]').val('');
                    }
                    M.textareaAutoResize($('textarea[name="' + item + '"]'));
                });
            } catch (e) {
                M.toast({html: e});
                return;
            }
        }
    });

    /**
     * resource create - JSON view.
     */
    $("#create-resource a[href='#json']").on('click', function () {
        if (!$(this).hasClass('active')) {
            try {
                ['security', 'process', 'output'].forEach(function (item) {
                    var obj = jsyaml.safeLoad($('textarea[name="' + item + '"]').val());
                    if (typeof obj != 'undefined') {
                        $('textarea[name="' + item + '"]').val(JSON.stringify(obj, null, 2));
                        $('input[name="format"]').val('json');
                    } else {
                        $('textarea[name="' + item + '"]').val('');
                    }
                    M.textareaAutoResize($('textarea[name="' + item + '"]'));
                });
            } catch (e) {
                M.toast({html: e});
                return;
            }
        }
    });

    /**
     * Delete a resource.
     */
    $('.modal-resource-delete-trigger').click(function () {
        var self = $(this);
        var modal = $('#modal-resource-delete');
        modal.find('.name').html(self.attr('res-name'));
        modal.find('input[name="resid"]').val(self.attr('resid'));
        modal.modal('open');
    });

    /**
     * Download a resource modal preparation.
     */
    $('.modal-resource-download-trigger').click(function () {
        var self = $(this),
            res_name = self.attr('res-name'),
            resid = self.attr('resid'),
            modal = $('#modal-resource-download');
        modal.find('#resource-name').html(res_name);
        modal.find('button.resource-download-file.yaml').attr('url', '/resource/download/yaml/' + resid);
        modal.find('button.resource-download-file.json').attr('url', '/resource/download/json/' + resid);
        modal.modal('open');
    });

    /**
     * Download a resource.
     */
    $('button.resource-download-file').on('click', function() {
        var iframe = $("<iframe/>").attr({
            src: $(this).attr('url'),
            style: "visibility:hidden; display:none"
        });
        $(this).append(iframe);
    });

    $('input[name="resource_file"]').change(function() {
        $(this).closest('form').submit();
    });
});
