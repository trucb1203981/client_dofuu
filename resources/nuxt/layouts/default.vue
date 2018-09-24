<template>
	<v-app id="app" standalone class="grey lighten-2">
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
		data() {
			return {
				tracking: false
			}
		},
		methods: {
			trackingLocation: function() {
				var vm = this
				if(navigator.geolocation) {					
					navigator.geolocation.watchPosition(function(position) {
						vm.tracking = true
					},
					function (error) { 
						if (error.code == error.PERMISSION_DENIED) vm.tracking = false
					});
				} else {
					console.log("Geolocation is not supported by this browser.")
				}
			}
		},
		watch: {
			'tracking': function(val) {
				if(val) {
					this.$store.dispatch('currentLocation')
				}
			}
		},
		created: function() {			

			this.$store.dispatch("fetchCity").then(response => {
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
			this.trackingLocation()
		}
	};
</script>

<style>
.card-radius {
	border-radius: 18px;
}
.btn-custom.v-text-field.v-text-field--solo .v-input__control {
	min-height: 30px !important;
	top: 4px
}
.btn-custom.theme--light.v-text-field--solo .v-input__slot {
	border-radius: 18px;
}
</style>
