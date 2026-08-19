const validUsername = "admin";
const validPassword = "password123";

document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const errorMsg = document.getElementById("errorMsg");

    if (username === validUsername && password === validPassword) {
        localStorage.setItem("isLoggedIn", "true");
        localStorage.setItem("studentName", username);
        window.location.href = "dashboard.html";
    } else {
        errorMsg.style.display = "block";
    }
});