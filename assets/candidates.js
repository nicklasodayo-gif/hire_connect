const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function () {

    if (this.value.length > 0) {
        this.style.borderColor = "#0d6efd";
    } else {
        this.style.borderColor = "#ccc";
    }

});