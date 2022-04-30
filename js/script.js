document.querySelectorAll("#nav__nav > *").forEach(link => {
    if(link.href == window.location.href)
        link.classList.add("active");
});