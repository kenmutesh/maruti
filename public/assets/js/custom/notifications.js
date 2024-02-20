window.addEventListener('load', ()=>{
  setInterval(async function () {
     const response = await fetch('/checkNotification');
   
     const json = await response.json();
     const notificationDropdown = document.querySelector('#notificationDropdown');
     let newHTML = '';
     let unreadPresent = false;
     if (json.length > 0) {
       json.forEach((notification) => {
         if (notification.read_status) {
   
           // its read
           newHTML += `<li class="dropdown-item text-center align-items-center p-1 m-1 border"> <p>${ decodeHtml(notification.message) }</p></li>`;
         }else {
           // not read
           newHTML += `<li class="dropdown-item text-center align-items-center p-1 m-1 border"> <p>${ decodeHtml(notification.message) }</p> (NEW)</li>`;
           unreadPresent = true;
         }
       })
       if (unreadPresent) {
         document.querySelector('#notificationBubble .notify').classList.remove('invisible');
         var audio = new Audio('/assets/sound/notify.wav');
           audio.play();
       }else {
         document.querySelector('#notificationBubble').classList.remove('invisible');
       }
     }else {
       newHTML = `<li class="dropdown-item text-center d-flex align-items-center p-1 m-1">No unread notifications</li>`;
     }
 
     notificationDropdown.innerHTML = newHTML;
     
   
   }, 10000)
})


async function readNotifications() {
  const response = await fetch('/clearNotification');

  const json = await response.json();

  document.querySelector('#notificationBubble').classList.remove('badgebit');
}

function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
