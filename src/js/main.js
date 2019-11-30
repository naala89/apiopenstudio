$(document).ready(function() {

  M.AutoInit();

  // Close alert panel.
  $('.close-gaterdata-alert').click(function(){
    $(this).closest('.gaterdata-alert').fadeOut("slow", function() {});
  });

  // Edit account modal.
  $('.modal-acc-edit-trigger').click(function() {
    var modal = $('#modal-acc-edit');
    var accid = $(this).attr('accid');
    var accName = $(this).attr('acc-name');
    modal.find('input[name="name"]').val(accName);
    modal.find('input[name="accid"]').val(accid);
    modal.modal('open');
  });

  // Delete account modal.
  $('.modal-acc-delete-trigger').click(function() {
    var modal = $('#modal-acc-delete');
    var accid = $(this).attr('accid');
    var name = $(this).attr('acc-name');
    modal.find('input[name="accid"]').val(accid);
    modal.find('.delete-acc-name').html(name);
    modal.modal('open');
  });

  // Edit application modal.
  $('.modal-app-edit-trigger').click(function() {
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

  // Delete application modal.
  $('.modal-app-delete-trigger').click(function() {
    var modal = $('#modal-app-delete');
    var appid = $(this).attr('appid');
    var name = $(this).attr('name');
    modal.find('input[name="delete-app-appid"]').val(appid);
    modal.find('#delete-app-name').html(name);
    modal.modal('open');
  });

  // Delete user modal.
  $('.modal-user-delete-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-user-delete');
    modal.find('#user-name').html(self.attr('delete-user-username'));
    modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('delete-user-uid'));
    modal.modal('open');
  });

  // User role create - role select
  $("#modal-user-role-create select[name='rid']").on('change', function() {
    var selected = $(this).find('option:selected').text();
    var modal = $('#modal-user-role-create');
    var selectAccid = modal.find("select[name='accid']");
    var selectAppid = modal.find("select[name='appid']");
    if (selected == 'Administrator') {
      selectAccid.val('').prop('disabled', true);
      selectAccid.formSelect();
      selectAppid.val('').prop('disabled', true);
      selectAppid.formSelect();
    }
    else if (selected == 'Account manager') {
      selectAccid.prop('disabled', false);
      selectAccid.formSelect();
      selectAppid.val('').prop('disabled', true);
      selectAppid.formSelect();
    }
    else {
      selectAccid.prop('disabled', false);
      selectAccid.formSelect();
      selectAppid.prop('disabled', false);
      selectAppid.formSelect();
    }
  });

  // User role create - application select
  $("#modal-user-role-create select[name='accid']").on('change', function() {
    var accid = $(this).val(),
        selectAppid = $('#modal-user-role-create').find("select[name='appid']");
    selectAppid.find('option').remove();
    selectAppid.find('option').remove();selectAppid.append($('<option>', {
      value: "",
      text: "Please select"
    }));
    GATERDATA.accAppMap.forEach(function (application, appid) {
      if (accid == application.accid) {
        selectAppid.append($('<option>', {
          value: appid,
          text : application.name
        }));
      }
    });
    selectAppid.val("");
    selectAppid.formSelect();
  });

  // User role delete
  $(".modal-user-role-delete-trigger").on('click', function() {
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

  // Upload a resource file into the editor
  $("#upload-resource-file").on('change',  function() {
    var $input = $(this);
    var inputFiles = this.files;
    if(inputFiles == undefined || inputFiles.length == 0) {
      return;
    }
    var inputFile = inputFiles[0];

    var reader = new FileReader();
    reader.onload = function(event) {
      $('textarea[name="meta"]').val(this.result);
      M.textareaAutoResize($('textarea[name="meta"]'));
    };
    reader.readAsText(inputFile);
  });

  // resource create - application select
  $("#create-resource select[name='acc']").on('change', function() {
    var acc = $(this).val(),
        selectApp = $('#create-resource').find("select[name='app']");
    selectApp.find('option').remove();
    selectApp.append($('<option>', {
      value: "",
      text: "Please select"
    }));
    for (var application in GATERDATA.accAppMap) {
      if (acc == GATERDATA.accAppMap[application]) {
        selectApp.append($('<option>', {
          value: application,
          text : application
        }));
      }
    }
    selectApp.val("");
    selectApp.formSelect();
  });

});
