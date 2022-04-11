function navbar_menu() {
    if (state == false) {
        
        state = false;
    } else {
        
        state = true;
    }
}

navbar_menu.state = false;

function myFunction(mq) {
    if (mq.matches) {
        
    } else {
        
    }
}
  
let mq = window.matchMedia("(max-width: 767px)");
myFunction(mq) // Call listener function at run time
mq.addEventListener("change", myFunction);