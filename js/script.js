document.addEventListener("DOMContentLoaded", function () {
    setupTheme();
    setupMenu();
    setupModal();
    setupActiveSidebar();
    setupEvents();
    setupFAQ();
    setupApiStudents();
<<<<<<< HEAD

    loadDashboardStats();

    if (document.getElementById("dashboardNotices")) {
        loadDashboardNotices();
    }
=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776

    if (document.getElementById("eventsContainer")) {
        loadEvents();
    }

<<<<<<< HEAD
    if (document.getElementById("dashboardEvents")) {
        loadDashboardEvents();
    }

    if (document.getElementById("faqContainer")) {
        loadFAQs();
    }
=======
    if (document.getElementById("faqContainer")) {
        loadFAQs();
    }
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
});

function setupTheme() {
    const button = document.getElementById("themeBtn");
<<<<<<< HEAD
    const savedTheme = localStorage.getItem("studenthub-theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark");
    }

    function updateButton() {
        if (!button) return;

        button.textContent = document.body.classList.contains("dark")
            ? "☀️ Light"
            : "🌙 Theme";
    }

    updateButton();

=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
    if (!button) return;

    button.addEventListener("click", function () {
        document.body.classList.toggle("dark");
<<<<<<< HEAD

        const isDark = document.body.classList.contains("dark");
        localStorage.setItem("studenthub-theme", isDark ? "dark" : "light");

        updateButton();
=======
        button.textContent = document.body.classList.contains("dark")
            ? "☀️"
            : "🌙 Theme";
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
    });
}

function setupMenu() {
    const button = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");
    if (!button || !sidebar) return;

    button.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        sidebar.classList.toggle("open");

        const isOpen =
            sidebar.classList.contains("active") ||
            sidebar.classList.contains("open");

        button.setAttribute("aria-expanded", String(isOpen));
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

    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.hidden = true;
        }
    });
}

function setupActiveSidebar() {
    const currentPage = window.location.pathname
        .split("/")
        .pop()
        .toLowerCase();

    document.querySelectorAll(".sidebar a").forEach(function (link) {
        const href = link.getAttribute("href");
        if (!href) return;

        const linkPage = href.split("#")[0].split("/").pop().toLowerCase();

        if (linkPage && linkPage === currentPage) {
            link.classList.add("active");
        }
    });
}

<<<<<<< HEAD
/* Dashboard: live statistics from the database */
async function loadDashboardStats() {
    const elements = {
        users: document.getElementById("statUsers"),
        students: document.getElementById("statStudents"),
        events: document.getElementById("statEvents"),
        registrations: document.getElementById("statRegistrations")
    };

    // Other pages do not contain these statistics.
    if (!elements.users) return;

    try {
        const response = await fetch("dashboard_stats.php", {
            headers: { Accept: "application/json" }
        });

        const result = await response.json();

        if (!response.ok || result.success !== true) {
            throw new Error(result.message || "Could not load statistics.");
        }

        elements.users.textContent = result.data.users ?? 0;
        elements.students.textContent = result.data.activeStudents ?? 0;
        elements.events.textContent = result.data.upcomingEvents ?? 0;
        elements.registrations.textContent = result.data.registrations ?? 0;
    } catch (error) {
        console.error("Dashboard statistics error:", error);

        Object.values(elements).forEach(function (element) {
            if (element) element.textContent = "—";
        });
    }
}

/* Dashboard: notices from the database */
async function loadDashboardNotices() {
    const container = document.getElementById("dashboardNotices");
    if (!container) return;

    container.textContent = "Loading notices...";

    try {
        const response = await fetch("notices_list.php", {
            headers: { Accept: "application/json" }
        });

        const result = await response.json();

        if (!response.ok || result.success !== true) {
            throw new Error(result.message || "Could not load notices.");
        }

        const notices = Array.isArray(result.data) ? result.data : [];
        container.replaceChildren();

        if (notices.length === 0) {
            container.textContent = "No recent notices are available.";
            return;
        }

        notices.forEach(function (notice) {
            const item = document.createElement("div");
            item.className = "notice-item";

            const icon = document.createElement("span");
            icon.textContent = "📌";
            icon.setAttribute("aria-hidden", "true");

            const content = document.createElement("div");

            const title = document.createElement("strong");
            title.textContent = notice.title || "Notice";

            const message = document.createElement("p");
            message.textContent = notice.message || "";

            content.append(title, message);
            item.append(icon, content);
            container.appendChild(item);
        });
    } catch (error) {
        console.error("Dashboard notices error:", error);
        container.textContent = "Notices are temporarily unavailable.";
    }
}

