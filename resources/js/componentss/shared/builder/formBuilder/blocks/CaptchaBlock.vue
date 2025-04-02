<template>
	<div class="mb-4 sm:mb-8">
		<div class="relative">
			<!-- Компонент капчи -->
			<YSmartCaptcha v-model="token" />
			<!-- Скрытое поле ввода -->
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
export default {
	name: "CaptchaBlock",
	data() {
		return {
			token: null, // Токен капчи
			inputValue: "", // Значение, которое будет отправлено в input
		};
	},
	watch: {
		// Отслеживаем изменения токена
		token(newToken) {
			if (newToken) {
				// Если токен получен, считаем капчу успешно пройденной
				this.inputValue = "Капча успешно пройдена";
			} else {
				// Если токен сброшен, очищаем поле
				this.inputValue = "";
			}
		},
	},
	props: {
		block: {
			type: Object,
		},
		error: {
			type: Object,
		},
	},
};
</script>