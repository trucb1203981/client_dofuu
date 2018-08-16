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
				<v-card color="grey lighten-4">
					<v-toolbar color="white" dense class="elevation-0">
						<v-toolbar-title>
							Thông tin tài khoản
						</v-toolbar-title>
						<v-spacer></v-spacer>
						<v-tooltip top>
							<span slot="activator"><v-icon color="red accent-3">scatter_plot</v-icon> <span class="font-weight-bold">{{currentUser.points}}</span></span>
							<span>Điểm df dùng đổi thưởng</span>
						</v-tooltip>
					</v-toolbar>
					<v-container>
						<v-layout row wrap class="justify-center">

							<!-- <a @click.stop="$store.commit('SHOW_IMAGE_DIALOG')">								 -->
								<v-avatar  size="150" color="grey" style="border" @mouseover="hoverImage = true" @mouseleave="hoverImage = false">
									<img :src="image(currentUser.image)" alt="avatar">
								</v-avatar>
								<v-badge color="blue-grey" top right overlap v-model="show">
									<v-icon slot="badge" dark >camera_alt</v-icon>
								</v-badge>	
								<!-- </a> -->
							</v-layout>
						</v-container>				
						<v-list>
							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="primary">person</v-icon>							
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.name}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="purple">cake</v-icon>							
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.birthday | formatDate}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="pink">wc</v-icon>							
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.gender ? 'Nam' : 'Nữ'}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="red">mail</v-icon>
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.email}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="green darken-3">phone</v-icon>
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.phone | formatPhone}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="indigo">location_on</v-icon>
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.address==null ? 'Chưa có' : currentUser.address}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>	
						<v-card-actions class="justify-center">
							<v-btn color="success" round small :to="{path: 'information/edit'}">Thay đổi thông tin</v-btn>
							<v-btn color="success" round small :to="{path: 'information/change-password'}">Thay đổi mật khẩu</v-btn>
						</v-card-actions>				
					</v-card>
				</v-flex>
			</v-layout>
			<vue-image></vue-image>
		</v-container>
	</template>

	<script>
	import ImageDialog from '@/components/ImageDialog'
	import index from '@/mixins/index'
	import {mapState} from 'vuex'
	export default {
		middleware: 'notAuthenticated',
		mixins: [index],
		components: {
			'vue-image': ImageDialog
		},
		data() {
			return {
				loading: false,
				hoverImage:false,
				showImage:false
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
			this.loading = true
			this.$store.dispatch('getUser').finally(() => {
				this.loading = false
			})
		}
	}
	</script>