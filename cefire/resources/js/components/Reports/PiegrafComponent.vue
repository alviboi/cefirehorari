<script>
import { Pie } from "vue-chartjs";

export default {
  extends: Pie,
  props: ["data", "labels", "nom"],
  mounted() {
    this.renderChart(
      {
        labels: this.labels,
        datasets: [
          {
            label: this.labels,
            data: this.data,
            backgroundColor: ["Blue", "Orange", "Yellow", "Green"],
          },
        ],
      },
      {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: this.nom,
        },
        // scales: {
        //     yAxes: [{
        //         ticks: {
        //             beginAtZero: true
        //         }
        //     }]
        // }
      }
    );
  },
};
</script>
