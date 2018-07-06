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
					</v-toolbar>
					<v-container>
						<v-layout row wrap class="justify-center">
							<v-avatar size="150" color="grey" style="border">
								<img :src="image(currentUser.image)" alt="avatar">
							</v-avatar>
						</v-avatar>
					</v-layout>
				</v-container>				
				<v-list :dense="$vuetify.breakpoint.smAndDown">
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
							<v-list-tile-title>{{currentUser.address}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>
				</v-list>	
				<v-card-actions class="justify-center">
					<v-btn color="success" round small :to="{path: 'information/edit'}">Thay đổi thông tin</v-btn>
				</v-card-actions>				
			</v-card>
		</v-flex>
	</v-layout>
</v-container>
</template>

<script>
import index from '@/mixins/index'
import {mapState} from 'vuex'
export default {
	middleware: 'notAuthenticated',
	mixins: [index],
	data() {
		return {
			loading: false
		}
	},
	computed: {
		...mapState({
			currentUser: state => state.authStore.currentUser
		})
	},
	created() {
		this.loading = true
		this.$store.dispatch('getUser').finally(() => {
			this.loading = false
		})
	}
}
</script>