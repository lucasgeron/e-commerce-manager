$(document).ready(function() {
  $('*[data-toggle="tooltip"]').tooltip()
  data_confirm();
  toogle_contact_message();
  auto_complete_products_search();
  jQuery("#client_phone").mask("(99) 9999-9999");
  jQuery("#client_address_cep").mask("99999-999");
  jQuery("#client_cpf").mask("999.999.999-99");
  jQuery("#client_cnpj").mask("99.999.999/9999-99");

});

/*** Confirm dialog **/
var data_confirm = function () {
   $('a[data-confirm], button[data-confirm]').click( function () {
      var msg = $(this).data('confirm');
      return confirm(msg);
   });
};

/*
 * Show and hide contact message **/
var toogle_contact_message = function () {
   $('#contacts table tbody a.show').on('click', function (event) {
      event.preventDefault();
      var id = $(this).attr('href');
      var msg = $(this).closest('tbody').find(id);
      msg.toggleClass('hidden');
   });
};

var auto_complete_products_search = function () {
  $('#autocomplete_products').autocomplete({
  serviceUrl: $('#autocomplete_products').data('url'),
  onSelect: function (suggestion) {
      $('#product_id').val(suggestion.data);
  }
});
};
