<!-- NavLink.vue -->
<template>
	<a :class="linkClass"
		 class="duration-150 block py-1 px-2 leading-[1.6] rounded-md"
		 :href="href">
		{{ label }}
	</a>
</template>

<script>
export default {
	name: 'PersonNavLink',
	props: {
		label: {
			type: String,
			required: true
		},
		href: {
			type: String,
			required: true
		},
		isActive: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		linkClass() {
			return {
				'translate-x-2 text-[#135aae]': this.isActive,
				'bg-transperant text-gray-600 hover:text-gray-900': !this.isActive
			};
		}
	}
}
</script>

<style scoped>
/* Добавьте стили, если это необходимо */
</style>