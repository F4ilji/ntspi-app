<template>
	<div class="space-y-3">
		<h2 class="font-semibold text-primary text-lg">{{ title }}</h2>
		<div v-for="(item, index) in items" :key="index">
			<h3 class="font-semibold md:mb-2 mb-1">{{ item.header }}</h3>
			<div class="font-light">
				<p v-for="(detail, idx) in item.details" :key="idx">
          <span v-if="detail.url">
            <a :href="detail.url" class="text-blue-500 hover:underline">{{ detail.content }}</a>
          </span>
					<span v-else>
            {{ detail.content }}
          </span>
				</p>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		title: {
			type: String,
			required: true,
		},
		items: {
			type: Array,
			required: true,
		},
	},
};
</script>

<style scoped>
/* ваши стили, если необходимо */
</style>
