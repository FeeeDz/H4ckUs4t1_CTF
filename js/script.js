function flush_css(element) {
    element.offsetHeight;
}

function navbar_menu_button(change_state = true) {
    if(change_state)
        navbar_menu_button.state = !navbar_menu_button.state;

    if (navbar_menu_button.state == true) {
        navbar_nav.classList.remove("hidden");
        navbar_login_menu.classList.remove("hidden");

    } else {
        navbar_nav.classList.add("hidden");
        navbar_login_menu.classList.add("hidden");
    }

    // main.style.minHeight = "calc(100vh - " + (parseInt(navbar.clientHeight) + parseInt(footer.clientHeight)) + "px)";
}

function mq_width_listener(mq){
    if (mq.matches) {
        navbar_logo.style.width = "50%";

        navbar_nav.style.width = "100%";
        navbar_nav.style.textAlign = "left";
        navbar_nav_links.forEach(element => element.style.paddingLeft = "15px");
        navbar_nav_links.forEach(element => element.style.margin = "0");

        navbar_login_menu.style.width = "100%";
        navbar_login_menu.style.justifyContent = "center";

        navbar_menu_button(false);
    }
    else {
        navbar_logo.style.width = "27%";

        navbar_nav.classList.remove("hidden");
        navbar_nav.style.width = "46%";
        navbar_nav.style.textAlign = "center";
        navbar_nav_links.forEach(element => element.style.paddingLeft = "");
        navbar_nav_links.forEach(element => element.style.margin = "");

        navbar_login_menu.classList.remove("hidden");
        navbar_login_menu.style.width = "27%";
        navbar_login_menu.style.justifyContent = "right";
        
        // main.style.minHeight = "calc(100vh - " + (parseInt(navbar.clientHeight) + parseInt(footer.clientHeight)) + "px)";
    }
    
    if(mq_width_listener.state != mq.matches){
        body.classList.add("disable-animation");
        flush_css(body);
        body.classList.remove("disable-animation");
        flush_css(body);
    }

    mq_width_listener.state = mq.matches
}

let body = document.querySelector("body");
let navbar = document.querySelector("#navbar");
let navbar_logo = navbar.querySelector("#navbar-logo");
let navbar_nav = navbar.querySelector("#navbar-nav");
let navbar_nav_links = navbar_nav.querySelectorAll("a");
let navbar_login_menu = navbar.querySelector("#navbar-login-menu");
let main = document.querySelector("#main");
let footer = document.querySelector("#footer");

navbar_menu_button.state = false;
mq_width_listener.state = false;
  
let mq = window.matchMedia("(max-width: 767px)");
mq_width_listener(mq)
mq.addEventListener("change", mq_width_listener);

window.onload = function() {
    body.classList.remove("disable-animation");
}