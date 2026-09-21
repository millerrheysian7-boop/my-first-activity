const DEMO_USERNAME = "admin";
const DEMO_PASSWORD = "password123";

const loginBtn = document.getElementById("loginBtn");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");
const loginAlert = document.getElementById("loginAlert");

if (localStorage.getItem("fittrack_loggedIn") === "true") {
  window.location.href = "dashboard.html";
}

function showError(message) {
  loginAlert.textContent = message;
  loginAlert.classList.remove("d-none");
}

function hideError() {
  loginAlert.classList.add("d-none");
}

function checkLogin() {
  const username = usernameInput.value.trim();
  const password = passwordInput.value.trim();

  if (username === "" || password === "") {
    showError("Please enter both username and password.");
    return;
  }

  if (username === DEMO_USERNAME && password === DEMO_PASSWORD) {
    hideError();

    localStorage.setItem("fittrack_loggedIn", "true");
    localStorage.setItem("fittrack_username", username);
    localStorage.setItem("fittrack_loginTime", new Date().toISOString());

    window.location.href = "dashboard.html";
  } else {
    showError("Invalid username or password. Try admin / password123.");
  }
}

loginBtn.addEventListener("click", checkLogin);

passwordInput.addEventListener("keydown", function (event) {
  if (event.key === "Enter") {
    checkLogin();
  }
});
