<template>
	<div>
		<template v-for="(step, index) in block.data.steps">
			<div class="flex gap-x-3">
				<div class="w-16 text-end min-w-[4rem]">
					<span class="text-xs text-gray-500">{{ block.data.step_name }} {{ index + 1 }}</span>
				</div>
				<div class="relative last:after:hidden after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200">
					<div class="relative z-10 size-7 flex justify-center items-center">
						<div class="size-2 rounded-full bg-primary"></div>
					</div>
				</div>
				<div class="grow max-w-[70%] pt-0.5 pb-8 overflow-wrap break-words">
					<h3 class="flex gap-x-1.5 font-semibold text-gray-800">
						{{ step.title }}
					</h3>
					<p class="mt-1 text-sm text-gray-600 step-content" v-html="step.content" />
				</div>
			</div>

		</template>
	</div>
</template>

<script>
import slugify from "slugify";


export default {
	name: "StepperBlock",
	data() {
		return {
			toggler: false,
			domainPath: null,
		}
	},
	methods: {
		generateSlug: function (text) {
			return slugify(text, {
				lower: true,
				strict: true,
				locale: 'ru'
			});
		},
	},
	mounted() {
		this.domainPath = window.location.origin;

	},
	props: {
		block: {
			type: Object,
		},
	},
}
</script>

<style>

.step-content a {
	@apply text-primary-lighter;
	@apply underline;
}

.step-content a:hover {
	@apply text-primary-dark;
	@apply underline;
}


.step-content ol li {
	@apply list-decimal list-inside
}

.step-content ul li {
	@apply list-disc list-inside
}

.step-content li ol {
	@apply ml-10
}
</style>