
document.addEventListener("DOMContentLoaded", function () {

    setupTheme();
    setupMenu();
    setupModal();
    setupActiveSidebar();
    setupRegistration();

    if (document.getElementById("eventsContainer")) {
        setupEvents();
        loadEvents();
    }

    if (document.getElementById("faqContainer")) {
        setupFAQ();
        loadFAQs();
    }

});


function setupTheme() {

    const button = document.getElementById("themeBtn");

    if (!button) return;

    button.addEventListener("click", function () {

        document.body.classList.toggle("dark");

        if (document.body.classList.contains("dark")) {
            button.textContent = "☀️";
        } else {
            button.textContent = "🌙 Theme";
        }

    });

}


function setupMenu() {

    const button = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");

    if (!button || !sidebar) return;

    button.addEventListener("click", function () {

        sidebar.classList.toggle("open");

        const isOpen = sidebar.classList.contains("open");

        button.setAttribute("aria-expanded", isOpen);

    });

}


function setupModal() {

    const helpButton = document.getElementById("helpBtn");
    const closeButton = document.getElementById("closeModal");
    const modal = document.getElementById("helpModal");

    if (!modal) return;

    if (helpButton) {

        helpButton.addEventListener("click", function () {
            modal.hidden = false;
        });

    }

    if (closeButton) {

        closeButton.addEventListener("click", function () {
            modal.hidden = true;
        });

    }

}


/* =========================
   ACTIVE SIDEBAR
========================= */

function setupActiveSidebar() {

    const links = document.querySelectorAll(".sidebar a");

    const currentPage =
        window.location.pathname
            .split("/")
            .pop()
            .toLowerCase();

    links.forEach(function (link) {

        const href = link.getAttribute("href");

        if (!href) return;

        if (href.toLowerCase() === currentPage) {
            link.classList.add("active");
        }

    });

}


/* =========================
   REGISTRATION
========================= */

function setupRegistration() {

    const form = document.getElementById("registrationForm");

    if (!form) return;

    const message =
        document.getElementById("registrationMessage");

    if (!message) return;

    const password =
        document.getElementById("password");

    const confirmPassword =
        document.getElementById("confirmPassword");

    const strengthText =
        document.getElementById("strengthText");

    const strengthBar =
        document.getElementById("strengthBar");

    const resetButton =
        document.getElementById("resetRegistration");


    form.addEventListener("submit", function (event) {

        event.preventDefault();

        clearRegistrationErrors();

        message.textContent =
            "⏳ Submitting registration...";

        message.className =
            "registration-message";


        const formData = new FormData(form);


        fetch("process.php", {
            method: "POST",
            body: formData
        })

        .then(function (response) {

            return response.text();

        })

        .then(function (text) {

            let data;

            try {

                data = JSON.parse(text);

            } catch (error) {

                throw new Error("Invalid server response");

            }


            if (data.success) {

                message.textContent =
                    "✅ " + data.message;

                message.className =
                    "registration-message success";


                form.reset();


                if (strengthText) {

                    strengthText.textContent =
                        "Password strength: Not entered";

                }


                if (strengthBar) {

                    strengthBar.style.width =
                        "0%";

                }

            } else {

                message.textContent =
                    "❌ " + data.message;

                message.className =
                    "registration-message error";


                if (data.errors) {

                    Object.keys(data.errors).forEach(function (field) {

                        const errorElement =
                            document.getElementById(
                                field + "Error"
                            );


                        if (errorElement) {

                            errorElement.textContent =
                                data.errors[field];

                        }

                    });

                }

            }

        })

        .catch(function (error) {

            console.error(error);

            message.textContent =
                "❌ Unable to connect to the server.";

            message.className =
                "registration-message error";

        });

    });


    /* PASSWORD STRENGTH */

    if (password) {

        password.addEventListener("input", function () {

            const value = password.value;


            if (value === "") {

                if (strengthText) {

                    strengthText.textContent =
                        "Password strength: Not entered";

                }

                if (strengthBar) {

                    strengthBar.style.width =
                        "0%";

                }

                return;

            }


            let score = 0;


            if (value.length >= 8) {
                score++;
            }

            if (/[a-z]/.test(value)) {
                score++;
            }

            if (/[A-Z]/.test(value)) {
                score++;
            }

            if (/[0-9]/.test(value)) {
                score++;
            }

            if (/[@$!%*?&]/.test(value)) {
                score++;
            }


            if (score <= 2) {

                strengthText.textContent =
                    "Password strength: Weak";

                strengthBar.style.width =
                    "30%";

            }

            else if (score <= 4) {

                strengthText.textContent =
                    "Password strength: Medium";

                strengthBar.style.width =
                    "65%";

            }

            else {

                strengthText.textContent =
                    "Password strength: Strong";

                strengthBar.style.width =
                    "100%";

            }

        });

    }


    /* CONFIRM PASSWORD */

    if (confirmPassword) {

        confirmPassword.addEventListener("input", function () {

            const confirmError =
                document.getElementById(
                    "confirmPasswordError"
                );


            if (!confirmError) return;


            if (
                password &&
                confirmPassword.value !== password.value
            ) {

                confirmError.textContent =
                    "Passwords do not match.";

            }

            else {

                confirmError.textContent =
                    "";

            }

        });

    }


    /* RESET */

    if (resetButton) {

        resetButton.addEventListener("click", function () {

            clearRegistrationErrors();

            message.textContent =
                "";

            message.className =
                "registration-message";


            if (strengthText) {

                strengthText.textContent =
                    "Password strength: Not entered";

            }


            if (strengthBar) {

                strengthBar.style.width =
                    "0%";

            }

        });

    }

}


