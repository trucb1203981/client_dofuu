<template>
	<v-container grid-list-xs="$vuetify.breakpoint.smAndDown" grid-list-md="$vuetify.breakpoint.mdAndUp" :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-dialog v-model="loading" hide-overlay persistent width="300">
			<v-card	color="red darken-3"	dark>
				<v-card-text>
					Xin Quý khách vui lòng chờ trong giây lát...
					<v-progress-linear
					indeterminate
					color="white"
					class="mb-0"
					></v-progress-linear>
				</v-card-text>
			</v-card>
		</v-dialog>
		
		<v-layout row wrap v-if="!loading">
			<v-flex xs12>
				<v-card>
					<v-layout grey lighten-4 fill-height row wrap class="elevation-1">
						<v-toolbar color="white" flat dense>
							<v-toolbar-title>
								<span class="text-capitalize">{{title}}</span>
							</v-toolbar-title>
						</v-toolbar>
						<v-divider></v-divider>
						<v-flex xs12>
							<v-container fluid>
								<v-layout row wrap>
									<!-- GRID STORE DESKTOP -->
									<v-flex v-if="$vuetify.breakpoint.smAndUp" sm4 md3 d-flex v-for="(item, i) in stores" :key="i">
										<v-hover>
											<v-card slot-scope="{ hover }" nuxt  hover ripple class="card-radius mx-auto" >
												<v-system-bar status color="grey lighten-5" class="transition-fast-in-fast-out text-xs-right">
													<div>{{item.type.name}}</div>
													<v-spacer></v-spacer>
													<v-tooltip top>													
														<v-icon slot="activator" @click="removeFavoriteStore(item)">close</v-icon>
														<span>Bỏ lưu</span>
													</v-tooltip>
												</v-system-bar>
												<v-card :to="{name: 'city-store', params: { city: item.citySlug, store: item.slug }}">
													<v-img :src="image(item.avatar)" :aspect-ratio="16/9" :lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`">
														<v-layout slot="placeholder" fill-height align-center justify-center ma-0 >
															<v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
														</v-layout>
													</v-img>
												</v-card>

												<v-divider light></v-divider>

												<v-card-text >
													<v-tooltip top>												
														<div slot="activator" class="text-truncate"><strong >{{item.name}}</strong>
														</div>
														<span>{{item.name}}</span>
													</v-tooltip>
													<v-tooltip top>												
														<div slot="activator" class="text-truncate">{{item.address}}</div>
														<span>{{item.address}}</span>
													</v-tooltip>
												</v-card-text>
											</v-card>
										</v-hover>
									</v-flex>	

									<!-- LIST STORE MOBILE -->

									<v-flex v-if="$vuetify.breakpoint.xsOnly" xs12 v-for="(item, i) in stores" :key="i">
										<v-card ripple class="card-radius" >

											<v-system-bar status color="grey lighten-5">
												<div>{{item.type.name}}</div>
												<v-spacer></v-spacer>
												<v-tooltip top>											
													<v-icon slot="activator" @click="removeFavoriteStore(item)">close</v-icon>
													<span>Bỏ lưu</span>
												</v-tooltip>
											</v-system-bar>

											<v-card :to="{name: 'city-store', params: { city: item.citySlug, store: item.slug }}" nuxt flat>
												<v-layout row wrap class="pa-1" >
													<v-flex xs3>
														<v-layout column align-center justify-center>
															<v-flex xs9>
																<v-card style="border-radius: 50%" :max-width="80" :max-height="80" raised>	
																	<v-avatar size="60" color="grey lighten-3">
																		<img :src="image(item.avatar)" alt="alt">
																	</v-avatar>		
																</v-card>
															</v-flex>	
														</v-layout>	
													</v-flex>

													<v-flex xs9 sm10 class="px-0">			
														<div class="font-weight-bold text-truncate">{{item.name}}</div>
														<v-tooltip top>									
															<div slot="activator" class="grey--text body-1 text-truncate">{{item.address}}</div>
															<span>{{item.address}}</span>
														</v-tooltip>									
													</v-flex>
												</v-layout>
											</v-card>
										</v-card>
									</v-flex>
								</v-layout>								
							</v-container>
						</v-flex>
					</v-layout>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
	import ImageDialog from '@/components/ImageDialog'
	import index from '@/mixins/index'
	import { mapState } from 'vuex'
	import axios from 'axios'
	import { getHeader } from '@/config'
	import StoreList from '@/components/StoreList'
	import StoreGrid from '@/components/StoreGrid'
	export default {
		middleware: 'notAuthenticated',
		mixins: [index],
		components: {
			'vue-store-list': StoreList,
			'vue-store-grid': StoreGrid
		},
		data() {
			return {
				title: 'Bộ sưu tập',
				loading: false,
				stores: null
			}
		},
		methods: {
		//GET ALL FAVORITE STORE
		fetchFavoriteStore() {
			var vm   = this
			var data = {}
			const params = {name: 'favoriteEndpoint'}
			axios.post('/api/FavoriteStore/FetchStores', data, {params, withCredentials:true}).then(response => {
				if(response.status === 200) {
					vm.stores = response.data.stores
				}
			})
		},
		removeFavoriteStore(store) {
			var vm        = this
			const data    = {}
			const storeId = store.id
			const params  = {name: 'favoriteEndpoint'}

			axios.post('/api/FavoriteStore/'+storeId+'/RemoveFavorite', data, {params, withCredentials:true}).then(response => {
				if(response.status === 200) {
					vm.stores.splice(this.stores.indexOf(store), 1)
				}
			})
		}
	},
	computed: {
		...mapState({
			currentUser: state => state.authStore.currentUser
		}),
		show: function() {
			if(this.$vuetify.breakpoint.smAndDown) {
				return true
			}
			if(this.hoverImage) {
				return true
			} else {
				return false
			}
		}
	},
	created() {
		this.fetchFavoriteStore()
	}
}
</script>

<style scoped>
.v-card--reveal {
	align-items: center;
	bottom: 0;
	justify-content: center;
	opacity: .5;
	position: absolute;
	width: 100%;
}
.card-radius {
	border-radius: 15px;
}
</style>