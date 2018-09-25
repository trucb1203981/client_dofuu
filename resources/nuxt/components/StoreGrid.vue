<template>
	<v-layout row wrap>
		<v-flex v-if="$vuetify.breakpoint.smAndUp" sm6 md3 d-flex v-for="(item, i) in stores" :key="i">
			<v-card hover ripple class="card-radius" >
				
				<v-system-bar status color="red darken-4" dark>
					<v-icon left>access_time</v-icon>
					<span v-for="(item, i) in item.activities" v-if="i==0">		
						<span v-for="(time, i) in item.times">
							{{time.from}} - {{time.to}} 
						</span>	
					</span>
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
			})
		},
		methods: {
			//CLICK FAVORITE
			favoriteStore(store) {
				var vm        = this
				const storeId = store.id
				var data      = {}
				const params  = {name: 'favoriteEndpoint'}

				if(!vm.processFavorite) {
					store.checkedFavorite = !store.checkedFavorite
					vm.$store.dispatch('toggleFavoriteStore', storeId)
				}
			},
		}
	}
</script>

<style scoped>
.card-radius {
	border-radius: 15px;
}
</style>