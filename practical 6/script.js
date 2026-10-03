
const dataContainer = document.getElementById("dataContainer");
const searchInput = document.getElementById("search");
const filterSelect = document.getElementById("filter");
const sortSelect = document.getElementById("sort");

const loading = document.getElementById("loading");
const error = document.getElementById("error");
const recordCount = document.getElementById("recordCount");

const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const pageNumber = document.getElementById("pageNumber");

const tabs = document.querySelectorAll(".tab");

let allData = {
    events: [],
    students: [],
    faqs: []
};

let currentType = "events";
let currentPage = 1;

const recordsPerPage = 6;

let filteredData = [];


async function loadJSON(file) {

    const response = await fetch(file);

    if (!response.ok) {
        throw new Error("Unable to load " + file);
    }

    return await response.json();
}


async function loadAllData() {

    try {

        loading.style.display = "block";
        error.style.display = "none";

        allData.events = await loadJSON("events.json");
        allData.students = await loadJSON("students.json");
        allData.faqs = await loadJSON("faqs.json");

        loading.style.display = "none";

        updateFilter();
        renderData();

    } catch (err) {

        loading.style.display = "none";
        error.style.display = "block";

        error.textContent =
            "Error: JSON data could not be loaded. Please check the JSON files and run using Live Server.";
    }
}


function getCurrentData() {

    return allData[currentType];

}


function updateFilter() {

    filterSelect.innerHTML =
        '<option value="All">All Categories</option>';

    const data = getCurrentData();

    let categories = [];

    if (currentType === "events") {

        categories = [
            ...new Set(data.map(item => item.category))
        ];

    }

    if (currentType === "students") {

        categories = [
            ...new Set(data.map(item => item.course))
        ];

    }

    if (currentType === "faqs") {

        categories = [
            ...new Set(data.map(item => item.category))
        ];

    }

    categories.forEach(category => {

        const option = document.createElement("option");

        option.value = category;
        option.textContent = category;

        filterSelect.appendChild(option);

    });

}


function renderData() {

    let data = [...getCurrentData()];

    const searchValue =
        searchInput.value.toLowerCase().trim();

    const filterValue =
        filterSelect.value;

    const sortValue =
        sortSelect.value;


    if (searchValue !== "") {

        data = data.filter(item => {

            return JSON.stringify(item)
                .toLowerCase()
                .includes(searchValue);

        });

    }


    if (filterValue !== "All") {

        if (currentType === "students") {

            data = data.filter(
                item => item.course === filterValue
            );

        } else {

            data = data.filter(
                item => item.category === filterValue
            );

        }

    }


    if (sortValue === "az") {

        data.sort((a, b) =>
            getTitle(a).localeCompare(getTitle(b))
        );

    }


    if (sortValue === "za") {

        data.sort((a, b) =>
            getTitle(b).localeCompare(getTitle(a))
        );

    }


    filteredData = data;

    const totalPages =
        Math.ceil(filteredData.length / recordsPerPage);


    if (currentPage > totalPages && totalPages > 0) {

        currentPage = totalPages;

    }


    const startIndex =
        (currentPage - 1) * recordsPerPage;


    const pageData =
        filteredData.slice(
            startIndex,
            startIndex + recordsPerPage
        );


    displayData(pageData);

    updatePagination(totalPages);


    recordCount.textContent =
        "Showing " + filteredData.length + " records";

}


function getTitle(item) {

    if (currentType === "events") {

        return item.title;

    }

    if (currentType === "students") {

        return item.name;

    }

    if (currentType === "faqs") {

        return item.question;

    }

    return "";

}


function displayData(data) {

    dataContainer.innerHTML = "";


    if (data.length === 0) {

        dataContainer.innerHTML = `
            <div class="card">
                <h3>No Records Found</h3>
                <p>Try another search or filter.</p>
            </div>
        `;

        return;

    }


    data.forEach(item => {

        const card =
            document.createElement("div");

        card.className = "card";


        if (currentType === "events") {

            card.innerHTML = `
                <h3>${item.title}</h3>

                <p>
                    <strong>Date:</strong>
                    ${item.date}
                </p>

                <p>
                    <strong>Time:</strong>
                    ${item.time}
                </p>

                <p>
                    <strong>Venue:</strong>
                    ${item.venue}
                </p>

                <p>
                    <strong>Category:</strong>
                    ${item.category}
                </p>

                <p>
                    ${item.description}
                </p>
            `;

        }


        if (currentType === "students") {

            card.innerHTML = `
                <h3>${item.name}</h3>

                <p>
                    <strong>Email:</strong>
                    ${item.email}
                </p>

                <p>
                    <strong>Course:</strong>
                    ${item.course}
                </p>

                <p>
                    <strong>Year:</strong>
                    ${item.year}
                </p>

                <p>
                    <strong>City:</strong>
                    ${item.city}
                </p>
            `;

        }


        if (currentType === "faqs") {

            card.innerHTML = `
                <h3>FAQ ${item.id}</h3>

                <p>
                    <strong>Question:</strong>
                    ${item.question}
                </p>

                <p>
                    <strong>Answer:</strong>
                    ${item.answer}
                </p>

                <p>
                    <strong>Category:</strong>
                    ${item.category}
                </p>
            `;

        }


        dataContainer.appendChild(card);

    });

}


function updatePagination(totalPages) {

    pageNumber.textContent =
        "Page " + currentPage +
        " of " + Math.max(totalPages, 1);


    prevBtn.disabled =
        currentPage === 1;


    nextBtn.disabled =
        currentPage >= totalPages ||
        totalPages === 0;

}


searchInput.addEventListener("input", () => {

    currentPage = 1;

    renderData();

});


filterSelect.addEventListener("change", () => {

    currentPage = 1;

    renderData();

});


sortSelect.addEventListener("change", () => {

    currentPage = 1;

    renderData();

});


prevBtn.addEventListener("click", () => {

    if (currentPage > 1) {

        currentPage--;

        renderData();

    }

});


nextBtn.addEventListener("click", () => {

    const totalPages =
        Math.ceil(
            filteredData.length / recordsPerPage
        );


    if (currentPage < totalPages) {

        currentPage++;

        renderData();

    }

});


tabs.forEach(tab => {

    tab.addEventListener("click", () => {

        tabs.forEach(item => {

            item.classList.remove("active");

        });


        tab.classList.add("active");


        currentType =
            tab.dataset.type;


        currentPage = 1;

        searchInput.value = "";

        sortSelect.value = "default";


        updateFilter();

        renderData();

    });

});


loadAllData();

