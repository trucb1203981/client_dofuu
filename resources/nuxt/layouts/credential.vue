<template>
	<v-app id="inspire" class="grey lighten-2">
		<v-toolbar color="white" dense fat class="elevation-0">
			<v-btn icon :to="{path: '/'}">
				<v-icon color="red darken-3">home</v-icon>
			</v-btn>
			<v-layout row wrap justify-center>
				<v-toolbar-title class="red--text text-accent-2 pt-2" :style="$vuetify.breakpoint.lgAndUp ? 'width: 200px': 'width: 200px'">		
					<a href="/">					
						<img src="/logo_page.png" alt="" >
					</a>
				</v-toolbar-title>
			</v-layout>			
		</v-toolbar>
		<v-content>
			<nuxt/>
		</v-content>
		<v-footer height="auto" absolute fixed color="white" app>
			<span>&copy; 2018 Dofuu Company</span>
		</v-footer>
	</v-app>
</template>

<script>
	export default {

	}
</script>

<style>
.card-radius {
	/*border-radius: 15px 15px 0 0;*/
	border-radius: 15px;
}
</style>