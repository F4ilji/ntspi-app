<template>
	<div class="relative px-3 pt-3 flex justify-start items-center overflow-x-auto whitespace-nowrap hide-scrollbar">
		<button @click="categorySort(null)" :class="selectedCategory === null ? 'bg-blue-50 text-blue-500 border-blue-300' : ''" class="hover:bg-gray-50 duration-150 border border-[#EAEAEA] rounded font-light text-[13px] px-1 mr-2" type="button">Все</button>
		<template v-for="category in categories">
			<button @click="categorySort(category)"
							:class="{
  							'bg-blue-50': selectedCategory === category,
  							'text-blue-500': selectedCategory === category,
  							'border-blue-300': selectedCategory === category,
							}"
							class="hover:bg-gray-50 duration-150 border border-[#EAEAEA] rounded font-light text-[13px] px-1 mr-2" type="button">{{ category }}</button>
		</template>
	</div>
</template>

<script>
import {debounce} from "lodash";

export default {
	name: "CategorySearchList",
	data() {
		return {
			selectedCategory: null,
			categories: null,
			paginate: null,
			loading: false,
		}
	},
	methods: {
		categorySort: debounce(function (category) {
			this.loading = true
			this.selectedCategory = category; // Устанавливаем выбранную категорию
			this.$emit('update:selected-category', this.selectedCategory, this.categories, this.paginate); // Эмитируем событие
			axios({
				method: "get",
				url: route('client.search.index'),
				params: {
					search: this.search.toLowerCase(),
					category: category
				}
			}).then((response) => {
				this.paginate = response.data.paginate;
				this.result = response.data.searchRes;
				this.categories = response.data.result_type;
				this.loading = false;
			});
		}, 500),
	},
	props: {
		categories: {
			type: Array,
		},
		search: {
			type: String,
		}
	}
}
</script>

<style scoped>

</style>