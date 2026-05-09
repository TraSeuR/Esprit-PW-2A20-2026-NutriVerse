

const ctx = document.getElementById("recipeChart");

if (ctx) {
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: recipeLabels,
      datasets: [{
        data: recipeData,
        backgroundColor: [
          "#4CAF50",
          "#FF9800",
          "#2196F3",
          "#9C27B0",
          "#E91E63",
          "#00BCD4"
        ],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,   // important
      cutout: "65%",
      plugins: {
        legend: {
          position: "bottom"
        }
      }
    }
  });
}