/**
 * Hide the loader
 */
window.addEventListener('load',()=>{
    // removing the splash screen
    const loadingScreen = $('#splashScreen')[0];
    loadingScreen.style.zIndex = '-1';
    loadingScreen.style.opacity = '0';
})

/**
 * puts loader on submit button once pressed
 * @param {submit} event 
 */
function showSpinner(event) {
    const submit = Array.from(event.target.elements).filter((element)=>{ return element.type == 'submit'});
    submit[0].innerHTML = '<span class="spinner-border text-dark mx-auto" style="width:1rem;height:1rem;"></span>';
}

function customSubmitShowSpinner(btnElement){
    const form =  btnElement.closest('form');
    if(form.checkValidity()){
        btnElement.innerHTML = '<span class="spinner-border text-dark mx-auto"></span>';
        form.submit();
    }
  }

/**
 * 
 * @param {the input providing the value for the search} searchInput 
 * @param {the single element in the area to be searched} listItemIdentifier 
 * @param {the whole element holding the single elements} listElement 
 */
function searchList(searchInput, listItemIdentifier, listElement) {
    // Declare variables
    let a;
    const filter = searchInput.value.toUpperCase();
    const list = document.querySelectorAll(listItemIdentifier);

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < list.length; i++) {
        a = list[i].getElementsByTagName(listElement)[0];
        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
        list[i].style.display = "";
        } else {
        list[i].style.display = "none";
        }
    }
}

function displayElement(selector, displayType) {
    document.querySelector(selector).style.display = displayType;
}

function hideElement(selector) {
    document.querySelector(selector).style.display = 'none';
}