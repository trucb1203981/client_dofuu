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
							<v-flex v-if="$vuetify.breakpoint.smAndDown" xs12  v-for="(item, i) in stores" :key="i">
								<v-card flat>	
									<v-layout row wrap class="pa-1" align-center justify-center>
										<v-flex xs3 sm3>
											<v-card :to="{name: 'city-store', params: {city: item.citySlug, store: item.slug}}" flat tile class="d-flex card-radius" >
												<v-img :src="image(item.avatar)" aspect-ratio="1" alt="alt"></v-img>
											</v-card>
										</v-flex>

										<v-flex xs7 sm7 class="px-0">		
											<v-card :to="{name: 'city-store', params: {city: item.citySlug, store: item.slug}}" flat transparent>												
												<div class="body-2 ">{{item.type.name}}</div>	
												<div class="font-weight-bold text-truncate">{{item.name}}</div>
												<v-tooltip top>									
													<div slot="activator" class="grey--text body-1 text-truncate">{{item.address}}</div>
													<span>{{item.address}}</span>
												</v-tooltip>
											</v-card>									
										</v-flex>
										<v-flex xs2 sm2 >
											<v-card>									
												<v-btn icon color="transparent" z-index="999" absolute top right @click="removeFavoriteStore(item.id)">
													<v-icon size="16">close</v-icon>
												</v-btn>
											</v-card>
										</v-flex>

										<v-flex offset-xs-3 xs11>
											<v-divider inset></v-divider>
										</v-flex>
									</v-layout>									
								</v-card>

							</v-flex>	

							<v-flex v-if="$vuetify.breakpoint.mdAndUp" md3 d-flex v-for="(item, i) in stores" :key="i">
								<v-hover>
									<v-card slot-scope="{ hover }" nuxt  hover ripple class="card-radius mx-auto" >
										<v-expand-transition>
											<v-system-bar v-if="hover" status color="red darken-4" dark class="transition-fast-in-fast-out text-xs-right">
												<v-spacer></v-spacer>
												<v-icon @click="removeFavoriteStore(item.id)">close</v-icon>
											</v-system-bar>
										</v-expand-transition>
										<v-card :to="{name: 'city-store', params: { city: item.citySlug, store: item.slug }}">
											<v-img :src="image(item.avatar)" :aspect-ratio="16/9" :lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`">
												<v-layout slot="placeholder" fill-height align-center justify-center ma-0 >
													<v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
												</v-layout>
											</v-img>
										</v-card>

										<v-system-bar v-if="item.coupon != null " status color="transparent">
											<h4 class="red--text"><i>{{item.coupon.title}}</i></h4>
										</v-system-bar>

										<v-divider light></v-divider>
										<v-card-text >
											<v-tooltip top>												
												<div slot="activator" style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap"><strong >{{item.name}}</strong>
												</div>
												<span>{{item.name}}</span>
											</v-tooltip>
											<v-tooltip top>												
												<div slot="activator" style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap">{{item.address}}</div>
												<span>{{item.address}}</span>
											</v-tooltip>
										</v-card-text>
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
		removeFavoriteStore(storeId) {
			var vm   = this
			vm.$store.dispatch('removeFavoriteStore', storeId).then(response => {
				if(response.status === 200) {
					this.stores.splice(this.stores.indexOf(response.data.store), 1)
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