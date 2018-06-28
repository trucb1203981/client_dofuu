<template>
	<v-card flat>
		<v-system-bar status color="red darken-4" dark>
			<span v-if="currentCity != null">				
				{{currentCity.service.startTime | formatTime}} - {{currentCity.service.endTime | formatTime}}
			</span>
		</v-system-bar>
		<v-toolbar color="white" :clipped-left="$vuetify.breakpoint.lgAndUp" class="elevation-0">
			<v-toolbar-side-icon @click.stop="$store.commit('LEFT_NAVIGATION_SHOW')" class="hidden-md-and-up"></v-toolbar-side-icon>

			<v-toolbar-title class="red--text text-accent-2 pt-2 hidden-sm-and-down" :style="$vuetify.breakpoint.lgAndUp ? 'width: 200px': 'width: 200px'">		
				<a href="/">					
					<img src="/logo_page.png" alt="dofuu-logo">
				</a>
			</v-toolbar-title>

			<v-text-field solo
			light
			v-model="keywords"
			color="red accent-2"
			label="Tìm kiếm (quán, món, ...)"
			offset-x
			@keyup.enter="search"
			:append-icon="'search'"
			:append-icon-cb="search"
			single-line></v-text-field>		

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
							<v-list-tile-title>
								<v-flex ellipsis xs12>								
									{{currentUser.name}}
								</v-flex>
							</v-list-tile-title>
						</v-list-tile-content>
						<v-icon>expand_more</v-icon>
					</v-list-tile>
				</v-list>
				<v-card>
					<v-list>
						<v-list-tile avatar @click="">
							<v-list-tile-avatar>
								<img :src="image(currentUser.image)" alt="John">
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>{{currentUser.name}}</v-list-tile-title>
								<v-list-tile-sub-title>{{currentUser.type}}</v-list-tile-sub-title>
							</v-list-tile-content>
						</v-list-tile>
					</v-list>
					<v-divider></v-divider>
					<v-list >
						<v-list-tile avatar :to="{name:'history'}">
							<v-list-tile-avatar>
								<v-icon class="yellow lighten-1 white--text">history</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Lịch sử đặt món</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>
						<v-list-tile avatar @click="$store.dispatch('logout')">
							<v-list-tile-avatar>
								<v-icon class="red lighten-1 white--text">exit_to_app</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Đăng xuất</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>
					</v-list>
				</v-card>
			</v-menu>
		</v-toolbar-items>	
	</v-toolbar>
	<v-tabs v-if="currentCity != null && types.length > 0" fixed-tabs show-arrows slider-color="red">
		<v-tab nuxt href="/">
			<v-icon left color="red accent-3">home</v-icon> <h5>Trang chủ </h5>
		</v-tab>
		<v-tab nuxt :to="{name: 'city-tat-ca-dia-diem', params: {city: currentCity.slug }}">
			<v-icon left size="20">apps</v-icon> <h5>Tất cả</h5>
		</v-tab>
		<v-tab v-for="(item, index) in types" :key="index" nuxt :to="{name: 'city-dia-diem-type', params: {city: currentCity.slug, type: item.slug }}">
			<v-icon left size="20">{{item.icon}}</v-icon> <h5>{{ item.name }}</h5>
		</v-tab>
	</v-tabs>
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
			keywords: typeof this.$route.query.q != 'undefined' ? this.$route.query.q : '',
			stores: [],
			loading: false,
			select: [],
			leftDrawer: true
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
		search() {
			const keyword = this.keywords
			if(keyword.length>0) {
				this.$router.push({name: 'city-tim-kiem-tat-ca', query: {q: keyword}, params: {city: this.currentCity.slug}})
			}
			return 		
		}
	}
}	
</script>

<style scoped lang="css">
.btn--content {
	width: 0px;
}
</style>