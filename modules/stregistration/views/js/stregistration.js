$(document).ready(function() {
    $('input[name="st_reg_terms_and_conditions"]').prop("disabled", "true");
    $('.st_reg_terms_link').on('click', function(event) {
        event.preventDefault();
        var url = $(event.target).attr('href');
        if (url) {
          url += '?content_only=1';
          $.get(url, function(content) {
            $('#st_reg_modal').find('.js-modal-content').html($(content).find('.page-cms').contents());
          }).fail(function(resp){
          });
        }

        $('#st_reg_modal').modal('show');
      });
    $(document).on('submit', '#customer-form', function () {
      var email_address = $(this).find('input[name="email"]').val();
      var confirm_email_address = $(this).find('input[name="confirm_email"]').val();
      if(!email_address || (email_address && !/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}$/.test(email_address))){
        $(this).find('input[name="email"]').closest('.form-group').addClass('has-danger');
        $(this).find('.js-submit-active').removeClass('disabled active');
        return false;
      }
      if(!confirm_email_address || (confirm_email_address && !/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}$/.test(confirm_email_address))){
        $(this).find('input[name="confirm_email"]').closest('.form-group').addClass('has-danger');
        $(this).find('.js-submit-active').removeClass('disabled active');
        return false;
      }
      if(confirm_email_address!=email_address){
        $(this).find('input[name="email"]').closest('.form-group').addClass('has-danger');
        $(this).find('input[name="confirm_email"]').closest('.form-group').addClass('has-danger');
        $(this).find('.js-submit-active').removeClass('disabled active');
        return false;
      }
    });
    if($('#customer-form input[name="confirm_email"]').length){
      $('#customer-form input[name="confirm_email"]').closest('.form-group').insertAfter($('#customer-form input[name="email"]').closest('.form-group'));
    }
});
