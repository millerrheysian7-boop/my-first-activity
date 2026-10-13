(function () {
  const POLL_INTERVAL_MS = 30000; // 30 seconds, as required by the lab

  function animateNumber(el, newValue) {
    if (!el) return;
    const oldValue = parseFloat(el.dataset.rawValue || el.textContent.replace(/[^0-9.-]/g, "")) || 0;
    const isMoney = el.dataset.format === "currency";
    const steps = 20;
    let step = 0;

    const timer = setInterval(function () {
      step++;
      const progress = step / steps;
      const current = oldValue + (newValue - oldValue) * progress;
      el.textContent = isMoney ? "$" + current.toFixed(2) : Math.round(current);
      if (step >= steps) {
        clearInterval(timer);
        el.textContent = isMoney ? "$" + Number(newValue).toFixed(2) : newValue;
        el.dataset.rawValue = newValue;
      }
    }, 20);
  }

  function updateLastUpdated() {
    const el = document.getElementById("lastUpdated");
    if (el) el.textContent = "Last updated: " + new Date().toLocaleTimeString();
  }

  async function fetchStats() {
    try {
      const res = await fetch("/api/dashboard/stats");
      if (!res.ok) throw new Error("Stats request failed: " + res.status);
      const data = await res.json();

      animateNumber(document.getElementById("statTotalProducts"), data.total_products);
      animateNumber(document.getElementById("statLowStock"), data.low_stock_count);
      animateNumber(document.getElementById("statOutOfStock"), data.out_of_stock_count);
      animateNumber(document.getElementById("statTotalValue"), data.total_value);
    } catch (err) {
      console.error("Dashboard stats polling error:", err);
    }
  }

  async function fetchLowStock() {
    try {
      const res = await fetch("/api/products/low-stock");
      if (!res.ok) throw new Error("Low stock request failed: " + res.status);
      const { data } = await res.json();
      const container = document.getElementById("lowStockAlerts");
      if (!container) return;

      if (data.length === 0) {
        container.innerHTML = '<p class="text-muted mb-0">No low stock items right now.</p>';
        return;
      }

      container.innerHTML = data.map(function (p) {
        return '<div class="alert-lowstock d-flex justify-content-between align-items-center mb-2">' +
          '<span><strong>' + p.name + '</strong> (' + p.sku + ')</span>' +
          '<span>' + p.quantity + ' / reorder at ' + p.reorder_level + '</span>' +
          '</div>';
      }).join("");
    } catch (err) {
      console.error("Low stock polling error:", err);
    }
  }

  async function fetchRecentTransactions() {
    try {
      const res = await fetch("/api/transactions/recent");
      if (!res.ok) throw new Error("Recent transactions request failed: " + res.status);
      const { data } = await res.json();
      const tbody = document.getElementById("recentTransactionsBody");
      if (!tbody) return;

      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No recent transactions.</td></tr>';
        return;
      }

      tbody.innerHTML = data.map(function (t) {
        const badgeClass = t.type === "stock_in" ? "bg-success" : (t.type === "stock_out" ? "bg-danger" : "bg-warning");
        const sign = t.quantity > 0 ? "+" : "";
        return "<tr>" +
          "<td>" + t.product_name + "</td>" +
          '<td><span class="badge ' + badgeClass + '">' + t.type.replace("_", " ") + "</span></td>" +
          "<td>" + sign + t.quantity + "</td>" +
          "<td>" + t.user_name + " &middot; " + t.created_at + "</td>" +
          "</tr>";
      }).join("");
    } catch (err) {
      console.error("Recent transactions polling error:", err);
    }
  }

  function pollAll() {
    fetchStats();
    fetchLowStock();
    fetchRecentTransactions();
    updateLastUpdated();
  }

  document.addEventListener("DOMContentLoaded", function () {
    pollAll(); // run once immediately on load
    setInterval(pollAll, POLL_INTERVAL_MS);
  });
})();
