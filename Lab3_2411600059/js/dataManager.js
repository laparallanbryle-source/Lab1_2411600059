let allCourses = [];
let currentFilters = {
    category: "all",
    status: "all",
    search: "",
    minUnits: null,
    maxUnits: null
};
async function initializeData() {
    try {
        const response = await fetch("courses.json");
        const data = await response.json();
        allCourses = data;
        return allCourses;
    } catch (error) {
        console.error("Error loading courses:", error);
        return [];
    }
}
function getProducts() {
    return allCourses;
}
function getProductById(id) {
    return allCourses.find(course => course.id === id);
}
function getProductsByCategory(category) {
    return allCourses.filter(course => course.category === category);
}
function getLowStockProducts() {
    return allCourses.filter(course => course.status === "Failed" || (course.grade >= 2.75 && course.grade <= 3.00));
}
function getStockStatistics() {
    const totalUnits = allCourses.reduce((sum, c) => sum + c.units, 0);
    const passedCount = allCourses.filter(c => c.status === "Passed").length;
    const failedCount = allCourses.filter(c => c.status === "Failed").length;
    const inProgressCount = allCourses.filter(c => c.status === "In Progress").length;

    return {
        totalCourses: allCourses.length,
        totalUnits: totalUnits,
        passedCount: passedCount,
        failedCount: failedCount,
        inProgressCount: inProgressCount
    };
}
function getCategorySummary() {
    const categories = {};
    allCourses.forEach(course => {
        if (!categories[course.category]) {
            categories[course.category] = { totalUnits: 0, count: 0 };
        }
        categories[course.category].totalUnits += course.units;
        categories[course.category].count += 1;
    });
    return categories;
}

function getTopCoursesByUnits(limit = 5) {
    return [...allCourses].sort((a, b) => b.units - a.units).slice(0, limit);
}
function filterByCategory(category) {
    currentFilters.category = category;
    return applyFilters();
}
function filterByStockStatus(status) {
    currentFilters.status = status;
    return applyFilters();
}
function filterByPriceRange(min, max) {
    currentFilters.minUnits = min === "" || min === null ? null : Number(min);
    currentFilters.maxUnits = max === "" || max === null ? null : Number(max);
    return applyFilters();
}
function applyFilters() {
    return allCourses.filter(course => {
        const matchesCategory = currentFilters.category === "all" || course.category === currentFilters.category;
        const matchesStatus = currentFilters.status === "all" || course.status === currentFilters.status;
        const matchesSearch = currentFilters.search === "" || course.name.toLowerCase().includes(currentFilters.search.toLowerCase());
        const matchesMin = currentFilters.minUnits === null || course.units >= currentFilters.minUnits;
        const matchesMax = currentFilters.maxUnits === null || course.units <= currentFilters.maxUnits;
        return matchesCategory && matchesStatus && matchesSearch && matchesMin && matchesMax;
    });
}
function searchProducts(query) {
    currentFilters.search = query;
    return applyFilters();
}
function exportToCSV(data) {
    const headers = ["ID", "Course Name", "Category", "Grade", "Units", "Status"];
    const rows = data.map(c => [c.id, c.name, c.category, c.grade, c.units, c.status]);

    let csvContent = headers.join(",") + "\n";
    rows.forEach(row => {
        csvContent += row.join(",") + "\n";
    });

    return csvContent;
}
function downloadCSV(csvContent, filename) {
    const blob = new Blob([csvContent], { type: "text/csv" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}