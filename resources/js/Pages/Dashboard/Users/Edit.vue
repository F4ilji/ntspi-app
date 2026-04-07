<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="pencil-square" size="5" class="text-primary" />
    </template>
    <template #header-title>Редактирование пользователя</template>
    <template #header-subtitle>{{ user.name }}</template>
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
                  Новый пароль
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  minlength="8"
                  maxlength="255"
                  placeholder="Оставьте пустым, чтобы не менять"
                  class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p class="mt-1 text-xs text-muted-foreground-1">Минимум 8 символов</p>
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
            {{ processing ? 'Сохранение...' : 'Сохранить изменения' }}
          </button>
        </div>
      </form>
    </div>

    <!-- User Detail Section -->
    <div class="mt-6 bg-white border border-layer-line rounded-lg shadow-xs overflow-hidden">
      <div class="px-6 py-4 border-b border-layer-line">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-foreground">Детальная информация</h3>
          <a
            v-if="!user.user_detail"
            :href="route('dashboard.users.detail.create', user.id)"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
          >
            <DashboardIcon name="plus" size="4" />
            Добавить
          </a>
          <a
            v-else
            :href="route('dashboard.users.detail.edit', { user: user.id, userDetail: user.user_detail.id })"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
          >
            <DashboardIcon name="pencil-square" size="4" />
            Редактировать
          </a>
        </div>
      </div>

      <div class="p-6">
        <div v-if="user.user_detail" class="space-y-6">
          <!-- Photo -->
          <div v-if="user.user_detail.photo" class="flex items-center gap-4">
            <img
              :src="RESOLVE_ASSET_URL(user.user_detail.photo)"
              alt="Фото"
              class="w-20 h-20 rounded-full object-cover border border-layer-line"
            />
            <div>
              <span class="text-xs text-muted-foreground-1">Фото</span>
              <p class="text-sm text-foreground">{{ user.user_detail.photo.split('/').pop() }}</p>
            </div>
          </div>

          <!-- Contact Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-if="user.user_detail.contactEmail">
              <span class="text-xs text-muted-foreground-1">Контактный Email</span>
              <p class="text-sm text-foreground">{{ user.user_detail.contactEmail }}</p>
            </div>
            <div v-if="user.user_detail.contactPhone">
              <span class="text-xs text-muted-foreground-1">Контактный телефон</span>
              <p class="text-sm text-foreground">{{ user.user_detail.contactPhone }}</p>
            </div>
          </div>

          <!-- Academic Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-if="user.user_detail.academicTitle">
              <span class="text-xs text-muted-foreground-1">Ученая степень</span>
              <p class="text-sm text-foreground">{{ user.user_detail.academicTitle }}</p>
            </div>
            <div v-if="user.user_detail.AcademicDegree">
              <span class="text-xs text-muted-foreground-1">Ученое звание</span>
              <p class="text-sm text-foreground">{{ user.user_detail.AcademicDegree }}</p>
            </div>
          </div>

          <!-- Status -->
          <div v-if="user.user_detail.is_only_worker !== null">
            <span class="text-xs text-muted-foreground-1">Только работник</span>
            <p class="text-sm text-foreground">
              <span :class="STATUS_BADGE_CLASS(!user.user_detail.is_only_worker)">
                {{ user.user_detail.is_only_worker ? 'Да' : 'Нет' }}
              </span>
            </p>
          </div>

          <!-- Text Sections -->
          <div class="space-y-4">
            <div v-if="user.user_detail.education" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Образование</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.education) }}</div>
            </div>

            <div v-if="FORMAT_JSON_FIELD(user.user_detail.awards)" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Награды</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.awards) }}</div>
            </div>

            <div v-if="user.user_detail.professDisciplines" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Преподаваемые программы</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.professDisciplines) }}</div>
            </div>

            <div v-if="FORMAT_JSON_FIELD(user.user_detail.professionalRetraining)" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Профессиональная переподготовка</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.professionalRetraining) }}</div>
            </div>

            <div v-if="user.user_detail.professionalDevelopment" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Повышение квалификации</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.professionalDevelopment) }}</div>
            </div>

            <div v-if="user.user_detail.workExperience" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Стаж работы</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.workExperience) }}</div>
            </div>

            <div v-if="user.user_detail.attendedConferences" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Участие в конференциях</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.attendedConferences) }}</div>
            </div>

            <div v-if="user.user_detail.participationScienceProjects" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Участие в научных проектах</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.participationScienceProjects) }}</div>
            </div>

            <div v-if="user.user_detail.publications" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Публикации</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.publications) }}</div>
            </div>

            <div v-if="FORMAT_JSON_FIELD(user.user_detail.other)" class="bg-muted/30 rounded-lg p-4">
              <span class="text-xs font-medium text-muted-foreground-1 uppercase tracking-wide">Прочее</span>
              <div class="mt-2 text-sm text-foreground whitespace-pre-line">{{ FORMAT_JSON_FIELD(user.user_detail.other) }}</div>
            </div>
          </div>

          <!-- Metadata -->
          <div class="pt-4 border-t border-layer-line grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <span class="text-xs text-muted-foreground-1">Создан</span>
              <p class="text-sm text-foreground">{{ FORMAT_DATE(user.user_detail.created_at, 'full') }}</p>
            </div>
            <div>
              <span class="text-xs text-muted-foreground-1">Обновлён</span>
              <p class="text-sm text-foreground">{{ FORMAT_DATE(user.user_detail.updated_at, 'full') }}</p>
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-muted-foreground-1 text-center py-4">
          Детальная информация еще не добавлена
        </p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  name: 'UserEdit',
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
  },

  props: {
    user: {
      type: Object,
      required: true
    },
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
        name: this.user.name || '',
        email: this.user.email || '',
        password: '',
        roles: (this.user.roles || []).map(r => r.name)
      },
      processing: false,
      showRolesDropdown: false
    }
  },

  mounted() {
    this.SET_DOCUMENT_TITLE('Редактирование пользователя - ' + this.user.name);
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
      this.$inertia.put(route('dashboard.users.update', this.user.id), formData, {
        onFinish: () => {
          this.processing = false;
        }
      });
    }
  }
}
</script>
