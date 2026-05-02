<?php
/**
 * js/chartGeneric.php
 * Modern Chart.js v4 compatible generic chart script
 * Updated for better performance and maintainability
 */
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('StatsGraph');
    
    if (!ctx) {
        console.error('Canvas element #StatsGraph not found');
        return;
    }

    new Chart(ctx, {
        type: '<?= $MyChartType ?? "bar" ?>',
        data: {
            labels: <?= $LabelNames ?? '[]' ?>,
            datasets: [{
                label: '<?= htmlspecialchars($MyChartTitle ?? "Statistics") ?>',
                data: <?= $DataPoints ?? '[]' ?>,
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',   // Primary blue
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(54, 162, 235, 0.7)'
                ],
                borderColor: [
                    'rgba(13, 110, 253, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#e0e0e0',
                        font: { size: 14 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#ddd'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.1)'
                    },
                    ticks: {
                        color: '#aaa'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255,255,255,0.1)'
                    },
                    ticks: {
                        color: '#aaa'
                    }
                }
            }
        }
    });
});
</script>