function clearRegistrationErrors() {

    document
        .querySelectorAll(".field-error")
        .forEach(function (error) {

            error.textContent = "";

        });

}


/* =========================
   EVENTS
========================= */

let events = [];
let filteredEvents = [];
let eventPage = 1;

const eventsPerPage = 6;


function setupEvents() {

    const search =
        document.getElementById("eventSearch");

    const filter =
        document.getElementById("eventFilter");

    const sort =
        document.getElementById("eventSort");


    if (search) {

        search.addEventListener("input", function () {

            eventPage = 1;

            displayEvents();

        });

    }


    if (filter) {

        filter.addEventListener("change", function () {

            eventPage = 1;

            displayEvents();

        });

    }


    if (sort) {

        sort.addEventListener("change", function () {

            eventPage = 1;

            displayEvents();

        });

    }

}


function loadEvents() {

    const container =
        document.getElementById("eventsContainer");

    if (!container) return;


    const result =
        document.getElementById("eventResultInfo");


    if (result) {

        result.textContent =
            "⏳ Loading events...";

    }


    fetch("json/events.json")

    .then(function (response) {

        if (!response.ok) {

            throw new Error(
                "events.json not found"
            );

        }

        return response.json();

    })

    .then(function (data) {

        events =
            Array.isArray(data)
                ? data
                : [];

        eventPage = 1;

        displayEvents();

    })

    .catch(function (error) {

        console.error(error);

        container.innerHTML = "";


        const errorBox =
            document.getElementById("eventError");


        if (errorBox) {

            errorBox.hidden = false;

            errorBox.textContent =
                "❌ Unable to load events.json";

        }


        if (result) {

            result.textContent =
                "Unable to load events.";

        }

    });

}


function displayEvents() {

    const container =
        document.getElementById("eventsContainer");

    if (!container) return;


    const searchInput =
        document.getElementById("eventSearch");

    const filterInput =
        document.getElementById("eventFilter");

    const sortInput =
        document.getElementById("eventSort");


    const searchText =
        searchInput
            ? searchInput.value
                .toLowerCase()
                .trim()
            : "";


    const filterValue =
        filterInput
            ? filterInput.value
            : "all";


    const sortValue =
        sortInput
            ? sortInput.value
            : "default";


    filteredEvents =
        events.filter(function (event) {

            const title =
                String(event.title || "")
                    .toLowerCase();

            const description =
                String(event.description || "")
                    .toLowerCase();

            const category =
                String(event.category || "");


            const searchMatch =
                title.includes(searchText) ||
                description.includes(searchText);


            const filterMatch =
                filterValue === "all" ||
                category === filterValue;


            return searchMatch && filterMatch;

        });


    if (sortValue === "title-asc") {

        filteredEvents.sort(function (a, b) {

            return String(a.title)
                .localeCompare(String(b.title));

        });

    }


    if (sortValue === "title-desc") {

        filteredEvents.sort(function (a, b) {

            return String(b.title)
                .localeCompare(String(a.title));

        });

    }


    if (sortValue === "date-asc") {

        filteredEvents.sort(function (a, b) {

            return new Date(a.date) -
                   new Date(b.date);

        });

    }


    if (sortValue === "date-desc") {

        filteredEvents.sort(function (a, b) {

            return new Date(b.date) -
                   new Date(a.date);

        });

    }


    const totalPages =
        Math.ceil(
            filteredEvents.length /
            eventsPerPage
        );


    if (
        eventPage > totalPages &&
        totalPages > 0
    ) {

        eventPage = totalPages;

    }


    const start =
        (eventPage - 1) *
        eventsPerPage;


    const pageEvents =
        filteredEvents.slice(
            start,
            start + eventsPerPage
        );


    container.innerHTML = "";


    if (pageEvents.length === 0) {

        container.innerHTML = `
            <div class="event-card">
                <h2>No Events Found</h2>
                <p>Try another search or filter.</p>
            </div>
        `;

        updateEventInfo();

        createEventPagination(totalPages);

        return;

    }


    pageEvents.forEach(function (event) {

        const card =
            document.createElement("div");

        card.className =
            "event-card";


        card.innerHTML = `
            <h2>${event.title || "Event"}</h2>

            <p>
                📅 <strong>Date:</strong>
                ${event.date || "N/A"}
            </p>

            <p>
                📂 <strong>Category:</strong>
                ${event.category || "N/A"}
            </p>

            <p>
                ${event.description || ""}
            </p>
        `;


        container.appendChild(card);

    });


    updateEventInfo();

    createEventPagination(totalPages);

}


