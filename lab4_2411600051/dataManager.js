const DataManager = (function () {

  const API_BASE_URL = "api/workouts.php";
  let usingLiveApi = false;

  let workouts = [];

  const state = {
    category: "All",
    status: "All",
    minCalories: 0,
    maxCalories: Infinity,
    searchQuery: ""
  };

  const FALLBACK_DATA = [
    { id: 1,  name: "Morning Run",          category: "Cardio",   date: "2026-08-19", duration: 32, calories: 310, status: "Completed", intensity: "Medium" },
    { id: 2,  name: "Upper Body Strength",  category: "Strength", date: "2026-08-18", duration: 45, calories: 260, status: "Completed", intensity: "High" },
    { id: 3,  name: "Yoga & Stretching",    category: "Yoga",     date: "2026-08-17", duration: 25, calories: 110, status: "Completed", intensity: "Low" },
    { id: 4,  name: "Long Distance Cycling",category: "Cycling",  date: "2026-08-16", duration: 50, calories: 420, status: "Completed", intensity: "High" },
    { id: 5,  name: "HIIT Session",         category: "HIIT",     date: "2026-08-15", duration: 20, calories: 290, status: "Missed",    intensity: "High" },
    { id: 6,  name: "Lower Body Strength",  category: "Strength", date: "2026-08-14", duration: 40, calories: 240, status: "Completed", intensity: "Medium" },
    { id: 7,  name: "Evening Jog",          category: "Cardio",   date: "2026-08-13", duration: 28, calories: 250, status: "Completed", intensity: "Medium" },
    { id: 8,  name: "Power Yoga Flow",      category: "Yoga",     date: "2026-08-12", duration: 30, calories: 150, status: "Missed",    intensity: "Low" },
    { id: 9,  name: "Sprint Intervals",     category: "HIIT",     date: "2026-08-11", duration: 22, calories: 300, status: "Completed", intensity: "High" },
    { id: 10, name: "City Bike Ride",       category: "Cycling",  date: "2026-08-10", duration: 35, calories: 280, status: "Completed", intensity: "Medium" },
    { id: 11, name: "Core & Abs",           category: "Strength", date: "2026-08-09", duration: 20, calories: 130, status: "Missed",    intensity: "Medium" },
    { id: 12, name: "Treadmill Run",        category: "Cardio",   date: "2026-08-22", duration: 30, calories: 0,   status: "Scheduled", intensity: "Medium" },
    { id: 13, name: "Full Body HIIT",       category: "HIIT",     date: "2026-08-23", duration: 25, calories: 0,   status: "Scheduled", intensity: "High" },
    { id: 14, name: "Restorative Yoga",     category: "Yoga",     date: "2026-08-24", duration: 30, calories: 0,   status: "Scheduled", intensity: "Low" },
    { id: 15, name: "Hill Cycling",         category: "Cycling",  date: "2026-08-20", duration: 55, calories: 460, status: "Completed", intensity: "High" }
  ];

  async function initializeData() {
    try {
      const response = await fetch(API_BASE_URL, { method: "GET" });
      if (!response.ok) throw new Error("API responded with status " + response.status);
      const data = await response.json();
      if (!Array.isArray(data)) throw new Error("Unexpected API response shape");
      workouts = data;
      usingLiveApi = true;
    } catch (err) {
      workouts = JSON.parse(JSON.stringify(FALLBACK_DATA));
      usingLiveApi = false;
    }
    return workouts;
  }

  function isUsingLiveApi() {
    return usingLiveApi;
  }

  function getWorkouts() {
    return workouts;
  }

  function getWorkoutById(id) {
    return workouts.find(function (w) { return w.id === Number(id); }) || null;
  }

  function getWorkoutsByCategory(category) {
    if (category === "All") return workouts;
    return workouts.filter(function (w) { return w.category === category; });
  }

  function getMissedWorkouts() {
    return workouts.filter(function (w) { return w.status === "Missed"; });
  }

  function getWorkoutStatistics() {
    const totalWorkouts = workouts.length;
    const totalCalories = workouts.reduce(function (sum, w) { return sum + w.calories; }, 0);
    const completedCount = workouts.filter(function (w) { return w.status === "Completed"; }).length;
    const missedCount = workouts.filter(function (w) { return w.status === "Missed"; }).length;
    const scheduledCount = workouts.filter(function (w) { return w.status === "Scheduled"; }).length;

    return { totalWorkouts, totalCalories, completedCount, missedCount, scheduledCount };
  }

  function getCategorySummary() {
    const categories = ["Cardio", "Strength", "Yoga", "HIIT", "Cycling"];
    return categories.map(function (cat) {
      const items = workouts.filter(function (w) { return w.category === cat; });
      const totalCalories = items.reduce(function (sum, w) { return sum + w.calories; }, 0);
      const totalDuration = items.reduce(function (sum, w) { return sum + w.duration; }, 0);
      return { category: cat, count: items.length, totalCalories, totalDuration };
    });
  }

  function filterByCategory(category) {
    state.category = category || "All";
    return applyFilters();
  }

  function filterByStatus(status) {
    state.status = status || "All";
    return applyFilters();
  }

  function filterByCalorieRange(min, max) {
    state.minCalories = (min === "" || min === undefined || min === null) ? 0 : Number(min);
    state.maxCalories = (max === "" || max === undefined || max === null) ? Infinity : Number(max);
    return applyFilters();
  }

  function applyFilters() {
    return workouts.filter(function (w) {
      const matchesCategory = state.category === "All" || w.category === state.category;
      const matchesStatus = state.status === "All" || w.status === state.status;
      const matchesCalories = w.calories >= state.minCalories && w.calories <= state.maxCalories;
      const matchesSearch = state.searchQuery === "" ||
        w.name.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
        ("WK" + String(w.id).padStart(3, "0")).toLowerCase().includes(state.searchQuery.toLowerCase());
      return matchesCategory && matchesStatus && matchesCalories && matchesSearch;
    });
  }

  function searchWorkouts(query) {
    state.searchQuery = query || "";
    return applyFilters();
  }

  function updateSearchResults(query) {
    return searchWorkouts(query);
  }

  function resetFilters() {
    state.category = "All";
    state.status = "All";
    state.minCalories = 0;
    state.maxCalories = Infinity;
    state.searchQuery = "";
  }

  function getState() {
    return Object.assign({}, state);
  }

  async function addWorkout(workoutData) {
    if (usingLiveApi) {
      try {
        const response = await fetch(API_BASE_URL, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(workoutData)
        });
        if (!response.ok) throw new Error("API responded with status " + response.status);
        const created = await response.json();
        workouts.unshift(created);
        return created;
      } catch (err) {}
    }
    const localWorkout = Object.assign(
      { id: workouts.length ? Math.max.apply(null, workouts.map(function (w) { return w.id; })) + 1 : 1 },
      workoutData
    );
    workouts.unshift(localWorkout);
    return localWorkout;
  }

  async function updateWorkoutStatus(id, updates) {
    if (usingLiveApi) {
      try {
        const response = await fetch(`${API_BASE_URL}?id=${id}`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(updates)
        });
        if (!response.ok) throw new Error("API responded with status " + response.status);
        const updated = await response.json();
        workouts = workouts.map(function (w) { return w.id === id ? updated : w; });
        return updated;
      } catch (err) {}
    }
    workouts = workouts.map(function (w) {
      return w.id === id ? Object.assign({}, w, updates) : w;
    });
    return getWorkoutById(id);
  }

  function exportToCSV(data) {
    const rows = data && data.length ? data : workouts;
    const header = ["ID", "SKU", "Workout Name", "Category", "Date", "Duration (min)", "Calories", "Status", "Intensity"];
    const lines = rows.map(function (w) {
      const sku = "WK" + String(w.id).padStart(3, "0");
      return [w.id, sku, w.name, w.category, w.date, w.duration, w.calories, w.status, w.intensity]
        .map(function (val) { return `"${String(val).replace(/"/g, '""')}"`; })
        .join(",");
    });
    return [header.join(","), ...lines].join("\n");
  }

  function downloadCSV(csvContent, filename) {
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", filename || "workout_log.csv");
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }

  async function simulateNewWorkout() {
    const categories = ["Cardio", "Strength", "Yoga", "HIIT", "Cycling"];
    const names = {
      Cardio: ["Interval Run", "Treadmill Sprint", "Stair Climb"],
      Strength: ["Deadlift Session", "Push Day", "Leg Day"],
      Yoga: ["Sunrise Yoga", "Deep Stretch", "Mobility Flow"],
      HIIT: ["Tabata Blast", "Circuit Training", "Bodyweight HIIT"],
      Cycling: ["Spin Class", "Trail Ride", "Sprint Cycling"]
    };
    const category = categories[Math.floor(Math.random() * categories.length)];
    const nameOptions = names[category];
    const name = nameOptions[Math.floor(Math.random() * nameOptions.length)];
    const duration = 15 + Math.floor(Math.random() * 40);
    const calories = Math.round(duration * (5 + Math.random() * 5));
    const intensities = ["Low", "Medium", "High"];

    return addWorkout({
      name: name,
      category: category,
      date: new Date().toISOString().slice(0, 10),
      duration: duration,
      calories: calories,
      status: "Completed",
      intensity: intensities[Math.floor(Math.random() * intensities.length)]
    });
  }

  return {
    initializeData,
    isUsingLiveApi,
    getWorkouts,
    getWorkoutById,
    getWorkoutsByCategory,
    getMissedWorkouts,
    getWorkoutStatistics,
    getCategorySummary,
    filterByCategory,
    filterByStatus,
    filterByCalorieRange,
    applyFilters,
    searchWorkouts,
    updateSearchResults,
    resetFilters,
    getState,
    exportToCSV,
    downloadCSV,
    addWorkout,
    updateWorkoutStatus,
    simulateNewWorkout
  };

})();
