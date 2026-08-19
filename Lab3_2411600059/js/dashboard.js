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

const ctx = document.getElementById("gradesChart");

new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["Web Systems", "Databases", "Sys. Analysis", "Sys. Integration"],
        datasets: [{
            label: "Grade",
            data: [1.25, 1.50, 2.00, 3.00],
            backgroundColor: ["#6D57D6", "#586BD6", "#A372D6", "#C161D6"]
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                reverse: true,
                max: 5
            }
        }
    }
});