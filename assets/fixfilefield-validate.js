// Grav Fix File Field Plugin - validation

window.addEventListener("DOMContentLoaded", (event) => {

  fixfilefieldplugin = (function () {
      
    function addErrorMsg(fileInput, msg) {
      fileInput.parentNode.parentNode.parentNode.classList.add('has-errors');
      const errorContainer = document.createElement('div');
      errorContainer.classList.add('text-error', 'fixfilefieldplugin-validation-error');
      errorContainer.textContent = msg;
      fileInput.parentNode.parentNode.append(errorContainer);
    }

    function sprintf(format, ...args) {
      return format.replace(/%([sdfx])/g, function(match, type) {
        const arg = args.shift();
        switch (type) {
          case 's':
            return String(arg);
          case 'd':
            return parseInt(arg, 10).toString();
          case 'f':
            return parseFloat(arg).toString();
          case 'x':
            return parseInt(arg, 10).toString(16);
          default:
            return match;
        }
      });
    }

    function validationInit(translations) {
      // Get the form element
      const form = document.querySelector('form');
      if (form !== null) {
        // Prevent the form from submitting immediately
        event.preventDefault();
        form.addEventListener('click', function (event) {
          const clickedElement = event.target;
        
          // Check if the clicked element is the Submit button or the Reset button
          if (clickedElement.matches('input[type="submit"], button[type="submit"]')) {
            // Submit button clicked
            console.log('Submit button clicked');

            const fileInputs = form.querySelectorAll('input[type="file"]');
            let allValid = true;

            // Process each set of file fields separately
            fileInputs.forEach(function (fileInput) {
              // Get the attributes from each form file field
              const required = fileInput.hasAttribute("data-required");
              
              // Determine the number of successfully uploaded files within the current set
              const uploads_success = fileInput.parentNode.querySelectorAll(".dz-success").length;
              // Determine the number of successfully uploaded files within the current set
              const uploads_complete = fileInput.parentNode.querySelectorAll(".dz-complete").length;
              
              // Perform validation on required attribute
              if (required && uploads_success < 1) {
                allValid = false;
                addErrorMsg(fileInput, translations. PLUGIN_FIXFILEFIELD.VALIDATION.ERRORS.REQUIRED);
              }

              let minNumber = null;
              let maxNumber = null;

              if (fileInput.hasAttribute("data-minnumberoffiles")) {
                minNumber = +fileInput.getAttribute("data-minnumberoffiles");
              } else {
                minNumber = null;
              }

              if (fileInput.hasAttribute("data-maxnumberoffiles")) {
                maxNumber = +fileInput.getAttribute("data-maxnumberoffiles");
              } else {
                maxNumber = null;
              }

              // Perform validation on number of files when the field is required
              // or the field is optional but a number of files has been uploaded
              if (required || uploads_complete > 0) {
                // Check the minimum and maximum requirements for uploaded files
                if (minNumber !== null && minNumber > 0 && uploads_complete < minNumber) {
                  allValid = false;
                  const t = translations. PLUGIN_FIXFILEFIELD.VALIDATION.ERRORS.UPLOAD_MIN;
                  if (minNumber > 1 && 'undefined' !== t.PLURAL) {
                    msg = sprintf(t.PLURAL, minNumber);
                  } else {
                    if ('undefined' !== t.SINGULAR) {
                      msg = sprintf(t.SINGULAR, minNumber);
                    } else {
                      msg = sprintf(t, minNumber);
                    }
                  }
                  addErrorMsg(fileInput, msg);
                }

                // Perform validation on maximum number of files
                if (maxNumber !== null && maxNumber > 0 && uploads_complete > maxNumber) {
                  allValid = false;
                  const t = translations. PLUGIN_FIXFILEFIELD.VALIDATION.ERRORS.UPLOAD_MAX;
                  if (maxNumber > 1 && 'undefined' !== t.PLURAL) {
                    msg = sprintf(t.PLURAL, maxNumber);
                  } else {
                    if ('undefined' !== t.SINGULAR) {
                      msg = sprintf(t.SINGULAR, maxNumber);
                    } else {
                      msg = sprintf(t, maxNumber);
                    }
                  }
                  addErrorMsg(fileInput, msg);
                }
              }

            });

            let hasError = false;
            allInputs = form.querySelectorAll('input');
            allInputs.forEach(function (anInput) {
              if (anInput.checkValidity() == false) {
                console.log('has-error');
                hasError = true;
              }
            });

            if (allValid && !hasError) {
              // If all pass validation, submit the form
              // For testing toggle (un)commenting below 2 lines
              // console.log('Will submit');
              form.submit();
            } else {
              // Prevent the form from submitting immediately
              event.preventDefault();
            }
          } else if (clickedElement.matches('.btn[type="reset"]')) {
            // Reset button clicked
            // Pretty raw way to reset but I haven't found a way to remove already uploaded files
            // from the server
            location.reload()

            // Remove any uploaded files. Needs to be completed by also removing
            // already successful uploads at the server side
            // const invalidUploads = form.querySelectorAll('div.dz-preview');
            // invalidUploads.forEach(function (invalidUpload) {
            //   invalidUpload.remove();
            // });
          } else {
            // Document click event handler (excluding clicks on the buttons)
            console.log('Click event on document');
        
            // Add click event listener to remove error messages and previews of failed uploads
            const errorContainers = form.querySelectorAll('.fixfilefieldplugin-validation-error, div.dz-error');
            errorContainers.forEach(function (errorContainer) {
              errorContainer.remove();
            });
            const errorElements = document.querySelectorAll('.has-errors');
            errorElements.forEach((errorElement) => {
              errorElement.classList.remove('has-errors');
            });
          }
        });

      }
    };

    return {
      validationInit: validationInit,
    };
  })();

  fixfilefieldplugin.validationInit(translations);

});