/* Dashboard: upcoming events from the database */
async function loadDashboardEvents() {
    const box = document.getElementById("dashboardEvents");
    if (!box) return;

    box.textContent = "Loading events...";

    try {
        const response = await fetch("upcoming_events.php", {
            headers: { Accept: "application/json" }
        });

        const result = await response.json();

        if (!response.ok || result.success !== true) {
            throw new Error(result.message || "Events could not be loaded.");
        }

        const eventList = Array.isArray(result.data) ? result.data : [];
        box.replaceChildren();

        if (eventList.length === 0) {
            box.textContent = "No upcoming events are currently available.";
            return;
        }

        eventList.forEach(function (event) {
            const item = document.createElement("div");
            item.className = "notice-item";

            const icon = document.createElement("span");
            icon.textContent = "📌";
            icon.setAttribute("aria-hidden", "true");

            const text = document.createElement("p");
            text.textContent =
                (event.title || "Event") +
                " — " +
                (event.date || "Date not set") +
                (event.venue ? " — " + event.venue : "");

            item.append(icon, text);
            box.appendChild(item);
        });
    } catch (error) {
        console.error("Dashboard events error:", error);
        box.textContent = "Events are temporarily unavailable.";
    }
}

/* Events page: search, filter, sort and pagination */
=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
let events = [];
let filteredEvents = [];
let eventPage = 1;
const eventsPerPage = 6;

