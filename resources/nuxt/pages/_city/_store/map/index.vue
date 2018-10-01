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
					<v-card-text class="grey lighten-4" v-if="!!distance && !!duration">
						<div>Khoảng cách: {{distance}}</div>
						<div>Thời gian: {{duration}}</div>
					</v-card-text>		
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
				var image  = this.typeIcon(this.store.type.name, 'blue')
				
				this.makeMarker(latLng, image, this.map)
				
			},
			direction: function() {
				var vm        = this
				var map       = vm.map
				var start     = vm.myLocation
				var end       = vm.store
				var startIcon = { url: '/map_icons/my-location.png',  scaledSize: new google.maps.Size(24, 24)}
				var endIcon   = vm.typeIcon(vm.store.type.name, 'blue')
				if(vm.tracking) {
					vm.marker.setMap(null)
					calculateDirection(map, start, end).then(response => {
						var leg     = response.routes[0].legs[0];
						vm.distance = leg.distance.text
						vm.duration = leg.duration.text
						vm.makeMarker(leg.start_location, startIcon, vm.map);
						vm.makeMarker(leg.end_location, endIcon, vm.map);
					})
				} else {
					if(!!vm.message) {						
						vm.$refs.alert.open('Thông báo', vm.message)
					} else {
						vm.$refs.alert.open('Thông báo', 'Chưa cấp phép truy cập vị trí hiện tại của bạn.')
					}
				}
			},
			makeMarker: function(position, image, map) {
				var marker = new google.maps.Marker({
					position: position,
					icon: image,
					map: map,
					animation: google.maps.Animation.DROP,
				});
				this.marker = marker
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
			this.initMap(this.store)
		}
	}
</script>

<style>
#map {
	width: 100%;
	height: 400px;
}
</style>