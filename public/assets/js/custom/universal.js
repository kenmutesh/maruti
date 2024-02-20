/**
*This script is used for all pages present in the system
*/
window.addEventListener('load',()=>{
  const loadingScreen = $('#splashScreen')[0];
  loadingScreen.style.zIndex = '-1';
  loadingScreen.style.opacity = '0';

  // check for already set theme
  if (localStorage.getItem("powdercoating-theme") == 'LIGHT') {
    $('body').addClass('white-content');
  }else {
    $('body').removeClass('white-content');
  }

  // the theme of the system
  $('.light-badge').click(function() {
    $('body').addClass('white-content');
     localStorage.setItem("powdercoating-theme", 'LIGHT');
  });

  $('.dark-badge').click(function() {
    $('body').removeClass('white-content');
    localStorage.setItem("powdercoating-theme", 'DARK');
  });

  // query for activation key fields
  generateActivationKey();

  const popups = $(".hide-popup");

  [...popups].forEach((popup) => {
    setTimeout(()=>{
      popup.querySelector('.close').click();
    }, 3500);
  })

})

// show a spinner for the action button when clicked
function showSpinner(event) {
  event.target.submit_btn.innerHTML = '<span class="spinner-border text-dark mx-auto"></span>';
}

function customSubmitShowSpinner(btnElement){
  const form =  btnElement.closest('form');
    if(form.checkValidity()){
        btnElement.innerHTML = '<span class="spinner-border text-dark mx-auto"></span>';
        form.submit();
    }
}

// function to generate random strings - mostly for use by activation key field
function getRandomString(length) {
    var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var result = '';
    for ( var i = 0; i < length; i++ ) {
        result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
    }
    return result;
}

function generateActivationKey(update = false ) {
  let activationKeyInput;
  if (update) {
    activationKeyInput = document.querySelectorAll('input[name="activation_key_update"]');
  }else {
    activationKeyInput = document.querySelectorAll('input[name="activation_key"]');
  }


  [...activationKeyInput].forEach((activationKeyField) => {
    activationKeyField.value = getRandomString(10);
  })
}
