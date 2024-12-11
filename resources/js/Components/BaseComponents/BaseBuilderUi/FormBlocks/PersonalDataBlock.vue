<template>
	<div class="mb-4 sm:mb-8">
		<label class="block mb-3 text-sm font-medium">{{ block.data.title_field }}</label>
		<div class="">
				<input
						type="checkbox"
						:name="block.data.name_field"
						:value="block.data.name_field"
						name="hs-default-radio"
						class="shrink-0 mb-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
						:id="block.data.name_field + '-id'">
			<label :for="block.data.name_field + '-id'" class="text-sm text-gray-500 ms-2">Даю свое согласие на сбор, обработку, хранение и использование персональных данных в соответствии с <a class="underline text-primaryBlue" target="_blank" href="https://ntspi.ru/upload/824_%D0%9E%D0%B1_%D1%83%D1%82%D0%B2_%D0%9F%D0%BE%D0%BB%D0%B8%D1%82_%D0%BE%D0%B1%D1%80_%D0%B7%D0%B0%D1%89%D0%B8%D1%82_%D0%BF%D0%B5%D1%80%D1%81_%D0%B4%D0%B0%D0%BD.pdf">Пользовательским соглашением</a> </label>
		</div>
		<p v-if="!error" class="mt-2 text-sm text-gray-500" id="hs-input-helper-text">{{ block.data.description }}</p>
		<p v-for="item in error" class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">{{ item }}</p>
	</div>


</template>

<script>
export default {
	name: "PersonalDataBlock",
	data() {
		return {
			text: "",
		}
	},
	methods: {},
	props: {
		block: {
			type: Object,
		},
		error: {
			type: Object,
		}

	},
}
</script>

