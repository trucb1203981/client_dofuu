<template>
	<v-card>
		<v-system-bar status color="red darken-4" dark>
			<span v-if="currentCity != null">				
				{{currentCity.service.startTime | formatTime}} - {{currentCity.service.endTime | formatTime}}
			</span>
		</v-system-bar>

		<v-toolbar color="white" :clipped-left="$vuetify.breakpoint.lgAndUp" class="elevation-0" prominent tabs>
			<v-toolbar-side-icon @click.stop="$store.commit('LEFT_NAVIGATION_SHOW')" class="hidden-md-and-up" small></v-toolbar-side-icon>

			<v-toolbar-title class="red--text text-accent-2 pt-2 hidden-sm-and-down" :style="$vuetify.breakpoint.lgAndUp ? 'width: 280px': 'width: 200px'">		
				<nuxt-link :to="{path: '/'}">					
					<img src="/logo_page.png" alt="dofuu-logo">
				</nuxt-link>
			</v-toolbar-title>

			<!-- <v-autocomplete color="red accent-3" :loading="loading"
			:search-input.sync="keywords" class="mx-3" hide-no-data hide-details label="Tìm kiếm (quán, món, ...)" solo @keyup.enter="search" :append-icon="null">
			<template slot="append-outer">
				<v-btn color="white" icon style="top: -9px" class="elevation-1">					
					<v-icon color="blue" @click="search" >search</v-icon>
				</v-btn>
			</template>
		</v-autocomplete>	 -->	
		<v-text-field class="btn-custom" solo flat background-color="grey lighten-4" label="Tìm kiếm (quán, món, ...)"  @keyup.enter="search" v-model="keywords" >
			<v-btn color="white" icon  class="ma-0"  style="right: -10px" @click="search" small slot="append">
				<v-icon color="blue" >search</v-icon>
			</v-btn>
		</v-text-field>
		<v-spacer v-if="$vuetify.breakpoint.mdAndUp"></v-spacer>

		<v-btn v-if="!isAuth" nuxt :to="{path: '/login', query: {redirect: $route.path}}" color="blue" small dark :round="$vuetify.breakpoint.mdAndUp" :icon="$vuetify.breakpoint.smAndDown">
			<v-icon>person</v-icon> <span class="hidden-sm-and-down pl-1">ĐĂNG NHẬP</span>
		</v-btn>

		<v-toolbar-items>
			<v-menu id="menu"
			v-if="isAuth && currentUser != null"
			lazy
			left
			:close-on-content-click="false"
			min-width="240px"
			max-width="240px"
			:nudge-bottom="50"
			class="hidden-sm-and-down">
			<v-list slot="activator" dense>
				<v-list-tile avatar @click="">
					<v-avatar size="28">
						<img :src="image(currentUser.image)">
					</v-avatar>
					<v-list-tile-content class="pl-2 ellipsis" style="max-width:140px">
						<v-list-tile-title class="text-truncate">								
							{{currentUser.name}}
						</v-list-tile-title>
					</v-list-tile-content>
					<v-icon>expand_more</v-icon>
				</v-list-tile>
			</v-list>
			<v-card>
				<v-list two-line>
					<v-list-tile avatar :to="{name:'information'}">
						<v-list-tile-avatar>
							<img :src="image(currentUser.image)" alt="John">
						</v-list-tile-avatar>
						<v-list-tile-content>
							<v-list-tile-title class="text-truncate">{{currentUser.name}}</v-list-tile-title>
							<v-list-tile-sub-title>Thông tin tài khoản</v-list-tile-sub-title>
							<v-tooltip top>
								<v-list-tile-sub-title slot="activator">
									<v-icon color="red accent-3">scatter_plot</v-icon> <span class="font-weight-bold">{{currentUser.points}}</span></v-list-tile-sub-title>
									<span>Điểm df dùng đổi thưởng</span>
								</v-tooltip>
							</v-list-tile-content>
						</v-list-tile>
					</v-list>
					<v-divider></v-divider>
					<v-list dense>

						<v-list-tile avatar :to="{name:'favorite'}">
							<v-list-tile-avatar size="32"> 
								<v-icon class="blue white--text">bookmarks</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Bộ sưu tập</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>

						<v-list-tile avatar :to="{name:'history'}">
							<v-list-tile-avatar size="32">
								<v-icon class="red darken-3 white--text">history</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Lịch sử đặt món</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>

					</v-list>
					<v-card-actions>					
						<v-btn color="error" block  @click="$store.dispatch('logout')" small round>
							Đăng xuất
							<v-icon color="white" right>exit_to_app</v-icon>
						</v-btn>
					</v-card-actions>
				</v-card>
			</v-menu>
		</v-toolbar-items>	

		<v-tabs slot="extension" v-if="currentCity != null && types.length > 0" slider-color="red" fixed-tabs>
			<v-tab nuxt :to="{path: '/'}">
				<v-icon left color="red darken-3" size="20">home</v-icon> <h5>Trang chủ </h5>
			</v-tab>
			<v-divider vertical inset></v-divider>
			<v-tab nuxt :to="{name: 'city-tat-ca-dia-diem', params: {city: currentCity.slug }}" >
				<v-icon left size="20">apps</v-icon> <h5>Tất cả</h5>
			</v-tab>
			<v-divider vertical inset></v-divider>
			<template v-for="(item, index) in types" >
				<v-tab :key="index" nuxt :to="{name: 'city-dia-diem-type', params: {city: currentCity.slug, type: item.slug }}">
					<v-icon left size="20">{{item.icon}}</v-icon> <h5>{{ item.name }}</h5>
				</v-tab>
				<v-divider vertical v-if="index != types.length-1" inset></v-divider>
			</template>
		</v-tabs>
	</v-toolbar>
