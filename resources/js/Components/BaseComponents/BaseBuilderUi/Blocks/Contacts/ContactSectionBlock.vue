<template>
<!--	<div v-if="loading" class="flex flex-col space-y-4">-->
<!--		<div class="group block rounded-xl overflow-hidden animate-pulse">-->
<!--			<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">-->
<!--				<div class="shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">-->
<!--					<div class="w-full h-full bg-gray-200"></div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
	<section class="bg-[#F5F5F5] w-full py-10">
		<div class="max-w-screen-xl md:flex justify-around w-full mx-auto px-4 py-[50px] flex-wrap">
			<ContactGroup v-for="contact in contacts.data.content"
					:title="contact.title"
					:items="contact.items"
			/>

		</div>
	</section>
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
			loading: false,
			contacts: null,
			contactItems: [
				{
					header: 'Главный корпус',
					details: [
						{text: '622031, Нижний Тагил, Красногвардейская 57', url: null},
					],
				},
				{
					header: 'Свяжитесь с нами',
					details: [
						{text: '(3435) 25-48-00', url: null},
						{text: 'office@ntspi.ru', url: 'mailto:office@ntspi.ru'},
					],
				},
			],
			admissionItems: [
				{
					header: 'Расписание',
					details: [
						{text: 'Понедельник - Пятница с 08.30 до 17.00', url: null},
					],
				},
				{
					header: 'Ответственный секретарь приемной комиссии',
					details: [
						{text: '+7(906)-802-55-59', url: null},
						{text: 'ntgspi@yandex.ru', url: 'mailto:ntgspi@yandex.ru'},
					]
				}
			],
			usefulItems: [
				{
					header: 'Полезное',
					details: [
						{text: 'Виртуальный тур', url: 'https://ntspi.ru/panorama/tour.html'},
					],
				},
			],
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
