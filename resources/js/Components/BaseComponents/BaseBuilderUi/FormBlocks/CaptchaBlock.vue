<template>
	<div class="mb-4 sm:mb-8">
		<div class="relative">
			<YSmartCaptcha v-model="token" />
			<input
					v-model="token"
					:required="block.data.rules.required"
					:name="block.data.name_field"
					type="text"
					class="hidden"
			>
		</div>


	</div>


</template>

<script>
export default {
	name: "CaptchaBlock",
	data() {
		return {
			text: "",
			token: null,
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

