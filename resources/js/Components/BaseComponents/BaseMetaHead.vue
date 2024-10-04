<template>

</template>

<script>
export default {
	name: "BaseMetaHead",
	data() {
		return {
		}
	},
	methods: {
	},
	props: {
		title: {
			type: String,
		},
		description: {
			type: String,
		},
		robots: {
			type: Array,
		},
		og_title: {
			type: String
		},
		og_description: {
			type: String
		},
		og_image: {
			type: String
		},
	},
}
</script>

<style scoped>

</style>