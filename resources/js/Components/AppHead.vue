<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
	title: String,
	description: String,
	image: String,
})
</script>

<template>
	<Head :title="title ? `${title} - Нижнетагильский государственый-педагогический институт` : 'Нижнетагильский государственый-педагогический институт'">
		<meta name="description" :content="description">
		<meta name="robots" content="index, follow">
		<meta property="og:title" :content="title ? `${title} - Нижнетагильский государственый-педагогический институт` : 'Нижнетагильский государственый-педагогический институт'">
		<meta property="og:description" :content="description">
		<meta property="og:image" content="/img/thumbnail-1.png">
	</Head>
</template>