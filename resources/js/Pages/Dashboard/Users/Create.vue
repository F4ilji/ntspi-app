<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="plus" size="5" class="text-primary" />
    </template>
    <template #header-title>Создание пользователя</template>
    <template #header-subtitle>Добавление нового пользователя в систему</template>
    <template #header-actions>
      <a
        :href="route('dashboard.users.index')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-surface text-foreground text-sm font-medium rounded-lg border border-layer-line hover:bg-muted-hover transition-all duration-200"
      >
        <DashboardIcon name="arrow-left" size="4" />
        Назад к списку
      </a>
    </template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Form Card -->
    <div class="bg-layer border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-6">
          <!-- Основные поля -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-foreground">Основная информация</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  ФИО <span class="text-danger">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  maxlength="255"
                  placeholder="Иванов Иван Иванович"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p v-if="errors.name" class="mt-1 text-sm text-danger">{{ errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Email <span class="text-danger">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  maxlength="255"
                  placeholder="example@ntspi.ru"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-danger">{{ errors.email }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Пароль <span class="text-danger">*</span>
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  required
                  minlength="8"
                  maxlength="255"
                  placeholder="Минимум 8 символов"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p v-if="errors.password" class="mt-1 text-sm text-danger">{{ errors.password }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-1">
                  Роль
                </label>
                <div class="relative" ref="rolesDropdownRef">
                  <button
                    type="button"
                    @click="showRolesDropdown = !showRolesDropdown"
                    class="w-full min-h-[42px] px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-left flex flex-wrap gap-1.5 items-center"
                  >
                    <span v-if="form.roles.length === 0" class="text-muted-foreground-1 text-sm">
                      Выберите роли
                    </span>
                    <span
                      v-for="role in form.roles"
                      :key="role"
                      class="inline-flex items-center gap-1 px-2 py-0.5 bg-primary/10 text-primary text-xs font-medium rounded"
                    >
                      {{ role }}
                      <button
                        type="button"
                        @click.stop="removeRole(role)"
                        class="text-primary hover:text-primary-hover"
                      >
                        ×
                      </button>
                    </span>
                  </button>

                  <div
                    v-if="showRolesDropdown"
                    class="absolute z-[100] w-full mt-1 bg-white border border-layer-line rounded-lg shadow-lg max-h-60 overflow-auto"
                  >
                    <label
                      v-for="role in roles"
                      :key="role.id"
                      class="flex items-center gap-3 px-3 py-2.5 hover:bg-muted-hover cursor-pointer transition-colors"
                    >
                      <input
                        type="checkbox"
                        :checked="form.roles.includes(role.name)"
                        @change="toggleRole(role.name)"
                        class="w-4 h-4 rounded border-layer-line text-primary focus:ring-primary focus:ring-offset-0"
                      />
                      <span class="text-sm text-foreground">{{ role.name }}</span>
                    </label>
                    <div v-if="roles.length === 0" class="px-3 py-4 text-sm text-muted-foreground-1 text-center">
                      Нет доступных ролей
                    </div>
                  </div>
                </div>
                <p class="mt-1 text-xs text-muted-foreground-1">Можно выбрать несколько ролей</p>
                <p v-if="errors.roles" class="mt-1 text-sm text-danger">{{ errors.roles }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-surface/50 border-t border-layer-line flex items-center justify-end gap-3">
          <a
            :href="route('dashboard.users.index')"
            class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-layer-line rounded-lg hover:bg-muted-hover transition-all"
          >
            Отмена
          </a>
          <button
            type="submit"
            :disabled="processing"
            class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ processing ? 'Создание...' : 'Создать пользователя' }}
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'UserCreate',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
  },

  props: {
    roles: {
      type: Array,
      required: true
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        roles: []
      },
      processing: false,
      showRolesDropdown: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Создание пользователя');
    document.addEventListener('click', this.handleClickOutside);
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },

  methods: {
    toggleRole(roleName) {
      const index = this.form.roles.indexOf(roleName);
      if (index === -1) {
        this.form.roles.push(roleName);
      } else {
        this.form.roles.splice(index, 1);
      }
    },

    removeRole(roleName) {
      const index = this.form.roles.indexOf(roleName);
      if (index !== -1) {
        this.form.roles.splice(index, 1);
      }
    },

    handleClickOutside(event) {
      if (this.$refs.rolesDropdownRef && !this.$refs.rolesDropdownRef.contains(event.target)) {
        this.showRolesDropdown = false;
      }
    },

    submit() {
      this.processing = true;
      const formData = {
        name: this.form.name,
        email: this.form.email,
        password: this.form.password,
        roles: [...this.form.roles]
      };
      this.$inertia.post(route('dashboard.users.store'), formData, {
        onFinish: () => {
          this.processing = false;
        }
      });
    }
  }
}
</script>
