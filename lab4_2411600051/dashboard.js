if (localStorage.getItem("fittrack_loggedIn") !== "true") {
  window.location.href = "index.html";
}

const username = localStorage.getItem("fittrack_username") || "Guest";

let categoryChart = null;
let statusChart = null;
let topWorkoutsChart = null;

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
  const stats = DataManager.getWorkoutStatistics();

  document.getElementById("stat1-title").textContent = "Total Calories Burned";
  document.getElementById("stat1-value").textContent = stats.totalCalories.toLocaleString() + " kcal";

  document.getElementById("stat2-title").textContent = "Workouts Completed";
  document.getElementById("stat2-value").textContent = stats.completedCount;

  document.getElementById("stat3-title").textContent = "Workouts Missed";
  document.getElementById("stat3-value").textContent = stats.missedCount;

  document.getElementById("stat4-title").textContent = "Scheduled Workouts";
  document.getElementById("stat4-value").textContent = stats.scheduledCount;

  const weeklyGoal = 2500;
  const percent = Math.min(100, Math.round((stats.totalCalories / weeklyGoal) * 100));

  const progressBar = document.getElementById("goalProgressBar");
  progressBar.style.width = percent + "%";
  progressBar.setAttribute("aria-valuenow", percent);
  document.getElementById("goalPercent").textContent = percent + "%";
}

function updateAlerts() {
  const missed = DataManager.getMissedWorkouts();
  const alertSection = document.getElementById("alertSection");

  if (missed.length === 0) {
    alertSection.innerHTML = "";
    return;
  }

  const names = missed.map(function (w) { return w.name; }).join(", ");
  alertSection.innerHTML = `
    <div class="alert alert-lowstock d-flex align-items-start" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
      <div>
        <strong>${missed.length} missed workout${missed.length > 1 ? "s" : ""}!</strong>
        You skipped: ${names}. Consider rescheduling to stay on track with your weekly goal.
      </div>
    </div>
  `;
}

function populateCategoryFilter() {
  const select = document.getElementById("categoryFilter");
  const categories = ["All", "Cardio", "Strength", "Yoga", "HIIT", "Cycling"];
  select.innerHTML = categories.map(function (c) {
    return `<option value="${c}">${c === "All" ? "All Categories" : c}</option>`;
  }).join("");
}

function statusBadgeClass(status) {
  if (status === "Completed") return "badge-success-custom";
  if (status === "Missed") return "badge-danger-custom";
  return "badge-scheduled-custom";
}

function highlightMatch(text, query) {
  if (!query) return text;
  const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`, "ig");
  return text.replace(re, "<mark>$1</mark>");
}

function renderWorkoutTable(workoutList) {
  const query = document.getElementById("searchInput") ? document.getElementById("searchInput").value : "";
  const tableBody = document.getElementById("activityTableBody");
  tableBody.innerHTML = "";

  if (workoutList.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-3">No workouts match your filters.</td></tr>`;
    return;
  }

  workoutList.forEach(function (item) {
    const badgeClass = statusBadgeClass(item.status);
    const rowClass = item.status === "Missed" ? "table-row-missed" : "";

    const row = document.createElement("tr");
    row.className = rowClass;
    row.innerHTML = `
      <td>${item.date}</td>
      <td><span class="badge badge-workout me-1"><i class="bi bi-lightning-charge"></i></span> ${highlightMatch(item.name, query)}</td>
      <td>${item.category}</td>
      <td>${item.duration} min</td>
      <td>${item.calories} kcal</td>
      <td><span class="badge ${badgeClass}">${item.status}</span></td>
    `;
    tableBody.appendChild(row);
  });
}

const chartColors = {
  primary: "#8A5F41",
  secondary: "#A77F60",
  accent: "#CCD67F",
  success: "#7A8F4A",
  danger: "#B45B4A",
  lightAccent: "#EDE0C8"
};

function renderCategoryChart() {
  const summary = DataManager.getCategorySummary();
  const ctx = document.getElementById("categoryChart").getContext("2d");

  if (categoryChart) categoryChart.destroy();
  categoryChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: summary.map(function (s) { return s.category; }),
      datasets: [{
        label: "Calories Burned",
        data: summary.map(function (s) { return s.totalCalories; }),
        backgroundColor: [chartColors.primary, chartColors.secondary, chartColors.accent, chartColors.success, chartColors.danger]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
}

function renderStatusChart() {
  const stats = DataManager.getWorkoutStatistics();
  const ctx = document.getElementById("statusChart").getContext("2d");

  if (statusChart) statusChart.destroy();
  statusChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Completed", "Scheduled", "Missed"],
      datasets: [{
        data: [stats.completedCount, stats.scheduledCount, stats.missedCount],
        backgroundColor: [chartColors.success, chartColors.accent, chartColors.danger]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: "bottom" } }
    }
  });
}

