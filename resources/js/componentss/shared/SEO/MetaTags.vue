<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
	seo: Object,
})
</script>

<template>
	<Head :title="seo?.title ? `${seo.title} - Нижнетагильский государственый-педагогический институт` : 'Нижнетагильский государственый-педагогический институт'">
		<meta name="description" :content="seo?.description ? `${seo.description} - Нижнетагильский государственый-педагогический институт` : 'Нижнетагильский государственый-педагогический институт'">
		<meta name="robots" content="index, follow">
		<meta property="og:title" :content="seo?.title ? `${seo.title} - Нижнетагильский государственый-педагогический институт` : 'Нижнетагильский государственый-педагогический институт'">
		<meta property="og:description" :content="seo?.description ? `${seo.description} - Нижнетагильский государственый-педагогический институт` : 'Нижнетагильский государственый-педагогический институт'">
		<meta property="og:image" content="/img/thumbnail-1.png">
	</Head>
</template>