function setupEvents() {
    const search = document.getElementById("eventSearch");
    const filter = document.getElementById("eventFilter");
    const sort = document.getElementById("eventSort");

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

async function loadEvents() {
    const container = document.getElementById("eventsContainer");
    if (!container) return;

    const result = document.getElementById("eventResultInfo");
    const errorBox = document.getElementById("eventError");

    if (result) result.textContent = "Loading events...";
    if (errorBox) errorBox.hidden = true;

    try {
<<<<<<< HEAD
        const response = await fetch("events_list.php", {
            headers: { Accept: "application/json" }
        });

        const payload = await response.json();

        if (!response.ok || payload.success !== true) {
            throw new Error(payload.message || "Could not load events.");
        }

        events = Array.isArray(payload.data) ? payload.data : [];
        eventPage = 1;
        displayEvents();
    } catch (error) {
        console.error("Events loading error:", error);
=======
        const response = await fetch("json/events.json");

        if (!response.ok) {
            throw new Error("Could not load events.");
        }

        const data = await response.json();
        events = Array.isArray(data) ? data : [];
        eventPage = 1;
        displayEvents();
    } catch (error) {
        console.error(error);
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
        container.replaceChildren();

        if (errorBox) {
            errorBox.hidden = false;
            errorBox.textContent = error.message;
        }

        if (result) result.textContent = "Unable to load events.";
    }
}

function displayEvents() {
    const container = document.getElementById("eventsContainer");
    if (!container) return;

    const searchText = (
        document.getElementById("eventSearch")?.value || ""
    ).toLowerCase().trim();

    const filterValue =
        document.getElementById("eventFilter")?.value || "all";

    const sortValue =
        document.getElementById("eventSort")?.value || "default";

    filteredEvents = events.filter(function (event) {
        const title = String(event.title || "").toLowerCase();
        const description = String(event.description || "").toLowerCase();
        const category = String(event.category || "");

        const matchesSearch =
            title.includes(searchText) ||
            description.includes(searchText);

        const matchesFilter =
            filterValue === "all" || category === filterValue;
<<<<<<< HEAD

        return matchesSearch && matchesFilter;
    });

    if (sortValue === "title-asc") {
        filteredEvents.sort((a, b) =>
            String(a.title || "").localeCompare(String(b.title || ""))
        );
    } else if (sortValue === "title-desc") {
        filteredEvents.sort((a, b) =>
            String(b.title || "").localeCompare(String(a.title || ""))
        );
    } else if (sortValue === "date-asc") {
        filteredEvents.sort(
            (a, b) => new Date(a.date) - new Date(b.date)
        );
    } else if (sortValue === "date-desc") {
        filteredEvents.sort(
            (a, b) => new Date(b.date) - new Date(a.date)
        );
    }

    const totalPages = Math.ceil(filteredEvents.length / eventsPerPage);

    if (totalPages > 0 && eventPage > totalPages) {
        eventPage = totalPages;
    }

    const start = (eventPage - 1) * eventsPerPage;
    const pageEvents = filteredEvents.slice(start, start + eventsPerPage);

    container.replaceChildren();

    if (pageEvents.length === 0) {
        const card = document.createElement("div");
        card.className = "event-card";

        const heading = document.createElement("h2");
        heading.textContent = "No Events Found";

        const paragraph = document.createElement("p");
        paragraph.textContent = "Try another search or filter.";

        card.append(heading, paragraph);
        container.appendChild(card);
    } else {
        pageEvents.forEach(function (event) {
            const card = document.createElement("div");
            card.className = "event-card";

            if (event.poster_path) {
                const poster = document.createElement("img");
                poster.src = event.poster_path;
                poster.alt = (event.title || "Event") + " poster";
                poster.loading = "lazy";
                card.appendChild(poster);
            }

            const heading = document.createElement("h2");
            heading.textContent = event.title || "Event";

            const date = document.createElement("p");
            date.textContent = "📅 Date: " + (event.date || "N/A");

            const category = document.createElement("p");
            category.textContent =
                "📂 Category: " + (event.category || "N/A");

            const venue = document.createElement("p");
            venue.textContent = "📍 Venue: " + (event.venue || "N/A");

            const description = document.createElement("p");
            description.textContent = event.description || "";

            card.append(heading, date, category, venue, description);
            container.appendChild(card);
        });
    }

    updateEventInfo();
    createEventPagination(totalPages);
}

function updateEventInfo() {
    const result = document.getElementById("eventResultInfo");
    if (!result) return;

    result.textContent =
        filteredEvents.length + " event(s) found - Page " + eventPage;
}

function createEventPagination(totalPages) {
    const pagination = document.getElementById("eventPagination");
    if (!pagination) return;

    pagination.replaceChildren();
    if (totalPages <= 1) return;

=======

        return matchesSearch && matchesFilter;
    });

    if (sortValue === "title-asc") {
        filteredEvents.sort((a, b) =>
            String(a.title || "").localeCompare(String(b.title || ""))
        );
    } else if (sortValue === "title-desc") {
        filteredEvents.sort((a, b) =>
            String(b.title || "").localeCompare(String(a.title || ""))
        );
    } else if (sortValue === "date-asc") {
        filteredEvents.sort(
            (a, b) => new Date(a.date) - new Date(b.date)
        );
    } else if (sortValue === "date-desc") {
        filteredEvents.sort(
            (a, b) => new Date(b.date) - new Date(a.date)
        );
    }

    const totalPages = Math.ceil(filteredEvents.length / eventsPerPage);

    if (totalPages > 0 && eventPage > totalPages) {
        eventPage = totalPages;
    }

    const start = (eventPage - 1) * eventsPerPage;
    const pageEvents = filteredEvents.slice(start, start + eventsPerPage);

    container.replaceChildren();

    if (pageEvents.length === 0) {
        const card = document.createElement("div");
        card.className = "event-card";

        const heading = document.createElement("h2");
        heading.textContent = "No Events Found";

        const paragraph = document.createElement("p");
        paragraph.textContent = "Try another search or filter.";

        card.append(heading, paragraph);
        container.appendChild(card);
    } else {
        pageEvents.forEach(function (event) {
            const card = document.createElement("div");
            card.className = "event-card";

            const heading = document.createElement("h2");
            heading.textContent = event.title || "Event";

            const date = document.createElement("p");
            date.textContent = "📅 Date: " + (event.date || "N/A");

            const category = document.createElement("p");
            category.textContent =
                "📂 Category: " + (event.category || "N/A");

            const description = document.createElement("p");
            description.textContent = event.description || "";

            card.append(heading, date, category, description);
            container.appendChild(card);
        });
    }

    updateEventInfo();
    createEventPagination(totalPages);
}

function updateEventInfo() {
    const result = document.getElementById("eventResultInfo");
    if (!result) return;

    result.textContent =
        filteredEvents.length + " event(s) found - Page " + eventPage;
}

