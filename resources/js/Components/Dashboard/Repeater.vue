<template>
	<div>
		<div class="space-y-4">
			<div v-for="(block, index) in blocks" :key="index">
				<div class="flex gap-x-3 ">
					<slot name="block" :index="index" :block="block"></slot>
					<button class="hover:text-gray-500" @click="removeBlock(index)">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
						</svg>
					</button>
				</div>
			</div>
		</div>

		<button class="hover:text-gray-500 flex mx-auto mt-3 gap-x-1" @click="addBlock">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
			</svg>
			<span>Добавить день</span>
		</button>
	</div>
</template>

<script>
export default {
	name: "Repeater",

	data() {
		return {
			blocks: []
		}
	},
	methods: {
		addBlock() {
			// Добавление нового блока в массив blocks
			this.blocks.push({});
		},
		removeBlock(index) {
			// Удаление блока из массива blocks
			this.blocks.splice(index, 1);
		}
	}
}
</script>