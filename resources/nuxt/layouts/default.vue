<template>
	<v-app id="inspire"  standalone class="grey lighten-2">
		<vue-toolbar></vue-toolbar>
		<vue-left-navigation class="hidden-md-and-up"></vue-left-navigation>
		<v-content>
			<nuxt/>
		</v-content>
		<v-footer height="auto" absolute fixed color="white" app>
			<span>&copy; 2018 Dofuu Company</span>
		</v-footer>
	</v-app>
</template>

<script>
import Toolbar from '@/components/Toolbar'
import LeftNavigation from '@/components/LeftNavigation'
import { mapState } from "vuex";
import Cookies from 'js-cookie'
export default {
	components: {
		'vue-left-navigation': LeftNavigation,
		'vue-toolbar' : Toolbar
	},
	created: async function() {
		this.$store.dispatch("fetchCity").then(response => {
			if(response.status == 200) {
				if(typeof this.$route.params.city != 'undefined') {
					const city = this.$store.getters.getCityBySlug(this.$route.params.city)
					this.$store.dispatch('getCityCurrent', city.id)
				} else {
					if(typeof Cookies.get('flag_c') != 'undefined') {
						this.$store.dispatch('getCityCurrent', Cookies.get('flag_c'))
					} else {
						this.$store.dispatch('getCityCurrent', 10001)
					}
				}
			}
		});

		this.$store.dispatch("fetchType");	

	}
};
</script>

<style>

</style>
