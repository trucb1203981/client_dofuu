<template>
	<v-layout row wrap>
		<v-flex v-if="$vuetify.breakpoint.smAndUp" sm6 md4 lg3 d-flex v-for="(item, i) in stores" :key="i">
			<v-card  hover ripple class="card-radius" >
				<v-system-bar status color="red darken-4" dark>
					<v-icon left>access_time</v-icon>
					<span>{{activityTime(item.activities)}}</span>
					<v-spacer></v-spacer>
					<span>
						{{item.type.name}}
					</span>
				</v-system-bar>

				<v-card flat nuxt :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}">
					<v-img :src="image(item.avatar)" :aspect-ratio="16/9" :lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`">
						<v-layout slot="placeholder" fill-height align-center justify-center ma-0 >
							<v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
						</v-layout>		
					</v-img>


					<v-divider light></v-divider>
					<v-card-text  class="pt-4" style="position: relative;">

						<v-btn slot="activator" v-if="!!item.coupon" absolute color="white" class="red--text" small right top style="height: 26px; width: 26px; top:-16px" round :ripple="false">
							<v-tooltip top>
								<span slot="activator" class="font-weight-bold">Giảm {{item.coupon.discount}}%</span>
								<span>{{item.coupon.title}}</span>
							</v-tooltip>
							
						</v-btn>

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
			}
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
.card-radius {
	border-radius: 15px;
}

.v-card--reveal--text {
	align-items: center;
	bottom: 0;
	justify-content: center;
	position: absolute;
	width: 100%;
}
</style>