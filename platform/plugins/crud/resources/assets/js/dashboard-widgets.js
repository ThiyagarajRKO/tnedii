window.doughnutChart = {};
window.barChart = {};
let chartUtils = {
    chartElm: document.getElementById('dashboard-chart'),
    dashboardStats: [],

    init: function (config) {
        this.plotChart(config);
        this.plotBarChart(config);
    },

    plotChart: function (config) {
        if (config.type != "pie") {
            return false;
        }

        let labels = [];
        let bgColor = [];
        let statsData = [];
        let chartElm = config.chartElm;

        $(config.dashboardStats).each(function (k, data) {
            labels.push(data.title);
            statsData.push(data.cnt);
            if (!data.color) {
                var back = ["#ff0000", "blue", "gray"];
                data.color = back[Math.floor(Math.random() * back.length)];
            }
            bgColor.push(data.color);
        });

        let chartConfig = {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: '# of Votes',
                    data: statsData,
                    backgroundColor: bgColor
                }],

            },
            options: {
                cutoutPercentage: 75,
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'bottom',
                },
                title: {
                    display: false,
                    text: 'Technology'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                tooltips: {
                    enabled: true,
                    intersect: false,
                    mode: 'nearest',
                    bodySpacing: 5,
                    yPadding: 10,
                    xPadding: 10,
                    caretPadding: 0,
                    displayColors: true,
                    //  backgroundColor: KTApp.getStateColor('brand'),
                    titleFontColor: '#ffffff',
                    cornerRadius: 4,
                    footerSpacing: 0,
                    titleSpacing: 0
                }
            }
        };

        var ctx = document.getElementById('dashboard-chart' + chartElm).getContext('2d');
        if (window.doughnutChart[chartElm]) {
            window.doughnutChart[chartElm].destroy();
        }
        window.doughnutChart[chartElm] = new Chart(ctx, chartConfig);
    },

    plotBarChart: function (config) {
        let chartElm = config.chartElm;

        if (config.type != "bar") {
            return false;
        }
        if (window.barChart[chartElm]) {
            window.barChart[chartElm].destroy();
        }

        // let chartsData = (!$.isEmptyObject(filterData)) ? filterData : barCharts;
        let data = [];
        $.each(config.dashboardStats, function (index, value) {
            let dashlets = {
                label: value.title,
                backgroundColor: value.color,
                data: [value.cnt],
                stack: value.slug
            }
            data.push(dashlets);
        });

        window.barChart[chartElm] = new Chart(document.getElementById('dashboard-chart' + chartElm), {
            type: 'bar',
            data: {
                labels: [2021],
                datasets: data
            },
            options: {
                title: {
                    display: false,
                    text: 'Population growth (millions)'
                },
                legend: {
                    display: true,
                    position: 'bottom',
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            //  floored the value using callback
                            userCallback: function (label, index, labels) {
                                if (Math.floor(label) === label) {
                                    return label;
                                }

                            },
                        }
                    }]
                }
            }
        });

    }
}

$(document).ready((function () {

    if (!$.isEmptyObject(dashboardStatsConfig)) {
        $.map(dashboardStatsConfig, function (obj, k) {
            if (obj.type == "table") {
                BDashboard.loadWidget($("#" + k).find(".widget-content"), route("crud.widget.dashboard-stats"), obj);
            } else {
                let config = {
                    chartElm: k,
                    dashboardStats: obj.data,
                    type: obj.type
                };
                chartUtils.init(config);
            }
        })
    }
}));

