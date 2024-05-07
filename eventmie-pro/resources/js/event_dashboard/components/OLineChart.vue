<template>
  <LineChartGenerator
    :chart-options="chartOptions"
    :chart-data="chartData"
    :chart-id="chartId"
    :dataset-id-key="datasetIdKey"
    :plugins="plugins"
    :css-classes="cssClasses"
    :styles="styles"
    :width="width"
    :height="height"
  />
</template>

<script>
import { Line as LineChartGenerator } from 'vue-chartjs/legacy'
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, LinearScale, CategoryScale, PointElement } from 'chart.js'
ChartJS.register(Title, Tooltip, Legend, LineElement, LinearScale, CategoryScale, PointElement)

export default {
  name: 'LineChart',
  components: { LineChartGenerator },

  props: {
    chartId: {
      type: String,
      default: 'line-chart'
    },
    datasetIdKey: {
      type: String,
      default: 'label'
    },
    width: {
      type: Number,
      default: 400
    },
    height: {
      type: Number,
      default: 100
    },
    cssClasses: {
      default: '',
      type: String
    },
    styles: {
      type: Object,
      default: () => {}
    },
    plugins: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      chartData: {

        labels:[],
        datasets: [ 
            { 
              label: '',
              backgroundColor: '#2176FF',
              data: []
            }, 
        ],         
      },
      chartOptions: {
        responsive: true,
      }
    }
  },
  methods: {
    monthlyRevenue(){
      let url  = route('eventmie.monthly_revenue');
      let _this = this;
      _this.chartData.datasets[0].label = "Monthly Revenue";
      axios.get(url).then(function(response){
          response.data.forEach((item)=>{
              _this.chartData.labels.push(item.full_month);
              _this.chartData.datasets[0].data.push(item.total_revenue);
          });    
      });
    },
  },
  mounted() {
    this.monthlyRevenue();
  }
    
}
</script>