$(document).ready(function() {

  M.AutoInit();


  // Edit application modal.
  $('.modal-app-edit-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-app-edit');
    modal.find('#edit-app-id').val(self.attr('app-id'));
    modal.find('#edit-app-name').val(self.attr('app-name'));
    modal.modal('open');
  });

  // Delete application modal.
  $('.modal-app-delete-trigger').click(function() {
    var self = $(this);
    var modal = $('#modal-app-delete');
    modal.find('#delete-app-id').val(self.attr('app-id'));
    modal.find('#delete-app-name').text(self.attr('app-name'));
    modal.modal('open');
  });

  // Delete user modal.
  $('.modal-user-delete-trigger').click(function() {
    console.log('jhi');
    var self = $(this);
    var modal = $('#modal-user-delete');
    modal.find('#user-name').html(self.attr('user-name'));
    modal.find('a#delete-user').attr('href', '/user/delete/' + self.attr('user-account-id'));
    modal.modal('open');
  });

  $('.close-gaterdata-alert').click(function(){
    $(this).closest('.gaterdata-alert').fadeOut("slow", function() {
    });
  });
});
