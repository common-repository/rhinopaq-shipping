"use strict";

console.log("ADMIN RHINO SHIPPING!");

// Make checkboxes in the admin area to sliders
const checkboxes = document.querySelectorAll('.rhinopaq-settings-wrap input[type="checkbox"]');
checkboxes.forEach((checkbox) => {
  // Build the toggle switch
  const toggleSwitch = document.createElement('span');
  toggleSwitch.classList.add('toggle-switch');
  checkbox.parentNode.insertBefore(toggleSwitch, checkbox.nextSibling);

  // Set the initiale status based on the checkbox
  if (checkbox.checked) {
    toggleSwitch.classList.add('toggle-switch--on');
  } else {
    toggleSwitch.classList.add('toggle-switch--off');
  }

  // Add an eventlistener
  checkbox.addEventListener('change', () => {
    if (checkbox.checked) {
      toggleSwitch.classList.remove('toggle-switch--off');
      toggleSwitch.classList.add('toggle-switch--on');
    } else {
      toggleSwitch.classList.remove('toggle-switch--on');
      toggleSwitch.classList.add('toggle-switch--off');
    }
  });
}); 

// Create event listeners to make a better form control
// Base activation
rhinosettingsListener('rhinopaq-enabled','.all-fields');
// Smart Settings
rhinosettingsListener('smart-enabled','.smart-fields');

// Add an event listener to the checkbox
function rhinosettingsListener(inputCheckField = '',fieldsToChange = ''){
    // Activate / Deactivate parts of the settings form
    // Get the checkbox element
    var checkbox = document.getElementById(inputCheckField);
    // Get an array of input elements to disable
    var inputsToDisable = document.querySelectorAll(fieldsToChange);
    // Initial check and update of the fields
    if (!checkbox.checked) {
      for (let i = 0; i < inputsToDisable.length; i++) {
        inputsToDisable[i].disabled = true;
      }
    } else { // Otherwise, enable the input fields
      for (let i = 0; i < inputsToDisable.length; i++) {
        inputsToDisable[i].disabled = false;
      }
    }
    // Now set up the event listener 
    checkbox.addEventListener('change', function() {

        // If the checkbox is unchecked, disable the input fields
        if (!this.checked) {
          for (let i = 0; i < inputsToDisable.length; i++) {
            inputsToDisable[i].disabled = true;
          }
        } else { // Otherwise, enable the input fields
          for (let i = 0; i < inputsToDisable.length; i++) {
            inputsToDisable[i].disabled = false;
          }
        }

        // Change appearance of the other sliders
        if(fieldsToChange == '.all-fields'){
          var checkboxSliders = document.querySelectorAll('.rhino-checkboxes');
          checkboxSliders.forEach((checkboxSlider) => {
            if(this.checked){
              checkboxSlider.parentNode.querySelectorAll('span')[0].classList.remove('disabledSlider');
            } else {
              checkboxSlider.parentNode.querySelectorAll('span')[0].classList.add('disabledSlider');
            }
          });
        }
    });
}

