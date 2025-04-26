<template>
	<div class="border-b">
			<div v-if="result !== null && result.length !== 0" class="relative px-3 pt-3 flex justify-start items-center overflow-x-auto whitespace-nowrap hide-scrollbar">
				<button @click="categorySort(null)"
								:class="{
  									'bg-blue-50': selectedCategory === null,
  									'text-primary': selectedCategory === null,
  									'border-primary-lighter': selectedCategory === null,
  									'animate-pulse': loading === true
									}"
								class="hover:bg-gray-50 duration-150 border border-[#EAEAEA] rounded font-light text-[13px] px-2 py-0.5 mr-2" type="button">Все</button>
				<template v-for="category in categories">
					<button @click="categorySort(category)"
									:class="{
  									'bg-blue-50': selectedCategory === category,
  									'text-primary': selectedCategory === category,
  									'border-primary-lighter': selectedCategory === category,
  									'animate-pulse': loading === true
									}"
									class="hover:bg-gray-50 duration-150 border border-[#EAEAEA] rounded font-light text-[13px] px-2 py-0.5 mr-2" type="button">
						{{ruCategories()[category] }}
					</button>
				</template>
			</div>
			<div class="relative px-1 py-2 flex justify-between items-center">
				<input autofocus="autofocus" autocomplete="off" @input="search" v-model="searchInput" type="text"
							 id="hs-as-table-product-review-search"
							 name="hs-as-table-product-review-search"
							 class="py-1 placeholder-[#8F8F8F] font-light px-2 block flex-1 w-full border-none text-lg focus:z-10 border-transparent focus:border-transparent focus:ring-0"
							 placeholder="Поиск по сайту">
        <button v-if='searchInput !== ""' @click="clearSearch" class="items-center flex justify-end px-2" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 hover:text-gray-300 text-gray-500 duration-150 font-bold">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
			</div>
		</div>
	<div class="overflow-y-auto flex-1">
		<ResultList class="h-full" :result="result" :loading="loading" :selected-category="selectedCategory" />
	</div>
	<nav v-if="paginate && result.length !== 0" class="bg-white border border-gray-100 flex items-center gap-x-1 fixed bottom-4 w-[200px] justify-center left-0 right-0 mx-auto py-2 rounded-xl " aria-label="Pagination">
		<button @click="prev(paginate.prev_page)" :disabled="!paginate.prev_page || loading === true" type="button" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-none disabled:opacity-50 disabled:pointer-events-none" aria-label="Previous">
			<svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="m15 18-6-6 6-6"></path>
			</svg>
			<span class="sr-only">Previous</span>
		</button>
		<div class="flex items-center gap-x-1">
			<span class="min-h-[38px] min-w-[38px] flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">{{ paginate.current_page }}</span>
			<span class="min-h-[38px] flex justify-center items-center text-gray-500 py-2 px-1.5 text-sm">из</span>
			<span class="min-h-[38px] flex justify-center items-center text-gray-500 py-2 px-1.5 text-sm">{{ paginate.last_page }}</span>
		</div>
		<button @click="next(paginate.next_page)" :disabled="!paginate.next_page || loading === true" type="button" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-none disabled:opacity-50 disabled:pointer-events-none" aria-label="Next">
			<span class="sr-only">Next</span>
			<svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="m9 18 6-6-6-6"></path>
			</svg>
		</button>
	</nav>
</template>

<script>
import {debounce} from "lodash";
import {Link} from "@inertiajs/vue3";
import {ruCategories} from "@/assets/data/ruCategories.js";
import ResultList from "@/componentss/features/search/components/ResultList.vue";
export default {
	name: "GlobalSearch",
	data() {
		return {
			searchInput: "",
			result: null,
			paginate: null,
			selectedCategory: null,
			categories: null,
			loading: false,
      debouncedSearch: debounce(this.executeSearch, 500),
      debouncedNext: debounce(this.executePagination, 300),
      debouncedPrev: debounce(this.executePagination, 300),
    }
	},
	components: {
		ResultList,
		Link,
	},
	methods: {
		ruCategories() {
			return ruCategories
		},
    categorySort: function(category) {
      this.toggleLoadingState(true);
      debounce(() => {
        axios({
          method: "get",
          url: route('client.search.index'),
          params: {
            search: this.searchInput.toLowerCase(),
            category: category
          }
        }).then((response) => {
          this.paginate = response.data.paginate;
          this.result = response.data.searchRes;
          this.categories = response.data.result_type;
          this.selectedCategory = response.data.selectedCategory;
          this.toggleLoadingState(false);
        }).catch((error) => {
          console.error("Ошибка при получении данных:", error);
          this.toggleLoadingState(false);
        });
      }, 500)();
    },

    search() {
      this.toggleLoadingState(true);
      this.debouncedSearch(this.searchInput.toLowerCase());
    },

    // Пагинация: следующая страница
    next(page) {
      this.toggleLoadingState(true);
      this.debouncedNext({ page, category: this.selectedCategory });
    },

    // Пагинация: предыдущая страница
    prev(page) {
      this.toggleLoadingState(true);
      this.debouncedPrev({ page, category: this.selectedCategory });
    },

    async executeSearch(searchValue) {
      try {
        const response = await axios.get(route('client.search.index'), {
          params: { search: searchValue }
        });
        this.updateResults(response);
      } finally {
        this.toggleLoadingState(false);
      }
    },

    // Общая функция для пагинации
    async executePagination(params) {
      try {
        const response = await axios.get(route('client.search.index'), {
          params: {
            search: this.searchInput.toLowerCase(),
            ...params
          }
        });
        this.updateResults(response);
      } finally {
        this.toggleLoadingState(false);
      }
    },

    // Обновление результатов
    updateResults(response) {
      this.paginate = response.data.paginate;
      this.result = response.data.searchRes;
      this.categories = response.data.result_type;
    },

		toggleLoadingState(state) {
			this.loading = state
		},

		textLimit(text, symbols) {
			if (text.length > symbols) {
				let LimitedText
				LimitedText = text.substring(0, symbols)
				return LimitedText + "..."
			}
			return text
		},

    clearSearch() {
      this.searchInput = ""
      this.search()
    }
	},

}
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
	display: none; /* Скрыть scrollbar для WebKit-браузеров */
}
.hide-scrollbar {
	-ms-overflow-style: none;  /* Для Internet Explorer и Edge */
	scrollbar-width: none;  /* Для Firefox */
}

</style>