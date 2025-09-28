<template>
  <div
      class="text-white min-h-screen flex font-sans transition-all duration-500"
      :class="{ 'invert': isResetting }"
  >
    <!-- Левый блок - Расписание -->
    <div class="w-1/2 p-10 flex flex-col justify-center bg-gray-900 m-5 rounded-xl">
      <!-- Состояние: Идет пара -->
      <div v-if="currentStatus.type === 'class'">
        <h2 class="text-6xl font-bold mb-4">
          Идет {{ currentStatus.number }} пара
        </h2>
        <p class="text-4xl tabular-nums">
          Осталось: {{ currentStatus.remaining }}
        </p>
      </div>

      <!-- Состояние: Идет перемена -->
      <div v-else-if="currentStatus.type === 'break'">
        <h2 class="text-6xl font-bold mb-4">
          Перемена
        </h2>
        <p class="text-4xl tabular-nums">
          До начала {{ currentStatus.nextClassNumber }} пары: {{ currentStatus.untilNext }}
        </p>
      </div>

      <!-- Состояние: До начала пар -->
      <div v-else-if="currentStatus.type === 'before_classes'">
        <h2 class="text-5xl font-bold text-center mb-3">Пары еще не начались</h2>
        <p class="text-4xl tabular-nums text-center">
          До начала первой пары: {{ currentStatus.untilNext }}
        </p>
      </div>

      <!-- Состояние: Пары закончились -->
      <div v-else>
        <h2 class="text-5xl font-bold">
          Пары на сегодня закончены
        </h2>
      </div>
    </div>

    <!-- Правый блок - Дата и время -->
    <div class="w-1/2 bg-gray-800 p-10 flex flex-col justify-center items-center m-5 rounded-xl">
      <h1 class="text-8xl font-bold mb-4 tabular-nums">
        {{ currentTime }}
      </h1>
      <p class="text-4xl">
        {{ currentDate }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

// --- НОВОЕ: Состояние для эффекта сброса ---
const isResetting = ref(false);

// --- Дата и время ---
const now = ref(new Date());

const currentTime = computed(() => {
  return now.value.toLocaleTimeString('ru-RU', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
});

const currentDate = computed(() => {
  return now.value.toLocaleDateString('ru-RU', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

let timer;
// --- НОВОЕ: Переменная для таймера сброса ---
let resetInterval;

onMounted(() => {
  // Устанавливаем таймер для обновления времени каждую секунду
  timer = setInterval(() => {
    now.value = new Date();
  }, 1000);

  // --- НОВОЕ: Запускаем таймер для эффекта сброса ---
  // Срабатывает каждые 15 минут
  resetInterval = setInterval(() => {
    isResetting.value = !isResetting.value;
  }, 900000); // 15 минут = 900 000 мс
});

onUnmounted(() => {
  // Очищаем таймер при уничтожении компонента
  clearInterval(timer);
  // --- НОВОЕ: Очищаем таймер сброса ---
  clearInterval(resetInterval);
});

// --- Расписание на основе документа ---
const schedule = [
  { number: '1', start: '09:00', end: '10:30' },
  { number: '2', start: '10:40', end: '12:10' },
  { number: '3', start: '13:00', end: '14:30' },
  { number: '4', start: '14:40', end: '16:10' },
  { number: '5', start: '16:20', end: '17:50' },
  { number: '6', start: '18:00', end: '19:30' },
];

// Вспомогательная функция для преобразования времени 'ЧЧ:ММ' в объект Date
const parseTime = (timeStr) => {
  const [hours, minutes] = timeStr.split(':').map(Number);
  const date = new Date();
  date.setHours(hours, minutes, 0, 0);
  return date;
};

// Вспомогательная функция для форматирования миллисекунд в 'ЧЧ:ММ:СС'
const formatDuration = (ms) => {
  if (ms < 0) ms = 0;
  const totalSeconds = Math.floor(ms / 1000);
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  return [
    hours.toString().padStart(2, '0'),
    minutes.toString().padStart(2, '0'),
    seconds.toString().padStart(2, '0')
  ].join(':');
};

// Главная вычисляемая переменная, определяющая текущий статус
const currentStatus = computed(() => {
  const currentTime = now.value;
  const firstClassTime = parseTime(schedule[0].start);

  // Проверка, если еще до начала первой пары
  if (currentTime < firstClassTime) {
    const untilNextMs = firstClassTime - currentTime;
    return {
      type: 'before_classes',
      untilNext: formatDuration(untilNextMs)
    };
  }

  for (let i = 0; i < schedule.length; i++) {
    const lesson = schedule[i];
    const startTime = parseTime(lesson.start);
    const endTime = parseTime(lesson.end);

    // Проверка, идет ли сейчас пара
    if (currentTime >= startTime && currentTime < endTime) {
      const remainingMs = endTime - currentTime;
      return {
        type: 'class',
        number: lesson.number,
        remaining: formatDuration(remainingMs),
      };
    }

    // Проверка, идет ли сейчас перемена
    if (i < schedule.length - 1) {
      const nextLesson = schedule[i + 1];
      const nextStartTime = parseTime(nextLesson.start);
      if (currentTime >= endTime && currentTime < nextStartTime) {
        const untilNextMs = nextStartTime - currentTime;
        return {
          type: 'break',
          untilNext: formatDuration(untilNextMs),
          nextClassNumber: nextLesson.number,
        };
      }
    }
  }

  // Если ни одно из условий не выполнилось, значит пары закончились
  return { type: 'finished' };
});

</script>