const faqBtns = document.querySelectorAll(".faq-btn");

faqBtns.forEach(btn => {

    btn.addEventListener("click", () => {

        btn.nextElementSibling.classList.toggle("show");

    });

});