function createEventPagination(totalPages) {
    const pagination = document.getElementById("eventPagination");
    if (!pagination) return;

    pagination.replaceChildren();
    if (totalPages <= 1) return;

>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
    const previous = document.createElement("button");
    previous.type = "button";
    previous.textContent = "Previous";
    previous.disabled = eventPage === 1;
<<<<<<< HEAD

=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
    previous.addEventListener("click", function () {
        if (eventPage > 1) {
            eventPage--;
            displayEvents();
        }
    });
<<<<<<< HEAD

=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
    pagination.appendChild(previous);

    for (let page = 1; page <= totalPages; page++) {
        const button = document.createElement("button");
        button.type = "button";
        button.textContent = String(page);

        if (page === eventPage) {
            button.classList.add("active");
        }

        button.addEventListener("click", function () {
            eventPage = page;
            displayEvents();
        });

        pagination.appendChild(button);
    }

    const next = document.createElement("button");
    next.type = "button";
    next.textContent = "Next";
    next.disabled = eventPage === totalPages;
<<<<<<< HEAD

=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
    next.addEventListener("click", function () {
        if (eventPage < totalPages) {
            eventPage++;
            displayEvents();
        }
    });
<<<<<<< HEAD

    pagination.appendChild(next);
}

/* FAQ page */
=======
    pagination.appendChild(next);
}

>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
let faqs = [];

function setupFAQ() {
    const search = document.getElementById("faqSearch");

    if (search) {
        search.addEventListener("input", displayFAQs);
    }
}

async function loadFAQs() {
    const container = document.getElementById("faqContainer");
    if (!container) return;

    const loading = document.getElementById("faqLoading");
    const errorBox = document.getElementById("faqError");

    try {
        const response = await fetch("json/faqs.json");

        if (!response.ok) {
            throw new Error("Could not load FAQs.");
        }

        const data = await response.json();
        faqs = Array.isArray(data) ? data : [];

        if (loading) loading.style.display = "none";
        if (errorBox) errorBox.hidden = true;

        displayFAQs();
    } catch (error) {
        console.error(error);

        if (loading) loading.style.display = "none";

        if (errorBox) {
            errorBox.hidden = false;
            errorBox.textContent = error.message;
        }
    }
}

function displayFAQs() {
    const container = document.getElementById("faqContainer");
    if (!container) return;

    const searchText = (
        document.getElementById("faqSearch")?.value || ""
    ).toLowerCase().trim();

    const matches = faqs.filter(function (faq) {
        return (
            String(faq.question || "").toLowerCase().includes(searchText) ||
            String(faq.answer || "").toLowerCase().includes(searchText)
        );
    });

    container.replaceChildren();

    if (matches.length === 0) {
        const empty = document.createElement("p");
        empty.textContent = "No FAQs found.";
        container.appendChild(empty);
    } else {
        matches.forEach(function (faq) {
            const item = document.createElement("div");
            item.className = "faq-item";

            const question = document.createElement("h3");
            question.textContent = faq.question || "Question";

            const answer = document.createElement("p");
            answer.textContent = faq.answer || "No answer available.";

            item.append(question, answer);
            container.appendChild(item);
        });
    }

    const result = document.getElementById("faqResultInfo");
    if (result) {
        result.textContent = matches.length + " FAQ(s) found";
    }
}

