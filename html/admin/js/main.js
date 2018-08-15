$(document).ready(function(){
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
});
