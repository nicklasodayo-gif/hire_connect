const testimonials = document.querySelectorAll(".testimonial");
let current = 0;

setInterval(() => {

    testimonials[current].classList.remove("active");

    current++;

    if(current >= testimonials.length){
        current = 0;
    }

    testimonials[current].classList.add("active");

}, 4000);