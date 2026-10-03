const themeToggle = document.getElementById("themeToggle");

const savedTheme = localStorage.getItem("studentPortalTheme");

if (savedTheme === "dark") {
    document.body.classList.add("dark");
    themeToggle.textContent = "☀️";
} else {
    themeToggle.textContent = "🌙";
}

themeToggle.addEventListener("click", function () {

    document.body.classList.toggle("dark");

    const isDark = document.body.classList.contains("dark");

    if (isDark) {
        localStorage.setItem("studentPortalTheme", "dark");
        themeToggle.textContent = "☀️";
    } else {
        localStorage.setItem("studentPortalTheme", "light");
        themeToggle.textContent = "🌙";
    }

});


const menuToggle = document.getElementById("menuToggle");
const mainNav = document.getElementById("mainNav");

menuToggle.addEventListener("click", function () {

    mainNav.classList.toggle("show");

    const menuOpen = mainNav.classList.contains("show");

    menuToggle.setAttribute("aria-expanded", menuOpen);

    if (menuOpen) {
        menuToggle.textContent = "✕";
    } else {
        menuToggle.textContent = "☰";
    }

});


const notificationBanner =
    document.getElementById("notificationBanner");

const closeNotification =
    document.getElementById("closeNotification");

closeNotification.addEventListener("click", function () {

    notificationBanner.classList.add("hide");

});


const faqQuestions =
    document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {

    question.addEventListener("click", function () {

        const answer =
            question.nextElementSibling;

        const isOpen =
            question.classList.contains("active");

        faqQuestions.forEach(function (otherQuestion) {

            otherQuestion.classList.remove("active");

            otherQuestion.setAttribute(
                "aria-expanded",
                "false"
            );

            otherQuestion.nextElementSibling.style.maxHeight = null;

        });

        if (!isOpen) {

            question.classList.add("active");

            question.setAttribute(
                "aria-expanded",
                "true"
            );

            answer.style.maxHeight =
                answer.scrollHeight + "px";

        }

    });

});


const modal =
    document.getElementById("portalModal");

const openModal =
    document.getElementById("openModal");

const closeModal =
    document.getElementById("closeModal");

const modalOkay =
    document.getElementById("modalOkay");


function showModal() {

    modal.classList.add("show");

    modal.setAttribute("aria-hidden", "false");

}


function hideModal() {

    modal.classList.remove("show");

    modal.setAttribute("aria-hidden", "true");

}


openModal.addEventListener("click", showModal);

closeModal.addEventListener("click", hideModal);

modalOkay.addEventListener("click", hideModal);


modal.addEventListener("click", function (event) {

    if (event.target === modal) {
        hideModal();
    }

});


document.addEventListener("keydown", function (event) {

    if (event.key === "Escape") {
        hideModal();
    }

});


const slides = [

    {
        icon: "📚",
        title: "Academic Resources",
        description:
            "Access courses, assignments and important academic information from one place."
    },

    {
        icon: "📝",
        title: "Assignment Management",
        description:
            "Check your assignments, submission information and academic tasks easily."
    },

    {
        icon: "📊",
        title: "Attendance Tracking",
        description:
            "Monitor your attendance information and stay updated with your academic progress."
    },

    {
        icon: "👨‍🏫",
        title: "Faculty Information",
        description:
            "Find faculty information and connect with the appropriate academic resources."
    }

];


let currentSlide = 0;

const slideIcon =
    document.getElementById("slideIcon");

const slideTitle =
    document.getElementById("slideTitle");

const slideDescription =
    document.getElementById("slideDescription");

const slideNumber =
    document.getElementById("slideNumber");


function showSlide(index) {

    const slide = slides[index];

    slideIcon.textContent = slide.icon;

    slideTitle.textContent = slide.title;

    slideDescription.textContent =
        slide.description;

    slideNumber.textContent = index + 1;

}


document.getElementById("nextSlide")
    .addEventListener("click", function () {

        currentSlide++;

        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }

        showSlide(currentSlide);

    });


document.getElementById("previousSlide")
    .addEventListener("click", function () {

        currentSlide--;

        if (currentSlide < 0) {
            currentSlide = slides.length - 1;
        }

        showSlide(currentSlide);

    });


showSlide(currentSlide);