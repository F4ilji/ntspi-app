<template>

	<div class="mt-6 grid">
		<button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">{{ block }}</button>
	</div>

</template>

<script>
import slugify from "slugify";

export default {
	name: "SubmitBlock",
	methods: {
	},
	props: {
		block: {
			type: Object,
		},
	},
}
</script>

<style>








</style>