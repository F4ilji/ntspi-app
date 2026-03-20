<template>
	<div class="mb-4 sm:mb-8">
		<div class="">
				<input
						type="checkbox"
						required="required"
						class="shrink-0 mb-0.5 border-gray-200 rounded text-primary focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
						id="personal-data-id">
			<label for="personal-data-id" class="text-sm text-gray-500 ms-2">
        Согласно Федеральному закону от 27.07.2006 № 152-ФЗ "О персональных данных", даю свое согласие на обработку персональных данных.
      </label>
		</div>
	</div>


</template>

<script>
export default {
	name: "PersonalDataBlock",
	data() {
		return {
			text: "",
		}
	},
	methods: {},
	props: {
	},
}
</script>

