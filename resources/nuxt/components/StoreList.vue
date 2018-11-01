<template>
	<v-layout row wrap>
		<v-flex v-if="$vuetify.breakpoint.xsOnly" xs12  v-for="(item, i) in stores" :key="i">
			<v-card hover :ripple="false" class="card-radius">	
				<v-system-bar status color="red darken-4" dark>
					<v-icon left>access_time</v-icon>
					<span>{{activityTime(item.activities)}}</span>
					<v-spacer></v-spacer>
					<span>{{item.type.name}}</span>
				</v-system-bar>

				<v-card tile :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}" flat>
					<v-layout row wrap class="pa-1" >
						<v-flex xs3 sm2>
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
							<v-system-bar v-if="!!item.coupon" status color="transparent" class="px-0 mx-0">
								<span>
									<h4 class="red--text"><i>{{item.coupon[0].title}}</i></h4>
								</span>
							</v-system-bar>										
						</v-flex>
					</v-layout>
				</v-card>

				<v-system-bar color="grey lighten-5">
					<v-spacer></v-spacer>
					<v-tooltip top>
						<span slot="activator">
							<v-icon>visibility</v-icon>
							{{item.views}}
						</span>
						<span>{{item.views}} lượt xem</span>
					</v-tooltip>
					<v-divider vertical inset class="mx-2"></v-divider>
					
					<v-tooltip top>
						<span slot="activator">
							<v-icon >comment</v-icon>
							{{item.totalComment}}
						</span>
						<span>{{item.totalComment}} lượt bình luận</span>
					</v-tooltip>
					<v-divider vertical inset class="mx-2"></v-divider>

					<v-tooltip top>
						<span slot="activator">
							<v-icon>favorite</v-icon>
							{{item.likes}}
						</span>
						<span>{{item.likes}} lượt thích</span>
					</v-tooltip>
					<v-divider vertical inset class="mx-2"></v-divider>
					
					<v-tooltip top>		
						<v-icon slot="activator" @click.prevent="favoriteStore(item)" color="blue">{{item.checkedFavorite ? 'bookmark' : 'bookmark_border'}}</v-icon>
						<span>{{item.checkedFavorite ? 'Bỏ lưu' : 'Lưu'}}</span>
					</v-tooltip>
				</v-system-bar>
			</v-card>
		</v-flex>
	</v-layout>	
</template>

<script>
	import index from "@/mixins/index.js";
	import { mapState } from 'vuex'
	export default {
		mixins: [index],
		props: ['stores', 'currentCity'],
		computed: {
			...mapState({
				processFavorite: state => state.storeStore.processFavorite
			}),
			isAuth() {
				return this.$store.getters.isAuth
			},
		},
		methods: {
			//CLICK FAVORITE
			favoriteStore(store) {
				var vm        = this
				const storeId = store.id
				var data      = {}
				const params  = {name: 'favoriteEndpoint'}
				if(vm.isAuth) {
					if(!vm.processFavorite) {
						store.checkedFavorite = !store.checkedFavorite
						vm.$store.dispatch('toggleFavoriteStore', storeId)			
					}
				} else {
					this.$router.push({name: 'login', query: {redirect: this.$route.path}})
				}	
			},
		}
	}
</script>

<style scoped>
</style>