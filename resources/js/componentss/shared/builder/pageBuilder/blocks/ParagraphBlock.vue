<template>

	<div class="text-normal leading-7 font-light text-gray-600 md:text-[16px] md:text-[#374151] md:leading-8 md:font-normal paragraph-container" v-html="wrapTables(block).data.content" />
</template>

<script>
import slugify from "slugify";

export default {
	name: "ParagraphBlock",
	methods: {
		wrapTables(data) {
			if (data.type === 'paragraph' && data.data && data.data.content) {
				// Используем регулярное выражение для поиска всех таблиц
				const wrappedContent = data.data.content.replace(/<table([^>]*)>([\s\S]*?)<\/table>/g, (match, attrs, content) => {
					return `<div class="div-table"><table${attrs}>${content}</table></div>`;
				});

				// Возвращаем новый объект с обновленным контентом
				return {
					...data,
					data: {
						...data.data,
						content: wrappedContent
					}
				};
			}
			return data; // Если тип не 'paragraph', возвращаем объект без изменений
		}
	},
	props: {
		block: {
			type: Object,
		},
	},
}
</script>

<style>

.paragraph-container a {
	@apply text-primary-lighter;
	@apply underline;
}

.paragraph-container a:hover {
	@apply text-primary-dark;
	@apply underline;
}

.paragraph-container p {
	@apply mb-4
}

.paragraph-container ol li {
	@apply list-decimal list-inside
}

.paragraph-container ul li {
	@apply list-disc list-inside
}

.paragraph-container li ol {
	@apply ml-10
}

.paragraph-container ul {
	@apply mb-4
}

.paragraph-container hr {
	@apply my-4
}

.paragraph-container strong {
	@apply text-xl
}

.div-table {
	@apply overflow-x-auto
}


.paragraph-container table {
	@apply w-full border-collapse mt-4 mb-4 overflow-hidden; /* Ширина 100%, стыковка границ, отступы, закругленные края */
}

.paragraph-container th, .paragraph-container td {
	@apply border border-gray-300 p-3 text-left; /* Границы, отступы, выравнивание текста */
}

.paragraph-container th {
	@apply bg-gray-100 text-gray-800 font-semibold; /* Фон заголовка, цвет текста, жирный шрифт */
}

.paragraph-container tr {
	@apply transition-colors duration-200; /* Плавный переход цветов */
}


.paragraph-container tr:hover {
	@apply bg-gray-200; /* Фон строки при наведении */
}

.paragraph-container td {
	@apply text-gray-600; /* Цвет текста ячеек */
}





.paragraph-container tr:hover {
	@apply bg-gray-100;
}







</style>