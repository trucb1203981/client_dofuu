<template>
	<v-card flat>
		<v-system-bar window color="red accent-2 white--text">
			<v-icon color="white">alarm</v-icon>
			<span class="px-1">6:00</span>
			<span> - </span>
			<span class="px-1">22:00</span>
			<v-spacer></v-spacer>
			<v-btn v-if="!isAuth" flat nuxt :to="{path: '/login', query: {redirect: $route.path}}" class="white--text text-accent-2">
				ĐĂNG NHẬP
			</v-btn>		
		</v-system-bar>
		<v-toolbar extended dense color="white" :clipped-left="$vuetify.breakpoint.lgAndUp" class="elevation-0">
			<v-toolbar-title class="red--text text-accent-2" :style="$vuetify.breakpoint.lgAndUp ? 'width: 170px': 'width: 100px'">
				<v-list-tile :exact="false" active-class="red--text" :to="{path: '/'}">
					DOFUU
				</v-list-tile>  
			</v-toolbar-title>
			<v-text-field solo
			light
			v-model="keywords"
			color="red accent-2"
			label="Tìm kiếm"
			offset-x
			@keyup.enter="search"
			:append-icon="'search'"
			:append-icon-cb="search"
			></v-text-field>
			<v-spacer class="hidden-sm-and-down"></v-spacer>
			<v-toolbar-items>
				<v-menu id="menu"
				v-if="isAuth && currentUser != null"
				lazy
				left
				:close-on-content-click="false"
				min-width="240px"
				max-width="240px"
				:nudge-bottom="50">
				<v-list slot="activator" dense>
					<v-list-tile avatar @click="">
						<v-avatar size="28">
							<img :src="image(currentUser.avatar)">
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
								<img :src="image(currentUser.avatar)" alt="John">
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
		<v-toolbar color="white" slot="extension"  dense flat>
			<v-select flat :items="cities" v-model="cityCurrent" item-value="id" dense light solo-inverted item-text="name" return-object style="max-width:150px" class="ml-3 mr-3" color="red accent-2">
			</v-select >
			<v-toolbar-items>
				<v-menu bottom offset-y>
					<v-btn slot="activator" flat>Danh mục
						<v-icon right>expand_more</v-icon>
					</v-btn>
					<v-list v-if="currentCity != null">
						<v-list-tile nuxt :to="{name: 'city-tat-ca-dia-diem', params: {city: currentCity.slug }}">
							<v-list-tile-title>Tất cả</v-list-tile-title>
						</v-list-tile>
						
						<v-list-tile v-for="(item, index) in types" :key="index" nuxt :to="{name: 'city-dia-diem-type', params: {city: currentCity.slug, type: item.slug }}">
							<v-list-tile-title>{{ item.name }}</v-list-tile-title>
						</v-list-tile>
					</v-list>
				</v-menu>
			</v-toolbar-items>
		</v-toolbar>
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
			keywords: typeof this.$route.query.q != 'undefined' ? this.$route.query.q : '',
			stores: [],
			loading: false,
			select: []
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
	},
	created() {

	}
}	
</script>

<style scoped lang="css">
.btn--content {
	width: 0px;
}
</style>