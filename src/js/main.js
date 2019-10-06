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
    var self = $(this);
    var modal = $('#modal-app-edit');
    modal.find('#edit-app-accid').val(self.attr('acc-id'));
    modal.find('#edit-app-accid').formSelect();
    modal.find('#edit-app-appid').val(self.attr('app-id'));
    modal.find('#edit-app-name').val(self.attr('app-name'));
    modal.modal('open');
  });

  // Delete application modal.
  $('.modal-app-delete-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-app-delete');
    modal.find('#delete-app-appid').val(self.attr('app-id'));
    modal.find('#delete-app-name').html(self.attr('app-name'));
    modal.modal('open');
  });

  // Edit user modal.
  $('.modal-user-edit-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-user-edit');
    modal.find('#user-name').html(self.attr('user-name'));
    modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('user-account-id'));
    modal.modal('open');
  });

  // Delete user modal.
  $('.modal-user-delete-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-user-delete');
    modal.find('#user-name').html(self.attr('user-name'));
    modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('user-account-id'));
    modal.modal('open');
  });

});
