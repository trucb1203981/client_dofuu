<template>
	<div ref="target_store" fluid class="mt-4" v-scroll="onScroll" v-show="!loading">
		<v-dialog v-model="loading" persistent fullscreen>
			<div id="overlay">
				<v-container fill-height>
					<v-layout row wrap justify-center align-center>
						<v-flex xs4 d-flex>
							<v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
						</v-flex>
					</v-layout>					
				</v-container>
			</div>
		</v-dialog>
		<v-content v-show="!loading">
			<v-toolbar :fixed="offsetTop>offsetTab" color="white" class="elevation-0 scroll-y"  flat dense style="max-height: 400px" tabs  v-if="store != null"> 

				<v-btn flat :to="{path: '/'}" icon>
					<v-icon>arrow_back</v-icon>
				</v-btn>
				<v-toolbar-title class="red--text text-accent-2 hidden-sm-and-down" style="width: 300px">
					{{store.name}}
				</v-toolbar-title>
				<v-tabs show-arrows>
					<v-tabs-slider color="yellow"></v-tabs-slider>
					<v-tab nuxt exact :to="{name: 'city-store', params: {city: $route.params.city, store: $route.params.store}}">
						Thực Đơn
					</v-tab>
					<!-- <v-tab nuxt exact :to="{name: 'city-store-about', params: {city: $route.params.city, store: $route.params.store}}">
						Giới thiệu
					</v-tab> -->

				</v-tabs>
			</v-toolbar>
			<v-layout row wrap v-if="store != null">
				<v-flex xs12 md3>
					<v-container>
						<v-card flat tile color="transparent">
							<v-card-media :src="image(store.avatar)" height="200">
								<v-content class="text-md-right">
									<!-- <v-tooltip top>
										<v-icon slot="activator" :color="store.status_color">radio_button_checked</v-icon>
										<span>{{store.status}}</span>
									</v-tooltip> -->
								</v-content>
							</v-card-media>
						</v-card>
						<v-list avatar dense>
							<v-list-tile>
								<v-list-tile-content>
									<v-list-tile-title class="title">
										<v-tooltip top>
											<span slot="activator">{{store.name}}</span>
											<span>{{store.name}}</span>
										</v-tooltip>
									</v-list-tile-title>
								</v-list-tile-content>
								<v-list-tile-action>
									<v-tooltip top>
										<v-icon slot="activator" color="primary" v-if="store.verified" >
											verified_user
										</v-icon> 	
										<span>Chứng nhận hợp tác với dofuu</span>
									</v-tooltip>
								</v-list-tile-action>
							</v-list-tile>
							<v-list-tile>
								<v-list-tile-content>
									<v-list-tile-title>{{store.address}}
									</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
						<v-divider></v-divider>
						<v-list  avatar dense>
							<v-list-tile>
								<v-list-tile-content>
									<v-list-tile-title><v-icon>alarm</v-icon>
										<span v-for="(item, i) in store.activities" v-if="i==0"> 
											<span v-for="(time, index) in item.times">
												{{time.from}} - {{time.to}} 
											</span>	
											<!-- <span :class="{'red--text accent-4--text': status(store.status) == 2, 'green--text accent-4--text': status(store.status) == 1, 'yellow--text accent-4--text': status(store.status) == 3}"><strong><i>({{store.status}})</i></strong></span> -->
										</span>

									</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
							<v-list-tile>
								<v-list-tile-content>
									<v-list-tile-title>
										<v-icon>access_time</v-icon> Chuẩn bị: {{store.prepareTime}} phút
									</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
					</v-container>	
				</v-flex>
				<v-flex xs12 md9 d-flex>
					<nuxt-child :key="$route.params.store" :store.sync="store"/>
				</v-flex>		
			</v-layout>			
		</v-content>
	</div>
</template>

<script>
import moment from 'moment'
import Cookies from 'js-cookie'
import axios from 'axios'
import {getStoreURL, getHeader} from '@/config'
import index from '@/mixins/index'
import {mapState} from 'vuex'
export default {
	mixins: [index],
	asyncData({params}) {
		return {
			city: {},
			activeSearch: false,
			offsetTab: 0,
		}
	},
	methods: {
		getStore() {
			const params = {_CID: this.$route.params.city, _SID: this.$route.params.store}
			this.$store.dispatch('getStore', params).then(response => {
				if(response.status == 200) {
					this.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
					this.$store.commit('UPDATE_CITY', response.data.city)
				}
			})
		},
		onScroll (e) {
			this.$store.commit('ON_SCROLL', window.scrollY)
		}
	},
	computed: {
		...mapState({
			loading: state   => state.storeStore.loading,
			offsetTop: state => state.offsetTop,
			store: state     => state.storeStore.store
		})
	},
	created() {
		this.getStore()
	},
	mounted() {
		this.offsetTab = this.$refs.target_store.offsetTop
	}
}
</script>

<style scoped>
#overlay {
	position: fixed; /* Sit on top of the page content */
	display: block; /* Hidden by default */
	width: 100%; /* Full width (cover the whole page) */
	height: 100%; /* Full height (cover the whole page) */
	top: 0; 
	left: 0;
	right: 0;
	bottom: 0;
	background-color: rgba(0,0,0,0.5); /* Black background with opacity */
	z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
}
</style>