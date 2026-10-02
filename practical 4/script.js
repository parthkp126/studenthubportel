const notification = document.getElementById("notification");

const closeNotification =
    document.getElementById("closeNotification");

closeNotification.addEventListener("click", function () {

    notification.classList.add("hide");

});


const menuBtn = document.getElementById("menuBtn");

const menu = document.getElementById("menu");

menuBtn.addEventListener("click", function () {

    menu.classList.toggle("show");

});


const themeBtn = document.getElementById("themeBtn");

const savedTheme =
    localStorage.getItem("studentHubTheme");

if (savedTheme === "dark") {

    document.body.classList.add("dark");

    themeBtn.textContent = "☀️";

}


themeBtn.addEventListener("click", function () {

    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {

        localStorage.setItem(
            "studentHubTheme",
            "dark"
        );

        themeBtn.textContent = "☀️";

    } else {

        localStorage.setItem(
            "studentHubTheme",
            "light"
        );

        themeBtn.textContent = "🌙";

    }

});


const faqQuestions =
    document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {

    question.addEventListener("click", function () {

        const item =
            question.parentElement;

        item.classList.toggle("active");

    });

});


const modal =
    document.getElementById("modal");

const openModal =
    document.getElementById("openModal");

const openModal2 =
    document.getElementById("openModal2");

const closeModal =
    document.getElementById("closeModal");

const closeModal2 =
    document.getElementById("closeModal2");


function showModal() {

    modal.classList.add("show");

}


function hideModal() {

    modal.classList.remove("show");

}


openModal.addEventListener(
    "click",
    showModal
);

openModal2.addEventListener(
    "click",
    showModal
);

closeModal.addEventListener(
    "click",
    hideModal
);

closeModal2.addEventListener(
    "click",
    hideModal
);


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
        number: "01 / 03",
        icon: "🌐",
        title: "HTML & CSS",
        text: "Build the structure and beautiful design of a webpage."
    },

    {
        number: "02 / 03",
        icon: "⚡",
        title: "JavaScript",
        text: "Make webpages interactive using DOM and event handling."
    },

    {
        number: "03 / 03",
        icon: "💾",
        title: "Local Storage",
        text: "Save user preferences and remember the selected theme."
    }

];


let currentSlide = 0;


const slideNumber =
    document.getElementById("slideNumber");

const slideContent =
    document.getElementById("slideContent");


function showSlide() {

    const slide =
        slides[currentSlide];

    slideNumber.textContent =
        slide.number;

    slideContent.innerHTML = `

        <div class="slide-icon">
            ${slide.icon}
        </div>

        <h2>
            ${slide.title}
        </h2>

        <p>
            ${slide.text}
        </p>

    `;

}


document.getElementById("nextBtn")
    .addEventListener("click", function () {

        currentSlide++;

        if (currentSlide >= slides.length) {

            currentSlide = 0;

        }

        showSlide();

    });


document.getElementById("prevBtn")
    .addEventListener("click", function () {

        currentSlide--;

        if (currentSlide < 0) {

            currentSlide =
                slides.length - 1;

        }

        showSlide();

    });


showSlide();