import $ from 'jquery';
const axios = require('axios').default;
const Chart = require('chart.js/auto').default;

$(document).ready(function () {
    axios.get('/api/stats/sales/category').then(function (response) {
        let labels = [];
        let data = [];

        let responseData = response.data.data;

        responseData.forEach(function (category) {
            labels.push(category.Categoria);
            data.push(parseInt((category.TotalVentas)));
        });

        let myChart = new Chart(($('#sales-category-chart')[0] as HTMLCanvasElement).getContext("2d"), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas por categoría',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)'
                    ]
                }]
            }
        });

        console.log(myChart);

        myChart.render();
    });

    axios.get('/api/stats/sales/month').then(function (response) {
        let labels = [];
        let data = [];

        for (let i = 1; i <= 12; i++) {
            labels.push(i);
            data.push(0);
        }

        let responseData = response.data.data;

        responseData.forEach(function (month) {
            data[month.Mes - 1] = parseInt(month.TotalVentas);
        });

        let myChart = new Chart(($('#sales-month-chart')[0] as HTMLCanvasElement).getContext("2d"), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas por mes',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                    ]
                }]
            }
        });

        console.log(myChart);

        myChart.render();
    });
});