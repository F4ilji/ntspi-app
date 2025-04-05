<template>
	<div class="mb-4 sm:mb-8">
		<label :for="block.data.name_field + '-id'" class="block mb-2 text-sm font-medium">{{ block.data.title_field }}</label>
		<div class="relative">
			<textarea
					v-model="textarea"
					:required="block.data.rules.required"
					:name="block.data.name_field"
					:minlength="block.data.rules.min"
					:maxlength="block.data.rules.max"
					type="text"
					:id="block.data.name_field + '-id'"
					:class="(error) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'focus:border-blue-500 focus:ring-blue-500'"
					class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none"
					:placeholder="block.data.title_field"
			/>
			<div v-if="error" class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
				<svg class="shrink-0 size-4 text-red-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="12" x2="12" y1="8" y2="12"></line>
					<line x1="12" x2="12.01" y1="16" y2="16"></line>
				</svg>
			</div>
		</div>
		<div class="flex items-center mt-2 justify-between flex-wrap">
			<p v-if="!error" class="text-sm text-gray-500" id="hs-input-helper-text">
				{{ block.data.description }}
			</p>
			<p class="text-sm text-primary">{{ textarea.length }} / {{ block.data.rules.max }}</p>
		</div>

		<p v-for="item in error" class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">{{ item }}</p>

	</div>

</template>

<script>
export default {
	name: "TextAreaBlock",
	data() {
		return {
			textarea: "",
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

<style scoped>

</style>