<<<<<<< HEAD
/* Student API page */
=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
function setupApiStudents() {
    const form = document.getElementById("apiStudentForm");
    if (!form) return;

    const idInput = document.getElementById("apiStudentId");
    const nameInput = document.getElementById("apiStudentName");
    const emailInput = document.getElementById("apiStudentEmail");
    const courseInput = document.getElementById("apiStudentCourse");
    const saveButton = document.getElementById("apiStudentSave");
    const cancelButton = document.getElementById("apiStudentCancel");
    const searchInput = document.getElementById("apiStudentSearch");
    const courseFilter =
        document.getElementById("apiStudentCourseFilter");
    const rows = document.getElementById("apiStudentRows");
    const loading = document.getElementById("apiStudentLoading");
    const errorBox = document.getElementById("apiStudentError");
    const message = document.getElementById("apiStudentMessage");

    let students = [];
    let searchTimer;

    function showMessage(text, type) {
        message.textContent = text;
        message.className = type;
    }

    async function sendRequest(url, options = {}) {
        const headers = {
            Accept: "application/json",
            ...(options.body
                ? { "Content-Type": "application/json" }
                : {}),
            ...(options.headers || {})
        };

        const response = await fetch(url, {
            ...options,
            headers
        });

        let result;

        try {
            result = await response.json();
        } catch {
            throw new Error("Server response was not valid JSON.");
        }

        if (!response.ok || result.success !== true) {
            throw new Error(result.message || "The request failed.");
        }

        return result;
    }

    function makeCell(value) {
        const cell = document.createElement("td");
        cell.textContent = value ?? "";
        return cell;
    }

    function renderStudents(list) {
        rows.replaceChildren();

        if (list.length === 0) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");
            cell.colSpan = 5;
            cell.textContent = "No active student records found.";
            row.appendChild(cell);
            rows.appendChild(row);
            return;
        }

        list.forEach(function (student) {
            const row = document.createElement("tr");

            row.appendChild(makeCell(student.id));
            row.appendChild(makeCell(student.name));
            row.appendChild(makeCell(student.email));
            row.appendChild(makeCell(student.course));

            const actionsCell = document.createElement("td");

            const editButton = document.createElement("button");
            editButton.type = "button";
            editButton.textContent = "Edit";
            editButton.dataset.action = "edit";
            editButton.dataset.id = student.id;

            const deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.textContent = "Delete";
            deleteButton.dataset.action = "delete";
            deleteButton.dataset.id = student.id;

            actionsCell.append(editButton, " ", deleteButton);
            row.appendChild(actionsCell);
            rows.appendChild(row);
        });
    }

    async function loadStudents() {
        loading.hidden = false;
        errorBox.hidden = true;

        const params = new URLSearchParams();

        if (searchInput.value.trim()) {
            params.set("search", searchInput.value.trim());
        }

        if (courseFilter.value) {
            params.set("course", courseFilter.value);
        }

        const query = params.toString();
        const url = query ? "api.php?" + query : "api.php";

        try {
            const result = await sendRequest(url);
            students = result.data || [];
            renderStudents(students);
        } catch (error) {
            errorBox.textContent = error.message;
            errorBox.hidden = false;
        } finally {
            loading.hidden = true;
        }
    }

    function resetStudentForm() {
        form.reset();
        idInput.value = "";
        saveButton.textContent = "Add Student";
        cancelButton.hidden = true;
    }

    form.addEventListener("submit", async function (event) {
        event.preventDefault();
        showMessage("", "");

        if (!form.reportValidity()) return;

        const id = idInput.value;
        const url = id
            ? "api.php?id=" + encodeURIComponent(id)
            : "api.php";

        saveButton.disabled = true;

        try {
            const result = await sendRequest(url, {
                method: id ? "PUT" : "POST",
                body: JSON.stringify({
                    name: nameInput.value.trim(),
                    email: emailInput.value.trim(),
                    course: courseInput.value
                })
            });

            showMessage(result.message, "success");
            resetStudentForm();
            await loadStudents();
        } catch (error) {
            showMessage(error.message, "error");
        } finally {
            saveButton.disabled = false;
        }
    });

    rows.addEventListener("click", async function (event) {
        const button = event.target.closest("button[data-action]");
        if (!button) return;

        const student = students.find(function (item) {
            return String(item.id) === button.dataset.id;
        });

        if (!student) return;

        if (button.dataset.action === "edit") {
            idInput.value = student.id;
            nameInput.value = student.name;
            emailInput.value = student.email;
            courseInput.value = student.course;
            saveButton.textContent = "Update Student";
            cancelButton.hidden = false;
<<<<<<< HEAD

=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
            form.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
<<<<<<< HEAD

=======
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
            return;
        }

        if (
            button.dataset.action === "delete" &&
            window.confirm("Mark this student as inactive?")
        ) {
            try {
                const result = await sendRequest(
                    "api.php?id=" + encodeURIComponent(student.id),
                    { method: "DELETE" }
                );

                showMessage(result.message, "success");
                await loadStudents();
            } catch (error) {
                showMessage(error.message, "error");
            }
        }
    });

    cancelButton.addEventListener("click", resetStudentForm);

    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadStudents, 300);
    });

    courseFilter.addEventListener("change", loadStudents);

    loadStudents();
<<<<<<< HEAD
}
=======
}
>>>>>>> 2cde1d43867d7cccb15cbea43a9d7b6174545776
