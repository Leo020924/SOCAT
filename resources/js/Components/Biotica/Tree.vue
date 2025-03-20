<template>
    <div>
      <div ref="chartContainer" class="chart-container"></div>
    </div>
  </template>

  <script>
  

  export default {
    props: {
      treeMapData: {
        type: Array,
        required: true
      },
      width: {
        type: Number,
        default: 605
      },
      height: {
        type: Number,
        default: 605
      },
      google: {
        type: Object, //Importante el tipo de datos
        required: true, //Lo requerimos
      }
    },
    mounted() {
      this.renderChart();
    },
    methods: {
      renderChart() {
        const data = this.treeMapData;
        const width = this.width;
        const height = this.height;

        const treeMap = new this.google.visualization.TreeMap(this.$refs.chartContainer);

        treeMap.draw(this.google.visualization.arrayToDataTable(data), {
          minColor: '#f00',
          midColor: '#ddd',
          maxColor: '#0d0',
          headerHeight: 15,
          fontColor: 'black',
          showScale: true
        });
      }
    }
  }
  </script>

  <style scoped>
  .chart-container {
    width: 100%;
    height: 600px;
  }
  </style>