// =============================
// HireConnect JavaScript
// =============================

document.addEventListener("DOMContentLoaded", () => {

    console.log("HireConnect Loaded Successfully");

    // =============================
    // Login Form Validation
    // =============================
    const loginForm = document.querySelector("form");

    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {

            const email = document.querySelector(
                'input[name="email"]'
            );

            const password = document.querySelector(
                'input[name="password"]'
            );

            if (email && password) {

                if (email.value.trim() === "") {
                    e.preventDefault();
                    alert("Please enter your email.");
                    return;
                }

                if (password.value.trim() === "") {
                    e.preventDefault();
                    alert("Please enter your password.");
                    return;
                }

                if (password.value.length < 6) {
                    e.preventDefault();
                    alert(
                        "Password must be at least 6 characters."
                    );
                    return;
                }
            }
        });
    }

    // =============================
    // Search Form
    // =============================
    const searchButtons =
        document.querySelectorAll(".btn-primary");

    searchButtons.forEach(button => {

        if (
            button.textContent
                .toLowerCase()
                .includes("search")
        ) {

            button.addEventListener("click", (e) => {

                e.preventDefault();

                const inputs =
                    document.querySelectorAll(
                        '.form-control'
                    );

                let values = [];

                inputs.forEach(input => {
                    values.push(input.value);
                });

                alert(
                    "Searching for: " +
                    values.join(" | ")
                );
            });
        }
    });

    // =============================
    // Apply Buttons
    // =============================
    const applyButtons =
        document.querySelectorAll(
            ".job-card .btn"
        );

    applyButtons.forEach(button => {

        button.addEventListener("click", () => {

            const jobCard =
                button.closest(".job-card");

            const jobTitle =
                jobCard.querySelector("h4")
                .textContent;

            alert(
                `Application started for ${jobTitle}`
            );
        });
    });

    // =============================
    // Social Login Buttons
    // =============================
    const socialButtons =
        document.querySelectorAll(
            ".social button"
        );

    socialButtons.forEach(button => {

        button.addEventListener("click", () => {

            alert(
                `${button.textContent} feature coming soon.`
            );
        });
    });

    // =============================
    // Smooth Scroll
    // =============================
    document
        .querySelectorAll('a[href^="#"]')
        .forEach(anchor => {

            anchor.addEventListener(
                "click",
                function (e) {

                    const target =
                        document.querySelector(
                            this.getAttribute("href")
                        );

                    if (target) {
                        e.preventDefault();

                        target.scrollIntoView({
                            behavior: "smooth"
                        });
                    }
                }
            );
        });

    // =============================
    // Navbar Shadow on Scroll
    // =============================
    const navbar =
        document.querySelector(".navbar");

    if (navbar) {

        window.addEventListener(
            "scroll",
            () => {

                if (
                    window.scrollY > 50
                ) {

                    navbar.classList.add(
                        "shadow"
                    );

                } else {

                    navbar.classList.remove(
                        "shadow"
                    );
                }
            }
        );
    }

});