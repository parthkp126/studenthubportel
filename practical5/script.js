const form = document.getElementById("registrationForm");

const name = document.getElementById("name");
const email = document.getElementById("email");
const mobile = document.getElementById("mobile");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const course = document.getElementById("course");
const year = document.getElementById("year");
const terms = document.getElementById("terms");

const namePattern = /^[A-Za-z ]{3,40}$/;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mobilePattern = /^[6-9][0-9]{9}$/;


function setError(id, message) {
    document.getElementById(id).textContent = message;
}


function clearErrors() {
    document.querySelectorAll("small").forEach(function (item) {
        item.textContent = "";
    });

    document.getElementById("successMessage").textContent = "";
}


/* Password Strength */

password.addEventListener("input", function () {

    const value = password.value;

    const bar = document.getElementById("strengthBar");
    const text = document.getElementById("strengthText");

    let score = 0;

    if (value.length >= 8) {
        score++;
    }

    if (/[A-Z]/.test(value)) {
        score++;
    }

    if (/[a-z]/.test(value)) {
        score++;
    }

    if (/[0-9]/.test(value)) {
        score++;
    }

    if (/[^A-Za-z0-9]/.test(value)) {
        score++;
    }


    if (value.length === 0) {

        bar.style.width = "0%";

        text.textContent =
            "Password Strength: Not entered";

    }

    else if (score <= 2) {

        bar.style.width = "35%";

        text.textContent =
            "Password Strength: Weak";

    }

    else if (score <= 4) {

        bar.style.width = "70%";

        text.textContent =
            "Password Strength: Medium";

    }

    else {

        bar.style.width = "100%";

        text.textContent =
            "Password Strength: Strong";
    }

});


/* Form Validation */

form.addEventListener("submit", function (event) {

    event.preventDefault();

    clearErrors();

    let valid = true;


    /* Name */

    if (!namePattern.test(name.value.trim())) {

        setError(
            "nameError",
            "Enter a valid name using letters only."
        );

        valid = false;
    }


    /* Email */

    if (!emailPattern.test(email.value.trim())) {

        setError(
            "emailError",
            "Enter a valid email address."
        );

        valid = false;
    }


    /* Mobile */

    if (!mobilePattern.test(mobile.value.trim())) {

        setError(
            "mobileError",
            "Enter a valid 10-digit mobile number."
        );

        valid = false;
    }


    /* Course */

    if (course.value === "") {

        setError(
            "courseError",
            "Please select your course."
        );

        valid = false;
    }


    /* Year */

    if (year.value === "") {

        setError(
            "yearError",
            "Please select your academic year."
        );

        valid = false;
    }


    /* Gender */

    const gender =
        document.querySelector(
            'input[name="gender"]:checked'
        );

    if (!gender) {

        setError(
            "genderError",
            "Please select your gender."
        );

        valid = false;
    }


    /* Password */

    const strongPassword =
        password.value.length >= 8 &&
        /[A-Z]/.test(password.value) &&
        /[a-z]/.test(password.value) &&
        /[0-9]/.test(password.value) &&
        /[^A-Za-z0-9]/.test(password.value);


    if (!strongPassword) {

        setError(
            "passwordError",
            "Use 8+ characters with uppercase, lowercase, number and special character."
        );

        valid = false;
    }


    /* Confirm Password */

    if (
        confirmPassword.value === "" ||
        confirmPassword.value !== password.value
    ) {

        setError(
            "confirmPasswordError",
            "Passwords do not match."
        );

        valid = false;
    }


    /* Terms */

    if (!terms.checked) {

        setError(
            "termsError",
            "Please accept the Terms and Conditions."
        );

        valid = false;
    }


    /* Successful Registration */

    if (valid) {

        document.getElementById("successMessage").textContent =
            "✓ Registration Successful! Welcome to Student Hub.";

        form.reset();

        document.getElementById("strengthBar").style.width = "0%";

        document.getElementById("strengthText").textContent =
            "Password Strength: Not entered";
    }

});


/* Restart Button */

document
    .getElementById("restartBtn")
    .addEventListener("click", function () {

        setTimeout(function () {

            clearErrors();

            document.getElementById("strengthBar").style.width = "0%";

            document.getElementById("strengthText").textContent =
                "Password Strength: Not entered";

        }, 0);

    });