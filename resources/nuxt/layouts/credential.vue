<template>
	<v-app id="inspire" class="grey lighten-2">
		<v-toolbar color="white" dense fat class="elevation-0">
			<vue-logo />	
		</v-toolbar>
		<v-content>
			<nuxt/>
		</v-content>
		<vue-footer/>
	</v-app>
</template>

<script>
	import Logo from '@/components/Logo'
	import Footer from '@/components/Footer'
	export default {
		components: {
			'vue-footer': Footer,
			'vue-logo': Logo
		},
	}
</script>

<style>
.card-radius {
	/*border-radius: 15px 15px 0 0;*/
	border-radius: 15px;
}
</style>