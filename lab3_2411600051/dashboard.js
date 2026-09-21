if (localStorage.getItem("fittrack_loggedIn") !== "true") {
  window.location.href = "index.html";
}

const username = localStorage.getItem("fittrack_username") || "Guest";

function updateGreeting() {
  const now = new Date();
  const hour = now.getHours();
  let greeting;

  if (hour < 12) {
    greeting = "Good morning";
  } else if (hour < 18) {
    greeting = "Good afternoon";
  } else {
    greeting = "Good evening";
  }

  document.getElementById("greetingText").innerHTML =
    `${greeting}, ${username.charAt(0).toUpperCase() + username.slice(1)}! 💪`;
  document.getElementById("greetingSub").textContent =
    "Here's your fitness summary for today, " +
    now.toLocaleDateString("en-US", { weekday: "long", year: "numeric", month: "long", day: "numeric" });

  document.getElementById("navUsername").textContent = username;
}

function updateStatistics() {
  const stats = [
    { id: "stat1", title: "Steps Today", value: "8,742" },
    { id: "stat2", title: "Calories Burned", value: "512 kcal" },
    { id: "stat3", title: "Workouts This Week", value: "4" },
    { id: "stat4", title: "Avg Heart Rate", value: "128 bpm" }
  ];

  stats.forEach(function (stat) {
    document.getElementById(stat.id + "-title").textContent = stat.title;
    document.getElementById(stat.id + "-value").textContent = stat.value;
  });

  const weeklySteps = 32450;
  const weeklyGoal = 50000;
  const percent = Math.round((weeklySteps / weeklyGoal) * 100);

  const progressBar = document.getElementById("goalProgressBar");
  progressBar.style.width = percent + "%";
  progressBar.setAttribute("aria-valuenow", percent);
  document.getElementById("goalPercent").textContent = percent + "%";
}

function populateActivityTable() {
  const activities = [
    { date: "Aug 19, 2026", activity: "Morning Run", duration: "32 min", calories: "310 kcal", status: "Completed" },
    { date: "Aug 18, 2026", activity: "Upper Body Strength", duration: "45 min", calories: "260 kcal", status: "Completed" },
    { date: "Aug 17, 2026", activity: "Yoga & Stretching", duration: "25 min", calories: "110 kcal", status: "Completed" },
    { date: "Aug 16, 2026", activity: "Cycling", duration: "50 min", calories: "420 kcal", status: "Completed" },
    { date: "Aug 15, 2026", activity: "HIIT Session", duration: "20 min", calories: "290 kcal", status: "Missed" }
  ];

  const tableBody = document.getElementById("activityTableBody");
  tableBody.innerHTML = "";

  activities.forEach(function (item) {
    const badgeClass = item.status === "Completed" ? "badge-success-custom" : "badge-danger-custom";

    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${item.date}</td>
      <td><span class="badge badge-workout me-1"><i class="bi bi-lightning-charge"></i></span> ${item.activity}</td>
      <td>${item.duration}</td>
      <td>${item.calories}</td>
      <td><span class="badge ${badgeClass}">${item.status}</span></td>
    `;
    tableBody.appendChild(row);
  });
}

function logout() {
  localStorage.removeItem("fittrack_loggedIn");
  localStorage.removeItem("fittrack_username");
  localStorage.removeItem("fittrack_loginTime");
  window.location.href = "index.html";
}

document.getElementById("logoutBtn").addEventListener("click", logout);

updateGreeting();
updateStatistics();
populateActivityTable();
