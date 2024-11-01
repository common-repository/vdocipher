(function(){
  var inputField = document.getElementById('vdo_client_key');
  var toggleKey = document.getElementById('toggle_API_visibility');

  var currentState;

  toggleKey.addEventListener('click', function(e){
    e.preventDefault();
    toggleVisibility(e);
  });

  function toggleVisibility(e){
    currentState = e.target.getAttribute('data-protected');
    if (currentState === 'On'){
      showKey();
    }
    else {
      hideKey();
    }
  }

  function showKey(){
    toggleKey.setAttribute('data-protected', 'Off');
    toggleKey.innerHTML = 'Hide API Secret Key';
    inputField.type='text';
  }

  function hideKey(){
    toggleKey.setAttribute('data-protected', 'On');
    toggleKey.innerHTML = 'Show API Secret Key';
    inputField.type='password';
  }

}());
