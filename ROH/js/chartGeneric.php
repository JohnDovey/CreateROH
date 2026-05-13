<?php
/**
 * js/chartGeneric.php
 * Chart.js v4 — server supplies JSON-safe arrays; this include validates types and encodes for the browser.
 */
$rohAllowedChartTypes = ['bar', 'line', 'radar', 'pie', 'doughnut', 'polarArea'];
$rohChartTypeIn = isset($MyChartType) ? (string) $MyChartType : 'bar';
$rohChartTypeJs = in_array($rohChartTypeIn, $rohAllowedChartTypes, true) ? $rohChartTypeIn : 'bar';

$rohEncodeChartDataset = function ($value, $default = '[]') {
    if (is_array($value)) {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    if (is_string($value) && $value !== '') {
        json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $value;
        }
    }
    return $default;
};

$rohLabelsJs = $rohEncodeChartDataset(isset($LabelNames) ? $LabelNames : null);
$rohDataJs = $rohEncodeChartDataset(isset($DataPoints) ? $DataPoints : null);
$rohTitleJs = json_encode(isset($MyChartTitle) ? (string) $MyChartTitle : 'Statistics', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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
        type: <?= json_encode($rohChartTypeJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        data: {
            labels: <?= $rohLabelsJs ?>,
            datasets: [{
                label: <?= $rohTitleJs ?>,
                data: <?= $rohDataJs ?>,
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
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
