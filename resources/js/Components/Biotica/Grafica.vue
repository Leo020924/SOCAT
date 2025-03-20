<!-- src/Components/Biotica/TreeMapComponent.vue -->
<template>
    <div>
      <h3>Gráfico Tree Map</h3>
      <GChart
        :type="chartType"
        :data="chartData"
        :options="chartOptions"
      />
    </div>
  </template>
  
  <script>
  import { GChart } from 'vue-google-charts';
  import axios from 'axios';
  
  export default {
    components: {
      GChart,
    },
    data() {
      return {
        chartType: 'TreeMap',
        chartData: [],
        chartOptions: {
          enableHighlight: true,
          maxDepth: 1,
          maxPostDepth: 2,
          minHighlightColor: '#8c6bb1',
          midHighlightColor: '#9ebcda',
          maxHighlightColor: '#edf8fb',
          minColor: '#009688',
          midColor: '#f7f7f7',
          maxColor: '#ee8100',
          headerHeight: 15,
          showScale: true,
          height: 500,
          useWeightedAverageForAggregation: true,
          eventsConfig: {
            highlight: ['click'],
            unhighlight: ['mouseout'],
            rollup: ['contextmenu'],
            drilldown: ['dblclick'],
          },
        },
      };
    },
    mounted() {
      this.fetchChartData();
    },
    methods: {
      async fetchChartData() {
        try {
          const response = await axios.get('/api/treemap-data'); // Reemplaza con tu ruta de Laravel
          this.chartData = response.data;
        } catch (error) {
          console.error('Error fetching chart data:', error);
        }
      },
    },
  };
  </script>