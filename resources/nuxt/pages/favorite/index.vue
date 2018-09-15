<template>
	<v-container>
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
		
		<v-layout justify-center v-if="!loading">
			<v-flex xs12>
				<v-card>
					<v-toolbar color="transparent" dense flat>
						<v-toolbar-title>
							Bộ sưu tập
						</v-toolbar-title>
					</v-toolbar>
					<v-divider></v-divider>
					<v-container fluid grid-list-lg>
						<v-layout row wrap>
							<!-- GRID STORE DESKTOP -->
							<v-flex v-if="$vuetify.breakpoint.mdAndUp" md3 d-flex v-for="(item, i) in stores" :key="i">
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

							<v-flex v-if="$vuetify.breakpoint.smAndDown" xs12 d-flex v-for="(item, i) in stores" :key="i">
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
										<v-layout row wrap >
											<v-flex xs4 sm2>
												<v-layout column align-center justify-center>
													<v-flex xs9>
														<v-card style="border-radius: 50%" :max-width="60" :max-height="60">	
															<v-avatar size="60" color="grey lighten-3">
																<img :src="image(item.avatar)" alt="alt">
															</v-avatar>		
														</v-card>
													</v-flex>	
												</v-layout>	
											</v-flex>

											<v-flex xs8 sm10 class="px-0">			
												<div class="font-weight-bold text-truncate">{{item.name}}</div>
												<v-tooltip top>									
													<div slot="activator" class="grey--text body-1 text-truncate">{{item.address}}</div>
													<span>{{item.address}}</span>
												</v-tooltip>									
											</v-flex>
										</v-layout>
									</v-card>
								</v-hover>
							</v-flex>
						</v-layout>
					</v-container>
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
			axios.post('/api/Favorite/Stores', data, {params, headers: getHeader(), withCredentials:true}).then(response => {
				if(response.status === 200) {
					this.stores = response.data.stores
				}
			})
		},
		removeFavoriteStore(store) {
			var vm   = this
			console.log(store)
			vm.$store.dispatch('removeFavoriteStore', store.id).then(response => {
				if(response.status === 200) {
					this.stores.splice(this.stores.indexOf(store), 1)
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