</v-card>
</template>

<script>
import {mapState} from 'vuex'
import axios from 'axios'
import index from '@/mixins/index'
export default {
	mixins: [index],
	data() {
		return {
			keywords: typeof this.$route.query.q != 'undefined' ? this.$route.query.q : null,
			stores: [],
			loading: false,
			select: [],
			leftDrawer: true,
			model:null
		}
	},
	computed: {
		...mapState({
			currentUser: state => state.authStore.currentUser,
			currentCity: state => state.cityStore.currentCity
		}),
		isAuth() {
			return this.$store.getters.isAuth
		},
		cities: function() {
			return Array.from(this.$store.getters.getAllCity);
		},
		types: function() {
			return Array.from(this.$store.getters.getAllType)
		},
		cityCurrent: function() {
			return this.$store.getters.getCurrentID
		}
	},
	watch: {
		// keywords (val) {
		// 	if(val.length>0) {
		// 		this.querySelections(val)
		// 	} else {
		// 		this.stores = JSON.parse(JSON.stringify([]))
		// 	}
		// }
	},
	methods: {
		// querySelections (v) {
		// 	var vm = this
		// 	this.loading = true
		// 	if(this.keywords.length>0) {
		// 		console.log(this.keywords.length)
		// 		setTimeout(() => {
		// 			const params = {keywords: this.keywords, cityId: this.currentCity.id, maxSize: 8, context: 'search'}
		// 			axios.get('/api/Search/Bar/Query', {params, withCredentials:true}).then(response => {
		// 				if(response.status == 200) {
		// 					vm.stores = response.data.data
		// 				}
		// 			})
		// 			this.loading = false
		// 		}, 500)
		// 	}
		// },
		title(value) {
			if(isEmployee) {
				return 'Quản trị viên'
			} else if(isPartner) {
				return 'Thương gia'
			} 
			return 'Khách hàng'
		},
		search () {
			const keyword = this.keywords
			if( keyword !== null || keyword.length>0) {
				this.$router.push({name: 'city-tim-kiem-tat-ca', query: {q: keyword}, params: {city: this.currentCity.slug}})
			}
			return 		
		}
	}
}	
</script>