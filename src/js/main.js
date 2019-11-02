$(document).ready(function() {

  M.AutoInit();

  // Close alert panel.
  $('.close-gaterdata-alert').click(function(){
    $(this).closest('.gaterdata-alert').fadeOut("slow", function() {});
  });

  // Edit account modal.
  $('.modal-acc-edit-trigger').click(function() {
    var modal = $('#modal-acc-edit');
    var acc_name = $(this).attr('acc-name');
    modal.find('input[name="new-acc-name"]').val(acc_name);
    modal.find('input[name="acc-name"]').val(acc_name);
    modal.modal('open');
  });

  // Delete account modal.
  $('.modal-acc-delete-trigger').click(function() {
    var modal = $('#modal-acc-delete');
    var acc_name = $(this).attr('acc-name');
    modal.find('input[name="acc-name"]').val(acc_name);
    modal.find('.delete-acc-name').html(acc_name);
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

  // Edit user modal.
  // $('.modal-user-edit-trigger').click(function() {
  //   var self = $(this);
  //   var modal = $('#modal-user-edit');
  //   modal.find('#user-name').html(self.attr('user-name'));
  //   modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('user-account-id'));
  //   modal.modal('open');
  // });

  // Delete user modal.
  $('.modal-user-delete-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-user-delete');
    modal.find('#user-name').html(self.attr('delete-user-username'));
    modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('delete-user-uid'));
    modal.modal('open');
  });

});
