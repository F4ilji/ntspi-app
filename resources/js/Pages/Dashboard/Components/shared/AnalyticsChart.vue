<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-foreground">Посещаемость</h3>
      <div class="flex bg-layer border border-layer-line rounded-md p-0.5">
        <button
          @click="metric = 'visitors'"
          class="px-2.5 py-1 text-[11px] font-medium rounded transition-colors"
          :class="metric === 'visitors' ? 'bg-primary text-white' : 'text-muted-foreground-1 hover:text-foreground'"
        >
          Посетители
        </button>
        <button
          @click="metric = 'views'"
          class="px-2.5 py-1 text-[11px] font-medium rounded transition-colors"
          :class="metric === 'views' ? 'bg-primary text-white' : 'text-muted-foreground-1 hover:text-foreground'"
        >
          Просмотры
        </button>
      </div>
    </div>

    <div v-if="data.length === 0" class="text-xs text-muted-foreground-1 py-8 text-center">
      Нет данных за выбранный период
    </div>

    <div v-else-if="data.length === 1" class="py-8 text-center">
      <p class="text-3xl font-bold text-foreground">{{ data[0][metric === 'views' ? 'views' : 'visitors'] }}</p>
      <p class="text-xs text-muted-foreground-1 mt-1">{{ formatDate(data[0].date) }}</p>
    </div>

    <div v-else class="relative" style="height: 200px;">
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend);

export default {
  name: 'AnalyticsChart',
  components: { Line },
  props: {
    data: { type: Array, required: true },
  },
  data() {
    return { metric: 'visitors' };
  },
  computed: {
    chartData() {
      const values = this.data.map(d => this.metric === 'views' ? d.views : d.visitors);
      return {
        labels: this.data.map(d => this.formatDate(d.date)),
        datasets: [
          {
            label: this.metric === 'views' ? 'Просмотры' : 'Посетители',
            data: values,
            borderColor: 'rgb(30, 87, 163)',
            backgroundColor: 'rgba(30, 87, 163, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: this.data.length > 31 ? 0 : 3,
            pointHoverRadius: 5,
            pointBackgroundColor: 'white',
            pointBorderColor: 'rgb(30, 87, 163)',
            pointBorderWidth: 2,
          },
        ],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index',
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgb(30, 30, 30)',
            titleFont: { size: 11 },
            bodyFont: { size: 12 },
            padding: 8,
            cornerRadius: 6,
            displayColors: false,
            callbacks: {
              title: (items) => {
                const idx = items[0].dataIndex;
                const d = new Date(this.data[idx].date);
                const days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
                return `${d.getDate()}.${d.getMonth() + 1} (${days[d.getDay()]})`;
              },
              label: (item) => `${this.metric === 'views' ? 'Просмотры' : 'Посетители'}: ${item.formattedValue}`,
            },
          },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { size: 10 },
              color: '#9ca3af',
              maxTicksLimit: this.data.length > 31 ? 10 : 15,
            },
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.05)' },
            ticks: {
              font: { size: 10 },
              color: '#9ca3af',
              precision: 0,
            },
          },
        },
      };
    },
  },
  methods: {
    formatDate(dateStr) {
      const d = new Date(dateStr);
      return `${d.getDate()}.${d.getMonth() + 1}`;
    },
  },
  watch: {
    metric() {},
  },
};
</script>