function renderTopWorkoutsChart() {
  const top5 = [...DataManager.getWorkouts()]
    .sort(function (a, b) { return b.calories - a.calories; })
    .slice(0, 5);

  const ctx = document.getElementById("topWorkoutsChart").getContext("2d");
  if (topWorkoutsChart) topWorkoutsChart.destroy();
  topWorkoutsChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: top5.map(function (w) { return w.name; }),
      datasets: [{
        label: "Calories Burned",
        data: top5.map(function (w) { return w.calories; }),
        backgroundColor: chartColors.primary
      }]
    },
    options: {
      indexAxis: "y",
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { x: { beginAtZero: true } }
    }
  });
}

function renderAllCharts() {
  renderCategoryChart();
  renderStatusChart();
  renderTopWorkoutsChart();
}

function refreshDashboard() {
  const filtered = DataManager.applyFilters();
  renderWorkoutTable(filtered);
  updateStatistics();
  updateAlerts();
  renderAllCharts();
}

function setupFilterHandlers() {
  document.getElementById("categoryFilter").addEventListener("change", function (e) {
    DataManager.filterByCategory(e.target.value);
    renderWorkoutTable(DataManager.applyFilters());
  });

  document.getElementById("statusFilter").addEventListener("change", function (e) {
    DataManager.filterByStatus(e.target.value);
    renderWorkoutTable(DataManager.applyFilters());
  });

  document.getElementById("applyCalorieFilter").addEventListener("click", function () {
    const min = document.getElementById("minCalories").value;
    const max = document.getElementById("maxCalories").value;
    DataManager.filterByCalorieRange(min, max);
    renderWorkoutTable(DataManager.applyFilters());
  });

  document.getElementById("clearFiltersBtn").addEventListener("click", function () {
    DataManager.resetFilters();
    document.getElementById("categoryFilter").value = "All";
    document.getElementById("statusFilter").value = "All";
    document.getElementById("minCalories").value = "";
    document.getElementById("maxCalories").value = "";
    document.getElementById("searchInput").value = "";
    renderWorkoutTable(DataManager.applyFilters());
  });

  document.getElementById("searchInput").addEventListener("input", function (e) {
    DataManager.updateSearchResults(e.target.value);
    renderWorkoutTable(DataManager.applyFilters());
  });
}

function setupExportHandler() {
  document.getElementById("exportCsvBtn").addEventListener("click", function () {
    const currentView = DataManager.applyFilters();
    const csv = DataManager.exportToCSV(currentView);
    const dateStamp = new Date().toISOString().slice(0, 10);
    DataManager.downloadCSV(csv, `fittrack_workout_log_${dateStamp}.csv`);
  });
}

function showToast(message) {
  const toastEl = document.getElementById("liveToast");
  document.getElementById("liveToastBody").textContent = message;
  const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
  toast.show();
}

function startRealTimeSimulation() {
  setInterval(async function () {
    const newWorkout = await DataManager.simulateNewWorkout();
    refreshDashboard();
    showToast(`New activity logged: "${newWorkout.name}" (${newWorkout.calories} kcal)`);
  }, 15000);
}

function updateApiStatusBadge() {
  const badge = document.getElementById("apiStatusBadge");
  if (!badge) return;
  if (DataManager.isUsingLiveApi()) {
    badge.textContent = "Live data (PHP API)";
    badge.className = "badge badge-success-custom";
  } else {
    badge.textContent = "Offline sample data";
    badge.className = "badge badge-scheduled-custom";
  }
}

function logout() {
  localStorage.removeItem("fittrack_loggedIn");
  localStorage.removeItem("fittrack_username");
  localStorage.removeItem("fittrack_loginTime");
  window.location.href = "index.html";
}

document.getElementById("logoutBtn").addEventListener("click", logout);

(async function initDashboard() {
  updateGreeting();
  document.getElementById("activityTableBody").innerHTML =
    `<tr><td colspan="6" class="text-center text-muted py-3">
       <span class="spinner-border spinner-border-sm me-2"></span>Loading workout data...
     </td></tr>`;

  await DataManager.initializeData();

  updateApiStatusBadge();
  populateCategoryFilter();
  setupFilterHandlers();
  setupExportHandler();
  refreshDashboard();
  startRealTimeSimulation();
})();
