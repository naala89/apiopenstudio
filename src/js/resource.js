/**
 * JS for the admin resource page.
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
                APIOPENSTUDIO.doc = jsyaml.safeLoad(this.result);
            } catch (e) {
                M.toast({html: e});
            }

            ['name', 'description', 'uri', 'ttl'].forEach(function (item) {
                if (typeof APIOPENSTUDIO.doc[item] != 'undefined') {
                    $('#' + item).val(APIOPENSTUDIO.doc[item]);
                } else {
                    $('#' + item).val('');
                }
            });

            APIOPENSTUDIO.resetApplications('#appid');
            ['appid', 'method'].forEach(function (item) {
                if (typeof APIOPENSTUDIO.doc[item] != 'undefined') {
                    $('#' + item).val(APIOPENSTUDIO.doc[item]);
                } else {
                    $('#' + item).val('');
                }
                $('#' + item).formSelect();
            });

            APIOPENSTUDIO.setAccount($('#appid').val(), '#accid');
            $('#accid').formSelect();

            $('ul.tabs').tabs('select', 'yaml');
            ['security', 'process', 'output'].forEach(function (item) {
                if (typeof APIOPENSTUDIO.doc[item] != 'undefined') {
                    $("textarea[name='" + item + "']").val(jsyaml.dump(APIOPENSTUDIO.doc[item]));
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
     * Resource create - account select.
     */
    $("#create-resource select[name='accid']").on('change', function () {
        APIOPENSTUDIO.setApplicationOptions($(this).val(), '#appid')
    });

    /**
     * Resource create - application select.
     */
    $("#create-resource select[name='appid']").on('change', function () {
        APIOPENSTUDIO.setAccount($(this).val(), '#accid')
    });

    /**
     * Resource create - YAML view.
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
     * Resource create - JSON view.
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
    $('button.resource-download-file').on('click', function () {
        var iframe = $("<iframe/>").attr({
            src: $(this).attr('url'),
            style: "visibility:hidden; display:none"
        });
        $(this).append(iframe);
    });

    $('input[name="resource_file"]').change(function () {
        $(this).closest('form').submit();
    });
});
