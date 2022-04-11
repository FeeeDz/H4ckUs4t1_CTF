function navbar_menu_button(change_state = true) {
    if(change_state)
        navbar_menu_button.state = !navbar_menu_button.state;

    if (navbar_menu_button.state == true) {
        navbar_nav.style.display = "";
        navbar_login.style.display = "";
    } else {
        navbar_nav.style.display = "none";
        navbar_login.style.display = "none";
    }
}

function mq_width_listener(mq){
    if (mq.matches) {
        navbar_logo.style.width = "50%";

        navbar_nav.style.width = "100%";
        navbar_nav_links.forEach(link => link.style.width = "100%");
        navbar_nav_links.forEach(link => link.style.textAlign = "left");

        navbar_login.style.width = "100%";
        navbar_login.style.textAlign = "center";

        navbar_menu_button(false);
    }
    else {
        navbar_logo.style.width = "30%";

        navbar_nav.style.display = "";
        navbar_nav.style.width = "40%";
        navbar_nav.style.textAlign = "center";
        navbar_nav_links.forEach(link => link.style.flex = "");

        navbar_login.style.display = "";
        navbar_login.style.width = "30%";
        navbar_login.style.textAlign = "right";
    }
}

navbar_menu_button.state = false;

let navbar_logo = document.querySelector("#navbar-logo");
let navbar_nav = document.querySelector("#navbar-nav");
let navbar_nav_links = navbar_nav.querySelectorAll("a");
let navbar_login = document.querySelector("#navbar-login");
  
let mq = window.matchMedia("(max-width: 767px)");
mq_width_listener(mq) // Call listener function at run time
mq.addEventListener("change", mq_width_listener);