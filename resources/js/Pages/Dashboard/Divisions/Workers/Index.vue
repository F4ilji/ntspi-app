<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-medium text-foreground">Сотрудники</h3>
      <button
        type="button"
        @click="showAddModal = true"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
      >
        <DashboardIcon name="plus" size="4" />
        Добавить сотрудника
      </button>
    </div>

    <!-- Workers Table -->
    <div class="border border-layer-line rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-line-2">
        <thead class="bg-muted/30">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              ФИО
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Должность
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Почта
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Телефон
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Кабинет
            </th>
            <th class="px-4 py-3 text-right text-xs font-medium text-muted-foreground-1 uppercase tracking-wider">
              Действия
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-line-2 bg-layer">
          <tr v-for="worker in workers.data" :key="worker.id" class="hover:bg-muted/20">
            <td class="px-4 py-3 text-sm font-medium text-foreground">
              {{ worker.name }}
            </td>
            <td class="px-4 py-3 text-sm text-foreground">
              {{ worker.pivot.administrativePosition }}
            </td>
            <td class="px-4 py-3 text-sm text-foreground">
              {{ worker.pivot.service_email || '—' }}
            </td>
            <td class="px-4 py-3 text-sm text-foreground">
              {{ worker.pivot.service_phone || '—' }}
            </td>
            <td class="px-4 py-3 text-sm text-foreground">
              {{ worker.pivot.cabinet || '—' }}
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1">
                <button
                  type="button"
                  @click="editWorker(worker)"
                  class="p-1.5 text-muted-foreground-1 hover:text-primary hover:bg-primary/10 rounded transition-all"
                  title="Редактировать"
                >
                  <DashboardIcon name="pencil-square" size="4" />
                </button>
                <button
                  type="button"
                  @click="confirmDetach(worker)"
                  class="p-1.5 text-muted-foreground-1 hover:text-danger hover:bg-danger/10 rounded transition-all"
                  title="Удалить"
                >
                  <DashboardIcon name="trash" size="4" />
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="workers.data.length === 0">
            <td colspan="6" class="px-4 py-12 text-center">
              <DashboardIcon name="user-group" size="12" class="mx-auto text-muted-foreground-1" />
              <p class="mt-2 text-sm text-muted-foreground-1">Нет сотрудников</p>
              <p class="text-xs text-muted-foreground-1">Добавьте первого сотрудника</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal || editingWorker"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="closeModal"
    >
      <div class="bg-layer border border-layer-line rounded-lg shadow-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-layer-line flex items-center justify-between">
          <h3 class="text-lg font-medium text-foreground">
            {{ editingWorker ? 'Редактировать сотрудника' : 'Добавить сотрудника' }}
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
          <div v-if="!editingWorker">
            <label class="block text-sm font-medium text-foreground mb-1">
              Сотрудник <span class="text-danger">*</span>
            </label>
            <select
              v-model="form.user_id"
              required
              class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            >
              <option :value="null">Выберите сотрудника...</option>
              <option v-for="user in availableUsers" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-foreground mb-1">
              Административная должность <span class="text-danger">*</span>
            </label>
            <input
              v-model="form.administrativePosition"
              type="text"
              required
              maxlength="255"
              placeholder="Например: Руководитель отдела"
              class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-foreground mb-1">
                Рабочая почта
              </label>
              <input
                v-model="form.service_email"
                type="email"
                maxlength="255"
                placeholder="example@university.ru"
                class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-foreground mb-1">
                Рабочий телефон
              </label>
              <input
                v-model="form.service_phone"
                type="text"
                maxlength="20"
                placeholder="+7 (XXX) XXX-XX-XX"
                class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-foreground mb-1">
              Кабинет
            </label>
            <input
              v-model="form.cabinet"
              type="text"
              maxlength="10"
              placeholder="Например: 305а"
              class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
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
              {{ processing ? 'Сохранение...' : 'Сохранить' }}
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
  name: 'DivisionWorkers',
  components: {
    DashboardIcon,
  },
  props: {
    division: { type: Object, required: true },
    workers: { type: Object, required: true },
    availableUsers: { type: Array, required: true },
  },
  data() {
    return {
      showAddModal: false,
      editingWorker: null,
      processing: false,
      form: {
        user_id: null,
        administrativePosition: '',
        service_email: '',
        service_phone: '',
        cabinet: ''
      }
    };
  },
  methods: {
    editWorker(worker) {
      this.editingWorker = worker;
      this.form = {
        user_id: worker.id,
        administrativePosition: worker.pivot.administrativePosition,
        service_email: worker.pivot.service_email || '',
        service_phone: worker.pivot.service_phone || '',
        cabinet: worker.pivot.cabinet || ''
      };
    },
    confirmDetach(worker) {
      if (confirm(`Вы уверены, что хотите удалить "${worker.name}" из сотрудников подразделения?`)) {
        this.detachWorker(worker);
      }
    },
    detachWorker(worker) {
      this.$inertia.delete(route('dashboard.divisions.workers.detach', [this.division.id, worker.id]), {
        preserveScroll: true,
        onSuccess: () => {
          // Остаемся на странице edit, flash message покажется автоматически
        }
      });
    },
    submit() {
      this.processing = true;
      
      const url = this.editingWorker
        ? route('dashboard.divisions.workers.update', [this.division.id, this.editingWorker.id])
        : route('dashboard.divisions.workers.attach', this.division.id);
      
      const method = this.editingWorker ? 'put' : 'post';
      
      this.$inertia[method](url, this.form, {
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
      this.editingWorker = null;
      this.form = {
        user_id: null,
        administrativePosition: '',
        service_email: '',
        service_phone: '',
        cabinet: ''
      };
    }
  }
}
</script>
