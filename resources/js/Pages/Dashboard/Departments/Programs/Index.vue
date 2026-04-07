<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-medium text-foreground">Образовательные программы</h3>
      <button
        type="button"
        @click="showAddModal = true"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить программу
      </button>
    </div>

    <!-- Programs Table -->
    <div class="border border-layer-line rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-line-2">
        <thead class="bg-muted/30">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Название программы
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Статус
            </th>
            <th class="px-4 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Действия
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-line-2 bg-layer">
          <tr v-for="program in programs.data" :key="program.id" class="hover:bg-muted/20">
            <td class="px-4 py-3 text-sm font-medium text-foreground">
              {{ program.name }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span :class="getStatusBadgeClass(program.status)">
                {{ getStatusLabel(program.status) }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <button
                type="button"
                @click="confirmDetach(program)"
                class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
                title="Открепить программу"
              >
                <DashboardIcon name="trash" size="4" />
              </button>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="programs.data.length === 0">
            <td colspan="3" class="px-4 py-12 text-center">
              <DashboardIcon name="book-open" size="12" class="mx-auto text-muted-foreground-1" />
              <p class="mt-2 text-sm text-muted-foreground-1">Нет образовательных программ</p>
              <p class="text-xs text-muted-foreground-1">Добавьте первую программу</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="closeModal"
    >
      <div class="bg-layer border border-layer-line rounded-lg shadow-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-layer-line flex items-center justify-between">
          <h3 class="text-lg font-medium text-foreground">
            Добавить программу
          </h3>
          <button
            type="button"
            @click="closeModal"
            class="p-1.5 text-muted-foreground-1 hover:text-foreground hover:bg-muted-hover rounded transition-all"
          >
            <DashboardIcon name="x-mark" size="5" />
          </button>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-foreground mb-1">
              Образовательная программа <span class="text-danger">*</span>
            </label>
            <select
              v-model="form.program_id"
              required
              class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            >
              <option :value="null">Выберите программу...</option>
              <option v-for="program in availablePrograms" :key="program.id" :value="program.id">
                {{ program.name }}
              </option>
            </select>
            <p class="mt-1 text-xs text-muted-foreground-1">Только опубликованные программы</p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-layer-line rounded-lg hover:bg-muted-hover transition-all"
            >
              Отмена
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Сохранение...' : 'Добавить' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardIcon from '../../Components/DashboardIcon.vue';

export default {
  name: 'DepartmentPrograms',
  components: {
    DashboardIcon,
  },
  props: {
    department: { type: Object, required: true },
    programs: { type: Object, required: true },
    availablePrograms: { type: Array, required: true },
  },
  data() {
    return {
      showAddModal: false,
      processing: false,
      form: {
        program_id: null
      }
    };
  },
  methods: {
    getStatusBadgeClass(status) {
      const classes = {
        published: 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-success/10 text-success border border-success/20',
        draft: 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-warning/10 text-warning border border-warning/20',
        archived: 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-muted text-muted-foreground-1 border border-layer-line',
      };
      return classes[status] || classes.draft;
    },
    getStatusLabel(status) {
      const labels = {
        published: 'Опубликована',
        draft: 'Черновик',
        archived: 'Архив',
      };
      return labels[status] || status;
    },
    confirmDetach(program) {
      if (confirm(`Вы уверены, что хотите открепить программу "${program.name}" от кафедры?`)) {
        this.detachProgram(program);
      }
    },
    detachProgram(program) {
      this.$inertia.delete(route('dashboard.departments.programs.detach', [this.department.id, program.id]), {
        preserveScroll: true,
        onSuccess: () => {
          // Остаемся на странице edit, flash message покажется автоматически
        }
      });
    },
    submit() {
      this.processing = true;

      this.$inertia.post(route('dashboard.departments.programs.attach', this.department.id), this.form, {
        preserveScroll: true,
        onSuccess: () => {
          this.processing = false;
          this.closeModal();
        },
        onError: () => {
          this.processing = false;
        }
      });
    },
    closeModal() {
      this.showAddModal = false;
      this.form = {
        program_id: null
      };
    }
  }
}
</script>