function updateEventInfo() {

    const result =
        document.getElementById(
            "eventResultInfo"
        );

    if (!result) return;


    result.textContent =
        filteredEvents.length +
        " event(s) found - Page " +
        eventPage;

}


function createEventPagination(totalPages) {

    const pagination =
        document.getElementById(
            "eventPagination"
        );

    if (!pagination) return;


    pagination.innerHTML = "";


    if (totalPages <= 1) return;


    const previous =
        document.createElement("button");


    previous.textContent =
        "Previous";

    previous.disabled =
        eventPage === 1;


    previous.addEventListener(
        "click",
        function () {

            if (eventPage > 1) {

                eventPage--;

                displayEvents();

            }

        }
    );


    pagination.appendChild(previous);


    for (
        let i = 1;
        i <= totalPages;
        i++
    ) {

        const button =
            document.createElement("button");


        button.textContent =
            i;


        if (i === eventPage) {

            button.classList.add("active");

        }


        button.addEventListener(
            "click",
            function () {

                eventPage = i;

                displayEvents();

            }
        );


        pagination.appendChild(button);

    }


    const next =
        document.createElement("button");


    next.textContent =
        "Next";


    next.disabled =
        eventPage === totalPages;


    next.addEventListener(
        "click",
        function () {

            if (eventPage < totalPages) {

                eventPage++;

                displayEvents();

            }

        }
    );


    pagination.appendChild(next);

}


/* =========================
   FAQ
========================= */

let faqs = [];
let filteredFAQs = [];
let faqPage = 1;

const faqsPerPage = 5;


function setupFAQ() {

    const search =
        document.getElementById("faqSearch");

    if (!search) return;


    search.addEventListener(
        "input",
        function () {

            faqPage = 1;

            displayFAQs();

        }
    );

}


function loadFAQs() {

    const container =
        document.getElementById("faqContainer");

    if (!container) return;


    fetch("json/faqs.json")

    .then(function (response) {

        if (!response.ok) {

            throw new Error(
                "faqs.json not found"
            );

        }

        return response.json();

    })

    .then(function (data) {

        faqs =
            Array.isArray(data)
                ? data
                : [];

        faqPage = 1;


        const loading =
            document.getElementById(
                "faqLoading"
            );


        if (loading) {

            loading.style.display =
                "none";

        }


        displayFAQs();

    })

    .catch(function (error) {

        console.error(error);


        const loading =
            document.getElementById(
                "faqLoading"
            );


        if (loading) {

            loading.style.display =
                "none";

        }


        const errorBox =
            document.getElementById(
                "faqError"
            );


        if (errorBox) {

            errorBox.hidden = false;

            errorBox.textContent =
                "❌ Unable to load faqs.json";

        }

    });

}


function displayFAQs() {

    const container =
        document.getElementById(
            "faqContainer"
        );

    if (!container) return;


    const search =
        document.getElementById(
            "faqSearch"
        );


    const text =
        search
            ? search.value
                .toLowerCase()
                .trim()
            : "";


    filteredFAQs =
        faqs.filter(function (faq) {

            const question =
                String(faq.question || "")
                    .toLowerCase();

            const answer =
                String(faq.answer || "")
                    .toLowerCase();


            return (
                question.includes(text) ||
                answer.includes(text)
            );

        });


    const totalPages =
        Math.ceil(
            filteredFAQs.length /
            faqsPerPage
        );


    if (
        faqPage > totalPages &&
        totalPages > 0
    ) {

        faqPage = totalPages;

    }


    const start =
        (faqPage - 1) *
        faqsPerPage;


    const pageFAQs =
        filteredFAQs.slice(
            start,
            start + faqsPerPage
        );


    container.innerHTML = "";


    pageFAQs.forEach(function (faq) {

        const item =
            document.createElement("div");


        item.className =
            "faq-item";


        item.innerHTML = `
            <h3>
                ${faq.question || "Question"}
            </h3>

            <p>
                ${faq.answer ||
                  "No answer available."}
            </p>
        `;


        container.appendChild(item);

    });


    const result =
        document.getElementById(
            "faqResultInfo"
        );


    if (result) {

        result.textContent =
            filteredFAQs.length +
            " FAQ(s) found - Page " +
            faqPage;

    }

}

