<template>
	<div class="sm:col-span-1 w-full">
		<label for="hs-as-table-product-review-search" class="sr-only">Поиск</label>
		<div class="relative">
			<input autocomplete="off" @input="search" v-model="searchInput" type="text"
				   id="hs-as-table-product-review-search"
				   name="hs-as-table-product-review-search"
				   class="py-3 px-3 pl-11 block flex-1 w-full border-gray-200 shadow-sm rounded-md text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400"
				   placeholder="Поиск новостей">
			<div
					class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-4">
				<svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
					 width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
					<path
							d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
				</svg>
			</div>
		</div>
	</div>
</template>

<script>
import { debounce } from "lodash";

export default {
	name: "PostListSearch",
	data() {
		return {
			searchInput: this.search_filter.value,
		}
	},
	methods: {
		search: debounce(function () {
            if(this.searchInput === "") {
                let url = new URL(window.location.href);
                url.searchParams.delete('search');
                let newUrl = url.toString();
                this.$inertia.visit(newUrl,{
				          method: 'get',
				          preserveState: true,
				          replace: true,
			          });
            } else {
                let url = new URL(window.location.href);
                url.searchParams.delete('page');
                let newUrl = url.toString();
			this.$inertia.visit(newUrl,{
				method: 'get',
				data: {
					search: this.searchInput,
				},
				preserveState: true,
				replace: true,
			})
            }
		}, 500),
        
	},
	props: [
		'search_filter'
	],

}
</script>

<style scoped>

</style>