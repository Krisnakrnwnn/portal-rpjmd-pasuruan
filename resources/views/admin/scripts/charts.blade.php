<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
  // ===== CHART.JS INIT =====
  (function initCharts() {
    const sectors = @json($chartSectors);
    if (!sectors || sectors.length === 0) return;

    // Show panel
    if (document.getElementById('chart-panel')) document.getElementById('chart-panel').style.display = 'block';

    // Build chart data
    const sectorLabels = sectors.map(s => s.name);
    const sectorAvgProgress = sectors.map(s => {
      const inds = s.indicators;
      if (!inds || inds.length === 0) return 0;
      const sum = inds.reduce((a, b) => a + b.progress, 0);
      return Math.round(sum / inds.length);
    });

    const palette = [
      '#3b82f6','#facc15','#10b981','#f97316','#8b5cf6','#ec4899','#14b8a6','#f43f5e'
    ];
    const sectorColors = sectors.map((_, i) => palette[i % palette.length]);

    // Doughnut
    const dCtx = document.getElementById('chart-doughnut');
    if (dCtx) {
      new Chart(dCtx, {
        type: 'doughnut',
        data: {
          labels: sectorLabels,
          datasets: [{
            data: sectorAvgProgress,
            backgroundColor: sectorColors,
            borderWidth: 3,
            borderColor: '#fff',
            hoverBorderWidth: 4,
          }]
        },
        options: {
          cutout: '72%',
          plugins: {
            legend: { position: 'bottom', labels: { font: { weight: 'bold', size: 11 }, padding: 16, usePointStyle: true } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw}%` } }
          }
        }
      });
    }

    // Bar — all indicators
    const barLabels = [];
    const barData = [];
    const barColors = [];
    sectors.forEach((sector, si) => {
      (sector.indicators || []).forEach(ind => {
        barLabels.push(ind.name.length > 20 ? ind.name.substring(0, 20) + '…' : ind.name);
        barData.push(ind.progress);
        barColors.push(sectorColors[si % sectorColors.length]);
      });
    });

    const bCtx = document.getElementById('chart-bar');
    if (bCtx && barLabels.length > 0) {
      new Chart(bCtx, {
        type: 'bar',
        data: {
          labels: barLabels,
          datasets: [{
            label: 'Progress (%)',
            data: barData,
            backgroundColor: barColors.map(c => c + 'cc'),
            borderColor: barColors,
            borderWidth: 2,
            borderRadius: 8,
          }]
        },
        options: {
          indexAxis: barLabels.length > 6 ? 'y' : 'x',
          scales: {
            x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
            y: { ticks: { font: { size: 10, weight: 'bold' } }, grid: { display: false } }
          },
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.raw}%` } }
          }
        }
      });
    }

    // ===== CHART CAPAIAN SECTION =====
    const cdCtx = document.getElementById('chart-capaian-doughnut');
    if (cdCtx) {
      new Chart(cdCtx, {
        type: 'doughnut',
        data: {
          labels: sectorLabels,
          datasets: [{ data: sectorAvgProgress, backgroundColor: sectorColors, borderWidth: 3, borderColor: '#fff', hoverBorderWidth: 4 }]
        },
        options: {
          cutout: '70%',
          plugins: {
            legend: { position: 'bottom', labels: { font: { weight: 'bold', size: 11 }, padding: 14, usePointStyle: true } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw}%` } }
          }
        }
      });
    }

    const cbCtx = document.getElementById('chart-capaian-bar');
    if (cbCtx && barLabels.length > 0) {
      new Chart(cbCtx, {
        type: 'bar',
        data: {
          labels: barLabels,
          datasets: [{ label: 'Progress (%)', data: barData, backgroundColor: barColors.map(c => c + 'cc'), borderColor: barColors, borderWidth: 2, borderRadius: 8 }]
        },
        options: {
          indexAxis: barLabels.length > 6 ? 'y' : 'x',
          scales: {
            x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
            y: { ticks: { font: { size: 10, weight: 'bold' } }, grid: { display: false } }
          },
          plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw}%` } } }
        }
      });
    }
  })();


</script>
