<template>
	<div v-if="loading" class="flex flex-col space-y-4">
		<div class="group block rounded-xl overflow-hidden animate-pulse">
			<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
				<div class="shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">
					<div class="w-full h-full bg-gray-200"></div>
				</div>
			</div>
		</div>
	</div>
	<div v-else>
		<section class="bg-[#F5F5F5] w-full py-10">
			<div class="max-w-screen-xl md:flex justify-around w-full mx-auto px-4 md:py-[50px] flex-wrap space-y-7 md:space-y-0">
							<ContactGroup v-for="contact in contacts.data.content"
									:title="contact.title"
									:items="contact.items"
							/>
			</div>
		</section>
	</div>
</template>

<script>
import ContactGroup from './ContactGroup.vue';
import axios from "axios";

export default {
	components: {
		ContactGroup,
	},
	data() {
		return {
			loading: true,
			contacts: null,
		};
	},
	methods: {
		getContact(id) {
			axios.get(route('client.widget.contact.show', id))
					.then(response => {
						this.contacts = response.data;
						this.loading = false; // Установить состояние загрузки в false
					})
					.catch(error => {
						console.error('Ошибка:', error);
					});
		},
	},
	mounted() {
		const id = this.block?.data.contact || this.contactId
		this.getContact(id);
	},
	props: {
		block: {
			type: Object,
		},
		contactId: {
			type: String,
			default: null,
		}
	},
};
</script>

<style scoped>
/* ваши стили, если необходимо */
</style>
