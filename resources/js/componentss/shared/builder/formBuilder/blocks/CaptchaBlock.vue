<template>
  <div class="mb-4 sm:mb-8">
    <!-- Рендерим компонент только после того, как родитель примонтирован -->
    <YandexSmartCaptcha
        v-if="isMounted"
        :siteKey="yandexCaptchaKey"
        @onSuccess="handleSuccess"
        @onTokenExpired="handleTokenExpired"
    />

    <div class="relative">
      <input
          name="captcha"
          v-model="inputValue"
          required="required"
          type="text"
          class="hidden"
      />
    </div>
  </div>
</template>

<script>
import { YandexSmartCaptcha } from '@gladesinger/vue3-yandex-smartcaptcha';

export default {
  name: "CaptchaBlock",
  components: {
    YandexSmartCaptcha,
  },
  data() {
    return {
      yandexCaptchaKey: import.meta.env.VITE_YANDEX_CAPTCHA_KEY,
      token: null,
      inputValue: "",
      isMounted: false, // 1. Добавляем новый флаг
    };
  },
  mounted() {
    // 2. Хук mounted() вызывается, когда компонент вставлен в DOM.
    //    Теперь можно безопасно рендерить капчу.
    this.isMounted = true;
  },
  watch: {
    token(newToken) {
      if (newToken) {
        this.inputValue = "Капча успешно пройдена";
      } else {
        this.inputValue = "";
      }
    },
  },
  methods: {
    handleSuccess(token) {
      this.token = token;
    },
    handleTokenExpired() {
      this.token = null;
    }
  },
  props: {
    block: { type: Object },
    error: { type: Object },
  },
};
</script>