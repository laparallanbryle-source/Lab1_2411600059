if (localStorage.getItem("isLoggedIn") !== "true") {
    window.location.href = "index.html";
}
const studentName = localStorage.getItem("studentName");
const currentHour = new Date().getHours();
let greeting;
if (currentHour < 12) {
    greeting = "Good morning";
} else if (currentHour < 18) {
    greeting = "Good afternoon";
} else {
    greeting = "Good evening";
}
document.getElementById("welcomeMsg").textContent = greeting + ", " + studentName + "!";

document.getElementById("logoutBtn").addEventListener("click", function () {
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("studentName");
    window.location.href = "index.html";
});
let categoryChartInstance = null;
let statusChartInstance = null;
let topCoursesChartInstance = null;
function highlightMatch(text, query) {
    if (!query) return text;
    const regex = new RegExp(`(${query})`, "gi");
    return text.replace(regex, "<mark>$1</mark>");
}
function renderTable(courses) {
    const tbody = document.getElementById("gradesTableBody");
    tbody.innerHTML = "";

    const searchQuery = currentFilters.search;

    courses.forEach(course => {
        const row = document.createElement("tr");

        const isAtRisk = course.status === "Failed" || (course.grade >= 2.75 && course.grade <= 3.00);
        if (isAtRisk) {
            row.classList.add("table-warning");
        }

        let badgeClass = "bg-secondary";
        if (course.status === "Passed") badgeClass = "bg-success";
        if (course.status === "Failed") badgeClass = "bg-danger";
        if (course.status === "In Progress") badgeClass = "bg-info";

        const highlightedName = highlightMatch(course.name, searchQuery);

        row.innerHTML = `
            <td>${highlightedName}</td>
            <td>${course.category}</td>
            <td>${course.grade > 0 ? course.grade.toFixed(2) : "—"}</td>
            <td>${course.units}</td>
            <td><span class="badge ${badgeClass}">${course.status}</span></td>
        `;

        tbody.appendChild(row);
    });
}
function renderAlerts() {
    const atRisk = getLowStockProducts();
    const alertSection = document.getElementById("alertSection");

    if (atRisk.length > 0) {
        alertSection.innerHTML = `
            <div class="alert alert-warning">
                <strong>⚠ At-Risk Courses:</strong> May ${atRisk.length} subject(s) na malapit nang bumagsak o bumagsak na. I-check ang table sa ibaba (naka-highlight sa dilaw).
            </div>
        `;
    } else {
        alertSection.innerHTML = "";
    }
}

function showToast(message) {
    document.getElementById("toastMessage").textContent = message;
    const toastEl = document.getElementById("updateToast");
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}


function renderCharts() {
    const categorySummary = getCategorySummary();
    const categories = Object.keys(categorySummary);
    const unitsPerCategory = categories.map(cat => categorySummary[cat].totalUnits);

    const categoryCtx = document.getElementById("categoryChart");
    if (categoryChartInstance) categoryChartInstance.destroy();
    categoryChartInstance = new Chart(categoryCtx, {
        type: "bar",
        data: {
            labels: categories,
            datasets: [{
                label: "Total Units",
                data: unitsPerCategory,
                backgroundColor: ["#6D57D6", "#586BD6", "#A372D6", "#C161D6"]
            }]
        },
        options: { responsive: true }
    });

    const stats = getStockStatistics();
    const statusCtx = document.getElementById("statusChart");
    if (statusChartInstance) statusChartInstance.destroy();
    statusChartInstance = new Chart(statusCtx, {
        type: "doughnut",
        data: {
            labels: ["Passed", "Failed", "In Progress"],
            datasets: [{
                data: [stats.passedCount, stats.failedCount, stats.inProgressCount],
                backgroundColor: ["#28a745", "#dc3545", "#0dcaf0"]
            }]
        },
        options: { responsive: true }
    });

    const topCourses = getTopCoursesByUnits(5);
    const topCtx = document.getElementById("topCoursesChart");
    if (topCoursesChartInstance) topCoursesChartInstance.destroy();
    topCoursesChartInstance = new Chart(topCtx, {
        type: "bar",
        data: {
            labels: topCourses.map(c => c.name),
            datasets: [{
                label: "Units",
                data: topCourses.map(c => c.units),
                backgroundColor: "#A372D6"
            }]
        },
        options: {
            indexAxis: "y",
            responsive: true
        }
    });
}

function refreshDashboard() {
    const filtered = applyFilters();
    renderTable(filtered);
    renderAlerts();
    renderCharts();
}

document.getElementById("searchInput").addEventListener("input", function (e) {
    searchProducts(e.target.value);
    renderTable(applyFilters());
});

document.getElementById("categoryFilter").addEventListener("change", function (e) {
    filterByCategory(e.target.value);
    renderTable(applyFilters());
});

document.getElementById("statusFilter").addEventListener("change", function (e) {
    filterByStockStatus(e.target.value);
    renderTable(applyFilters());
});

document.getElementById("minUnits").addEventListener("input", function () {
    const min = document.getElementById("minUnits").value;
    const max = document.getElementById("maxUnits").value;
    filterByPriceRange(min, max);
    renderTable(applyFilters());
});

document.getElementById("maxUnits").addEventListener("input", function () {
    const min = document.getElementById("minUnits").value;
    const max = document.getElementById("maxUnits").value;
    filterByPriceRange(min, max);
    renderTable(applyFilters());
});

document.getElementById("exportBtn").addEventListener("click", function () {
    const csvContent = exportToCSV(applyFilters());
    downloadCSV(csvContent, "grades_export.csv");
});


setInterval(function () {
    const courses = getProducts();
    const randomCourse = courses[Math.floor(Math.random() * courses.length)];

    if (randomCourse.status === "In Progress") {
        randomCourse.grade = (Math.random() * 2 + 1).toFixed(2) * 1;
        randomCourse.status = randomCourse.grade <= 3.00 ? "Passed" : "Failed";
        showToast(`${randomCourse.name} is now ${randomCourse.status}!`);
        refreshDashboard();
    }
}, 15000);


async function init() {
    await initializeData();
    refreshDashboard();
}

init();