<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  props: ['data','labels','nom_datos','nom'],
  mounted() {
    this.renderChart(
      {
        labels: this.labels,
        datasets: [
          {
            label: this.nom_datos,
            data: this.data,
            backgroundColor: "#f87979",
            // borderColor: "rgba(1, 116, 188, 0.50)",
            // pointBackgroundColor: "rgba(171, 71, 188, 1)"
          }
        ]
      },
      {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: this.nom
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
      }
    );
  }
};
</script>
