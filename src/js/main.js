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
    GATERDATA.accAppMap.forEach(function (application, appid) {
      if (accid == application.accid) {
        selectAppid.append($('<option>', {
          value: appid,
          text : application.name
        }));
      }
    });
    selectAppid.formSelect();
  });

});
