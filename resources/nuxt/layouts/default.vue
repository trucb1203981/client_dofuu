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
		await this.$store.dispatch("fetchCity").then(response => {
			if(response.status == 200) {
				if(typeof this.$route.params.city != 'undefined') {
					const city = this.$store.getters.getCityBySlug(this.$route.params.city)
					this.$store.dispatch('getCityCurrent', city.id)
				} else {
					if(typeof Cookies.get('flag_c') != 'undefined') {

						setTimeout(() => {
							this.$store.dispatch('getCityCurrent', Cookies.get('flag_c'))
						}, 100)
						
					} else {

						setTimeout(() => {
							this.$store.dispatch('getCityCurrent', 10001)
						}, 100)
						
					}
				}
			}
		});
		this.$store.dispatch("fetchType");
	},
	mounted: function() {
		this.$store.dispatch("currentLocation").then(response => {
			
		})
	}
};
</script>

<style>
.card-radius {
	/*border-radius: 15px 15px 0 0;*/
	border-radius: 15px;
}
.btn-custom.v-text-field.v-text-field--solo .v-input__control {
	min-height: 30px !important;
	top: 4px
}
.btn-custom.theme--light.v-text-field--solo .v-input__slot {
	border-radius: 15px;
}
</style>
