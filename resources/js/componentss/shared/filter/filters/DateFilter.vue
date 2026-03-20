<template>

  <div class="mt-2">
    <VueDatePicker data-color="text-primary"
                   cancel-text="Назад"
                   select-text="Выбрать"
                   :model-value="date"
                   @update:model-value="filter"
                   :hide-input-icon="false"
                   placeholder="Выберете дату"
                   v-model="date" locale="ru"
                   range model-type="dd.MM.yyyy"
                   :enable-time-picker="false"
    >
      <template #input-icon>
        <BasicIcon class="input-slot-image w-4 h-4 ml-2 text-gray-600" name="calendar-date-range" />
      </template>
      <template #clear-icon="{ clear }">
        <BasicIcon class="input-slot-image w-4 h-4 mr-2 text-gray-600" @click="clear" name="delete" />
      </template>
    </VueDatePicker>
  </div>
</template>
<script>
import {Link} from "@inertiajs/vue3";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";
import {debounce} from "lodash";
import VueDatePicker from '@vuepic/vue-datepicker';


export default {
	name: "DateFilter",
	components: {
		BasicIcon,
		Link,
    VueDatePicker
	},
	data() {
		return {
			date: this.date_filter.value ?? null,
			}
	},
	methods: {
    filter: debounce(function () {
      let url = new URL(window.location.href);
      // Создаем массив для хранения всех ключей, которые нужно удалить
      const keysToDelete = [];

      // Перебираем все параметры и добавляем ключи, начинающиеся с 'budget', в массив
      for (const [key] of url.searchParams) {
        if (key.startsWith('date')) {
          keysToDelete.push(key);
        }
      }
      // Удаляем все ключи из массива
      keysToDelete.forEach(key => url.searchParams.delete(key));
      url.searchParams.delete('page');
      let newUrl = url.toString();
      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
        data: {
          date: this.date,
        },
      });
    }, 500),
    clearFilter() {
      let url = new URL(window.location.href);
      // Создаем массив для хранения всех ключей, которые нужно удалить
      const keysToDelete = [];

      // Перебираем все параметры и добавляем ключи, начинающиеся с 'budget', в массив
      for (const [key] of url.searchParams) {
        if (key.startsWith('date')) {
          keysToDelete.push(key);
        }
      }
      // Удаляем все ключи из массива
      keysToDelete.forEach(key => url.searchParams.delete(key));
      this.budgetEdu = [];
      let newUrl = url.toString();
      this.$inertia.visit(newUrl, {
        method: 'get',
        preserveState: true,
      });
    },
	},
	props: {
		date_filter: {
			type: Object,
		},
	},
}

</script>


<style>

:root {
  --dp-font-size: 0.875rem; /*Default font-size*/
  --dp-border-radius: 0.5rem; /*Configurable border-radius*/
  --dp-input-icon-padding: 30px !important; /*Padding on the left side of the input if icon is present*/
}
.dp--clear-btn {
  color: #0d120a;
}
</style>