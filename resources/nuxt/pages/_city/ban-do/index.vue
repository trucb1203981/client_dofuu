<template>
	<v-container>
		<v-layout v-show="!loading">
			<v-flex xs12 md12>
				<v-card>
					<v-toolbar color="white" dense flat>
						<v-btn color="blue" class="white--text" small @click.prevent="direction">
							<v-icon left>directions</v-icon>Chỉ đường
						</v-btn>
					</v-toolbar>			
					<v-divider></v-divider>
					<div id="map"></div>
				</v-card>
			</v-flex>
		</v-layout>
		<vue-alert ref="alert"/> 
	</v-container>
</template>

<script>
	import { mapState } from 'vuex'
	import index from '@/mixins/index'
	import { calculateDirection } from '@/utils'
	import AlertDialog from '@/components/commons/alertDialog'
	export default {
		mixins: [index],
		components: {
			'vue-alert': AlertDialog
		},
		data() {
			return {
				map: null,
				marker: null,
				duration: null,
				distance: null
			}
		},
		methods: {
			initMap(position) {

				const latLng = new google.maps.LatLng(position.lat, position.lng);
				this.map     = new google.maps.Map(document.getElementById('map'), {
					zoom: 17,
					center: latLng,
					mapTypeControl: false,
					streetViewControl: false
				});
				
			}
		},
		computed: {
			...mapState({
				loading: state    => state.storeStore.loading,
				store: state      => state.storeStore.store,
				myLocation: state => state.myLocation,
				tracking: state   => state.locationStore.tracking,
				message: state    => state.locationStore.message
			})
		},
		mounted() {
			this.initMap(this.myLocation)
		}
	}
</script>

<style>
	#map {
		width: 100%;
		height: 400px;
	}
</style>
