<template>
	<div>
		<label class="block mb-3 text-sm font-medium">{{ block.data.title_field }}</label>
		<div class="space-y-2">
			<div v-for="column in block.data.columns" class="flex">
				<input
						type="checkbox"
						:name="block.data.name_field + '[]'"
						:value="column.name_field"
						class="shrink-0 mt-0.5 border-gray-200 rounded text-primary focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
						:id="column.name_field + '-id'">
				<label :for="column.name_field + '-id'" class="text-sm text-gray-500 ms-3">{{ column.title_field }}</label>
			</div>
		</div>
		<p v-if="!error" class="mt-2 text-sm text-gray-500" id="hs-input-helper-text">{{ block.data.description }}</p>
		<p v-for="item in error" class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">{{ item }}</p>

	</div>

</template>

<script>
export default {
	name: "MultipleChoiceBlock",
	data() {
		return {
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

