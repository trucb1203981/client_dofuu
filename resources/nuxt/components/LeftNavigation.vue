<template>
	<v-navigation-drawer fixed :clipped="$vuetify.breakpoint.mdAndUp" v-model="drawer" class="hidden-md-and-up">
		<v-toolbar 	color="red"	dark flat extended > 
			<v-toolbar-title>
				DOFUU
			</v-toolbar-title>
			<v-text-field
			slot="extension"
			solo-inverted
			flat
			class="mx-3"
			v-model="keywords"
			color="red accent-2"
			label="Tìm kiếm (quán, món, ...)"
			offset-x
			@keyup.enter="search"
			:append-icon="'search'"
			:append-icon-cb="search"
			></v-text-field>
		</v-toolbar>
		<v-list v-if="currentUser != null">
			<v-subheader>Thông tin tài khoản</v-subheader>
			<v-list-tile avatar :to="{name:'information'}">
				<v-list-tile-avatar>
					<img :src="image(currentUser.image)" alt="currentUser.name">
				</v-list-tile-avatar>
				<v-list-tile-content>
					<v-list-tile-title>{{currentUser.name}}</v-list-tile-title>
					<v-tooltip top>
						<v-list-tile-sub-title slot="activator">
							<v-icon color="red accent-3">scatter_plot</v-icon> <span class="font-weight-bold">{{currentUser.points}}</span></v-list-tile-sub-title>
							<span>Điểm df dùng đổi thưởng</span>
						</v-tooltip>
					</v-list-tile-content>
				</v-list-tile-content>
			</v-list-tile>
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
		search() {
			const keyword = this.keywords
			if(keyword.length>0) {
				this.$router.push({name: 'city-tim-kiem-tat-ca', query: {q: keyword}, params: {city: this.currentCity.slug}})
			}
			return 		
		}
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
		})
	},
	mounted() {

	}
}
</script>