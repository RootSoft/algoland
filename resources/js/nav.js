window.dropdownHandler = function(element) {
    let single = element.getElementsByTagName("ul")[0];
    single.classList.toggle("hidden");
}

window.MenuHandler = function(el, val) {
    let MainList = el.parentElement.parentElement.getElementsByTagName("ul")[0];
    let closeIcon = el.parentElement.parentElement.getElementsByClassName("close-m-menu")[0];
    let showIcon = el.parentElement.parentElement.getElementsByClassName("show-m-menu")[0];
    if (val) {
        MainList.classList.remove("hidden");
        el.classList.add("hidden");
        closeIcon.classList.remove("hidden");
    } else {
        showIcon.classList.remove("hidden");
        MainList.classList.add("hidden");
        el.classList.add("hidden");
    }
}
window.searchHandler = function(element) {
    let Input = element.parentElement.getElementsByTagName("input")[0];
    Input.classList.toggle("w-0");
}

// ------------------------------------------------------
window.sideBar = document.getElementById("mobile-nav");
window.menu = document.getElementById("menu");
window.cross = document.getElementById("cross");
window.sidebarHandler = (check) => {
    if (check) {
        window.sideBar.style.transform = "translateX(0px)";
        window.menu.classList.add("hidden");
        window.cross.classList.remove("hidden");
    } else {
        window.sideBar.style.transform = "translateX(-500%)";
        window.menu.classList.remove("hidden");
        window.cross.classList.add("hidden");
    }
};
let list = document.getElementById("list");
let chevrondown = document.getElementById("chevrondown");
let chevronup = document.getElementById("chevronup");
window.listHandler = (check) => {
    if (check) {
        list.classList.remove("hidden");
        chevrondown.classList.remove("hidden");
        chevronup.classList.add("hidden");
    } else {
        list.classList.add("hidden");
        chevrondown.classList.add("hidden");
        chevronup.classList.remove("hidden");
    }
};
let list2 = document.getElementById("list2");
let chevrondown2 = document.getElementById("chevrondown2");
let chevronup2 = document.getElementById("chevronup2");
window.listHandler2 = (check) => {
    if (check) {
        list2.classList.remove("hidden");
        chevrondown2.classList.remove("hidden");
        chevronup2.classList.add("hidden");
    } else {
        list2.classList.add("hidden");
        chevrondown2.classList.add("hidden");
        chevronup2.classList.remove("hidden");
    }
};
