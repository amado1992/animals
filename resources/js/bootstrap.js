window._ = require("lodash");

window.axios = require("axios");
window.axios.defaults.headers.common["Accepts"] = "application/json";
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;

const Toast = Swal.mixin({
    toast            : true,
    position         : "top",
    showConfirmButton: false,
    timer            : 3000,
    timerProgressBar : true,
    padding          : "1.25em 1em",
    showCloseButton  : true,
    didOpen          : (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
});

window.SuccessModal = (title) => {
    Toast.fire({
        title,
        icon: "success",
    });
};

window.InfoModal = (title) => {
    Toast.fire({
        title,
        icon: "info",
    });
};

window.ErrorModal = (title) => {
    Toast.fire({
        title,
        icon: "error",
    });
};

window.ConfirmChoice = (message) => {
    return Swal.fire({
        title             : "Confirm your choice",
        text              : message ?? "Are you sure you want to do this?",
        icon              : "warning",
        showCancelButton  : true,
        confirmButtonText : "Confirm",
    });
};
