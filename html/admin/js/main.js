$(document).ready(function(){
  M.AutoInit();

  // Edit application modal
  $('.modal-app-edit').click(function() {
    var self = $(this);
    var modal = $('#modal-edit');
    modal.find('#edit-app-id').val(self.attr('app-id'));
    modal.find('#edit-app-name').val(self.attr('app-name'));
    modal.modal('open');
  });
});
