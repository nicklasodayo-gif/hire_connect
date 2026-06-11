 function getAdvice() {

            let interest =
                document.getElementById("interest").value;

            let skill =
                document.getElementById("skill").value;

            let result =
                document.getElementById("result");

            let advice = "";

            if(interest === "technology"){

                advice =
                `
                <h3>Technology Career Path</h3>
                <p>
                Your interest in technology and skill in
                <strong>${skill}</strong>
                can lead to careers such as:
                Software Developer, Web Designer,
                Cybersecurity Analyst, or Data Scientist.
                </p>
                `;

            } else if(interest === "business"){

                advice =
                `
                <h3>Business Career Path</h3>
                <p>
                Consider roles such as Marketing Manager,
                Business Analyst, Entrepreneur,
                or Human Resource Specialist.
                </p>
                `;

            } else if(interest === "design"){

                advice =
                `
                <h3>Design Career Path</h3>
                <p>
                You may enjoy becoming a UI/UX Designer,
                Graphic Designer, Animator,
                or Product Designer.
                </p>
                `;

            } else if(interest === "health"){

                advice =
                `
                <h3>Healthcare Career Path</h3>
                <p>
                Explore careers such as Nurse,
                Doctor, Nutritionist,
                Medical Technologist,
                or Healthcare Administrator.
                </p>
                `;

            } else {

                advice =
                "<p>Please select an interest area.</p>";
            }

            result.style.display = "block";
            result.innerHTML = advice;
        }