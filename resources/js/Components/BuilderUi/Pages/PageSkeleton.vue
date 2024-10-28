<template>

	<div class="animate-pulse">
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
		<div class="h-4 bg-gray-300 rounded w-full mb-4"></div>
	</div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
	name: "PageSkeleton",
	components: { Link },
	props: {
		header: {
			type: String,
		},
	},
}
</script>

<style scoped>
</style>

Найти еще