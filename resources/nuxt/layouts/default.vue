<template>
	<v-app id="app" standalone class="grey lighten-2">
		<vue-toolbar></vue-toolbar>
		<vue-left-navigation class="hidden-md-and-up"></vue-left-navigation>
		<v-content style="min-height: 400px">
			<nuxt/>
		</v-content>
		<vue-footer />
	</v-app>
</template>

<script>
	import Toolbar from '@/components/Toolbar'
	import LeftNavigation from '@/components/LeftNavigation'
	import AlertDialog from '@/components/commons/alertDialog'
	import Footer from '@/components/Footer'
	import { mapState } from "vuex";
	import Cookies from 'js-cookie'
	export default {
		components: {
			'vue-left-navigation': LeftNavigation,
			'vue-toolbar' : Toolbar,
			'vue-footer': Footer,
			'vue-alert': AlertDialog
		},
		data() {
			return {
			}
		},
		computed: {
			...mapState({
				tracking: state => state.locationStore.tracking
			})
		},
		methods: {
			open: function() {
				this.$refs.alert.open()
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
			this.$store.dispatch('trackingLocation')
			this.$store.dispatch('currentLocation')
		}
	};
</script>

<style>
.card-radius {
	border-radius: 8px;
}
.btn-custom.v-text-field.v-text-field--solo .v-input__control {
	min-height: 30px !important;
	top: 4px
}
.btn-custom.theme--light.v-text-field--solo .v-input__slot {
	border-radius: 18px;
}
.v-card--reveal {
	align-items: center;
	bottom: 0;
	justify-content: center;
	opacity: .5;
	position: absolute;
	width: 100%;
}
</style>
