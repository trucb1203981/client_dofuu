<template>
	<v-navigation-drawer fixed :clipped="$vuetify.breakpoint.mdAndUp" v-model="drawer" class="hidden-md-and-up" v-if="isAuth">
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
							<v-icon color="red accent-3">scatter_plot</v-icon> <span class="font-weight-bold">{{currentUser.points}}</span>
						</v-list-tile-sub-title>
						<span>Điểm df dùng đổi thưởng</span>
					</v-tooltip>
				</v-list-tile-content>
			</v-list-tile>
		</v-list>
		<v-divider></v-divider>
		<v-list>
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
			<v-divider></v-divider>
			<v-list-tile>
				<v-btn color="error" block  @click="$store.dispatch('logout')" small round>
					Đăng xuất
					<v-icon color="white" right>exit_to_app</v-icon>
				</v-btn>
			</v-list-tile>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
	import axios from 'axios'
	import {mapState} from 'vuex'
	import index from '@/mixins/index'
	export default {
		mixins: [index],
		data() {
			return {
				keywords: typeof this.$route.query.q != 'undefined' ? this.$route.query.q : '',
				drawer:false,
			}
		},
		methods: {
		},
		watch: {
			'drawer': function(val) {
				if(!val) {
					this.$store.commit('LEFT_NAVIGATION_CLOSE')
				}
			},
			'leftDrawer': function(val) {
				if(val) {
					this.drawer = true
				}
			}
		},
		computed: {
			...mapState({
				leftDrawer: state  => state.leftDrawer,
				currentUser: state => state.authStore.currentUser,
				currentCity: state => state.cityStore.currentCity
			}),
			isAuth() {
				return this.$store.getters.isAuth
			},
		},
		mounted() {

		}
	}
</script>