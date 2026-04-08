<template>
  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="arrow-down-tray" size="5" class="text-primary" />
    </template>
    <template #header-title>Обновления VIKON</template>
    <template #header-subtitle>Управление обновлениями модулей</template>

    <!-- Flash Messages -->
    <FlashMessages />

    <!-- Not Authenticated State -->
    <div v-if="!isAuthenticated" class="bg-layer border border-layer-line rounded-lg shadow-xs p-8">
      <div class="text-center">
        <DashboardIcon name="shield-exclamation" size="16" class="text-muted-foreground-1 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-foreground mb-2">Требуется авторизация VIKON</h3>
        <p class="text-sm text-muted-foreground-1 mb-6">
          Для управления обновлениями необходимо войти через систему VIKON
        </p>
        <a
          :href="vikonAuthUrl"
          class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md"
        >
          <DashboardIcon name="arrow-right-on-rectangle" size="5" />
          Войти через VIKON
        </a>
      </div>
    </div>

    <!-- Authenticated State -->
    <div v-else class="space-y-6">
      <!-- Version Info & Update Button -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Current Version -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-6">
          <div class="flex items-center gap-3 mb-4">
            <DashboardIcon name="information-circle" size="6" class="text-primary" />
            <div>
              <h3 class="text-sm font-medium text-foreground">Текущая версия</h3>
              <p class="text-2xl font-semibold text-foreground mt-1">{{ currentVersion }}</p>
            </div>
          </div>

          <div v-if="versionInfo.has_update" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-2">
              <DashboardIcon name="exclamation-triangle" size="5" class="text-yellow-600 mt-0.5" />
              <div>
                <p class="text-sm font-medium text-yellow-800">Доступно обновление</p>
                <p class="text-xs text-yellow-700 mt-1">Версия: {{ versionInfo.latest_version }}</p>
              </div>
            </div>
          </div>

          <div v-else-if="versionInfo.latest_version" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start gap-2">
              <DashboardIcon name="check-circle" size="5" class="text-green-600 mt-0.5" />
              <div>
                <p class="text-sm font-medium text-green-800">Установлена последняя версия</p>
              </div>
            </div>
          </div>

          <button
            @click="checkVersion"
            :disabled="checkingVersion"
            class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-surface border border-layer-line text-sm font-medium rounded-lg hover:bg-muted-hover transition-all duration-200 disabled:opacity-50"
          >
            <DashboardIcon v-if="checkingVersion" name="arrow-path" size="4" class="animate-spin" />
            <DashboardIcon v-else name="magnifying-glass" size="4" />
            {{ checkingVersion ? 'Проверка...' : 'Проверить наличие обновлений' }}
          </button>
        </div>

        <!-- Access Status -->
        <div class="bg-layer border border-layer-line rounded-lg shadow-xs p-6">
          <div class="flex items-center gap-3 mb-4">
            <DashboardIcon name="shield-check" size="6" class="text-primary" />
            <div>
              <h3 class="text-sm font-medium text-foreground">Статус доступа</h3>
              <p class="text-sm text-muted-foreground-1 mt-1">
                {{ accessInfo.has_access ? 'Доступ разрешён' : 'Доступ запрещён' }}
              </p>
            </div>
          </div>

          <div v-if="accessInfo.error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
              <DashboardIcon name="x-circle" size="5" class="text-red-600 mt-0.5" />
              <p class="text-sm text-red-800">{{ accessInfo.error }}</p>
            </div>
          </div>

          <button
            @click="checkAccess"
            :disabled="checkingAccess"
            class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-surface border border-layer-line text-sm font-medium rounded-lg hover:bg-muted-hover transition-all duration-200 disabled:opacity-50"
          >
            <DashboardIcon v-if="checkingAccess" name="arrow-path" size="4" class="animate-spin" />
            <DashboardIcon v-else name="shield-check" size="4" />
            {{ checkingAccess ? 'Проверка...' : 'Проверить права доступа' }}
          </button>
        </div>
      </div>

      <!-- Module Updates -->
      <div class="bg-layer border border-layer-line rounded-lg shadow-xs">
        <div class="px-6 py-4 border-b border-layer-line">
          <h3 class="text-sm font-medium text-foreground">Модули для обновления</h3>
        </div>

        <div class="divide-y divide-line-2">
          <div v-for="(module, id) in modules" :key="id" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <DashboardIcon name="cube" size="6" class="text-primary" />
              <div>
                <p class="text-sm font-medium text-foreground">{{ module.name }}</p>
                <p class="text-xs text-muted-foreground-1">ID: {{ id }}</p>
              </div>
            </div>

            <button
              @click="downloadUpdate(id)"
              :disabled="updating || !accessInfo.has_access"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <DashboardIcon v-if="updating" name="arrow-path" size="4" class="animate-spin" />
              <DashboardIcon v-else name="arrow-down-tray" size="4" />
              {{ updating ? 'Обновление...' : 'Обновить' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Progress Bar -->
      <div v-if="updating" class="bg-layer border border-layer-line rounded-lg shadow-xs p-6">
        <div class="flex items-center gap-3 mb-3">
          <DashboardIcon name="arrow-path" size="5" class="text-primary animate-spin" />
          <p class="text-sm font-medium text-foreground">Выполняется обновление...</p>
        </div>
        <div class="w-full bg-muted rounded-full h-2.5">
          <div
            class="bg-primary h-2.5 rounded-full transition-all duration-300"
            :style="{ width: progress + '%' }"
          ></div>
        </div>
        <p class="text-xs text-muted-foreground-1 mt-2">{{ progress }}%</p>
      </div>

      <!-- Update Error -->
      <div v-if="updateError" class="bg-layer border border-red-200 rounded-lg shadow-xs p-6">
        <div class="flex items-start gap-3">
          <DashboardIcon name="x-circle" size="6" class="text-red-600" />
          <div>
            <h4 class="text-sm font-medium text-red-800">Ошибка обновления</h4>
            <p class="text-sm text-red-700 mt-1">{{ updateError }}</p>
          </div>
        </div>
      </div>

      <!-- Logout Button -->
      <div class="flex justify-end">
        <button
          @click="logout"
          class="inline-flex items-center gap-2 px-4 py-2 bg-surface border border-layer-line text-sm font-medium rounded-lg hover:bg-muted-hover transition-all duration-200"
        >
          <DashboardIcon name="arrow-right-on-rectangle" size="4" />
          Выйти из VIKON
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../Components/DashboardLayout.vue';
import DashboardIcon from '../Components/DashboardIcon.vue';
import FlashMessages from '../Components/shared/FlashMessages.vue';

export default {
  components: {
    DashboardLayout,
    DashboardIcon,
    FlashMessages,
  },

  props: {
    is_authenticated: {
      type: Boolean,
      default: false,
    },
    current_version: {
      type: String,
      default: '',
    },
    modules: {
      type: Object,
      default: () => ({}),
    },
    vikon_auth_domain: {
      type: String,
      default: 'https://auth.db-nica.ru/',
    },
    vikon_client_id: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      isAuthenticated: this.is_authenticated,
      currentVersion: this.current_version,
      checkingVersion: false,
      checkingAccess: false,
      updating: false,
      progress: 0,
      updateError: null,
      versionInfo: {
        current_version: this.current_version,
        has_update: false,
        latest_version: null,
      },
      accessInfo: {
        has_access: false,
        error: null,
        permissions: [],
      },
    };
  },

  computed: {
    vikonAuthUrl() {
      const redirectUri = encodeURIComponent(route('dashboard.vikon-updates.index'));
      return `${this.vikon_auth_domain}oauth2/authorize?client_id=${this.vikon_client_id}&redirect_uri=${redirectUri}&response_type=code`;
    },
  },

  mounted() {
    if (this.isAuthenticated) {
      this.checkAccess();
      this.checkVersion();
    }

    // Check for OAuth code in URL
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    if (code) {
      this.authenticate(code);
    }
  },

  methods: {
    async authenticate(code) {
      try {
        const response = await this.$inertia.post(route('dashboard.vikon-updates.authenticate'), {
          code: code,
          redirect_uri: window.location.origin + window.location.pathname,
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.isAuthenticated = true;
            this.checkAccess();
            this.checkVersion();
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
          },
          onError: (errors) => {
            this.$inertia.visit(route('dashboard.vikon-updates.index'), {
              preserveState: true,
              preserveScroll: true,
            });
          },
        });
      } catch (error) {
        console.error('Authentication failed:', error);
      }
    },

    async checkAccess() {
      this.checkingAccess = true;
      try {
        const response = await axios.post(route('dashboard.vikon-updates.check-access'));
        this.accessInfo = {
          has_access: response.data.has_access,
          error: response.data.error,
          permissions: response.data.permissions || [],
        };
      } catch (error) {
        console.error('Access check failed:', error);
        this.accessInfo = {
          has_access: false,
          error: 'Не удалось проверить права доступа',
          permissions: [],
        };
      } finally {
        this.checkingAccess = false;
      }
    },

    async checkVersion() {
      this.checkingVersion = true;
      try {
        const response = await axios.post(route('dashboard.vikon-updates.check-version'));
        this.versionInfo = response.data;
      } catch (error) {
        console.error('Version check failed:', error);
      } finally {
        this.checkingVersion = false;
      }
    },

    async downloadUpdate(moduleId) {
      if (!confirm('Вы уверены, что хотите обновить этот модуль?')) {
        return;
      }

      this.updating = true;
      this.progress = 0;
      this.updateError = null;

      // Simulate progress
      const progressInterval = setInterval(() => {
        if (this.progress < 90) {
          this.progress += 10;
        }
      }, 500);

      try {
        const response = await axios.post(route('dashboard.vikon-updates.download-update'), {
          module_id: moduleId,
        });

        this.progress = 100;
        clearInterval(progressInterval);

        if (response.data.success) {
          setTimeout(() => {
            this.$inertia.visit(route('dashboard.vikon-updates.index'), {
              preserveState: true,
              preserveScroll: true,
            });
          }, 500);
        } else {
          this.updateError = response.data.message || 'Неизвестная ошибка';
          this.updating = false;
          this.progress = 0;
        }
      } catch (error) {
        clearInterval(progressInterval);
        this.updating = false;
        this.progress = 0;
        this.updateError = error.response?.data?.message || 'Произошла ошибка при обновлении';
      }
    },

    async logout() {
      try {
        await axios.post(route('dashboard.vikon-updates.logout'));
        this.isAuthenticated = false;
        this.accessInfo = {
          has_access: false,
          error: null,
          permissions: [],
        };
        this.versionInfo = {
          current_version: this.currentVersion,
          has_update: false,
          latest_version: null,
        };
      } catch (error) {
        console.error('Logout failed:', error);
      }
    },
  },
};
</script>
