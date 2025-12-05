jQuery(document).ready(function ($) {
    // Find the form and the submit button
    const $form = $('#pmpro_form');
    const $submitButton = $form.find('input[type="submit"]');
  
    // Find all required fields by the data attribute
    const $requiredFields = $form.find('[data-required="true"]');
  
    // If we can't find the submit button, stop.
    if (!$submitButton.length) {
      return;
    }
  
    // --- THIS IS THE FIX ---
    // Disable the submit button immediately on page load.
    $submitButton.prop('disabled', true);
  
    // --- Validation Functions for each field type ---
  
    function isTextareaValid($field) {
      const minLength = parseInt($field.data('min-length'), 10) || 1;
      return $field.val().trim().length >= minLength;
    }
  
    function isSelectValid($field) {
      return $field.val() !== '';
    }
  
    function isRadioValid($field) {
      // The field is the <fieldset>
      const fieldName = $field.find('input[type="radio"]').first().attr('name');
      return $(`input[name="${fieldName}"]:checked`).length > 0;
    }
  
    function isCheckboxValid($field) {
      // The field is the <fieldset>
      return $field.find('input[type="checkbox"]:checked').length > 0;
    }
  
    // --- Main Validation Checker ---
  
    function validateAllFields() {
      let allFieldsValid = true;
  
      $requiredFields.each(function () {
        const $field = $(this);
        let isValid = false;
  
        if ($field.is('textarea')) {
          isValid = isTextareaValid($field);
        } else if ($field.is('select')) {
          isValid = isSelectValid($field);
        } else if ($field.is('fieldset')) {
          const type = $field.data('type');
          if (type === 'radio') {
            isValid = isRadioValid($field);
          } else if (type === 'checkbox') {
            isValid = isCheckboxValid($field);
          }
        }
  
        // Add/remove error class for visual feedback
        if (!isValid) {
          allFieldsValid = false;
          $field.addClass('pmpro_form_field-error');
        } else {
          $field.removeClass('pmpro_form_field-error');
        }
      });
  
      // Enable or disable the button
      $submitButton.prop('disabled', !allFieldsValid);
    }
  
    // Add event listeners to all form elements within our custom form
    $('#pmpro_custom_cancel_form').on(
      'input change',
      'textarea, select, input',
      validateAllFields
    );
  
    // Run validation once on page load to ensure button is disabled
    validateAllFields();
  });