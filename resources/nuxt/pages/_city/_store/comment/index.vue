<template>
	<v-container>
		<v-card style="border-radius: 10px" class="pa-2">		
			<v-card flat>
				<v-card-text>
					<v-textarea full-width class="btn-custom" auto-grow color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo hide-details background-color="grey lighten-4" flat @keyup.enter.prevent="addComment(0, 'parent')" v-model="comment" @click="focusComment">
						<v-avatar color="grey lighten-4" size="32" slot="prepend"> 
							<img src="https://scontent.fsgn5-6.fna.fbcdn.net/v/t1.0-1/p160x160/38758290_1707632599335485_5612655054730297344_n.jpg?_nc_cat=0&oh=a3ab5ff0d44c53510426072013848812&oe=5C2D3109" alt="avatar">
						</v-avatar>						
					</v-textarea>
				</v-card-text>
			</v-card>

			<v-divider></v-divider>

			<v-card-text>
				
				<v-layout  row v-for="(comment, index) in comments" :key="index">

					<div>
						<v-card flat width="32" height="32" style="margin-right: 9px"> 	
							<v-avatar color="transparent" size="32">		
								<img :src="image(comment.avatar)" alt="avatar">
							</v-avatar>						
						</v-card>
					</div>	

					<div>						
						<v-card color="grey lighten-4 card-radius" flat>
							<div class="pa-1 px-2">
								<a class="blue--text font-weight-bold"> {{comment.name}} </a> <span>{{comment.body}}</span>
							</div>
						</v-card>

						<div><v-btn icon> <v-icon size="18">thumb_up</v-icon></v-btn> 10</span> <span><v-btn small flat>Trả lời</v-btn></span> <span><a>6 phút</a> </span></div>

						<v-card flat class="d-block">
							<v-card-text class="pt-0 mt-0">
								<!-- <v-textarea full-width class="btn-custom" auto-grow color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo hide-details background-color="grey lighten-4" flat @click="focusComment">
									<v-avatar color="grey lighten-4" size="24" slot="prepend" style="top:2px"> 
										<img src="https://scontent.fsgn5-6.fna.fbcdn.net/v/t1.0-1/p160x160/38758290_1707632599335485_5612655054730297344_n.jpg?_nc_cat=0&oh=a3ab5ff0d44c53510426072013848812&oe=5C2D3109" alt="avatar">
									</v-avatar>						
								</v-textarea> -->
							</v-card-text>
						</v-card>

					</div>	




				</v-layout>
				
			</v-card-text>
			
		</v-card>
	</v-container>
</template>

<script>
import { mapState } from 'vuex'
import axios from 'axios'
import { getStoreURL, getHeader } from '@/config'
import index from '@/mixins/index'
export default {
	mixins: [index],
	data() {
		return {
			commentPopup: false,
			comment:null,
			user: {
				id: 12,
				avatar: 'https://scontent.fsgn5-6.fna.fbcdn.net/v/t1.0-1/p160x160/38758290_1707632599335485_5612655054730297344_n.jpg?_nc_cat=0&oh=a3ab5ff0d44c53510426072013848812&oe=5C2D3109',
				name: 'Nguyễn Trung Trực'
			},
			comments: null
		}
	},
	methods: {
		fetchComments: function() {
			var vm = this
			const data = {}
			const params = {name: 'fetchCommentStoreEndpoint'}
			axios.post('/api/CommentStore/'+this.store.id+'/FetchComments', data, {params, withCredentials:true}).then(response => {
				if(response.data.type === 'success') {						
					this.comments = response.data.comments
				}
			})
		},
		addComment: function(parentId = 0, request) {
			var vm = this
			if(vm.isAuth && vm.currentUser != null) {
				const data = {body: this.comment, parent_id: parentId}
				const params = {name: 'commentEndpoint'}
				axios.post('/api/CommentStore/'+this.store.id+'/Add', data, {params, headers: getHeader(), withCredentials:true}).then(response => {
					if(response.data.type === 'success') {						
						
					}
				})
			}
		},
		focusComment: function() {
			if(this.isAuth && this.currentUser != null) {
				this.commentPopup = false
				return false
			} else {
				this.$router.push({name: 'login', query: {redirect: this.$route.path}})
				return true
			}
		}
	},
	computed: {
		...mapState({
			currentUser: state => state.authStore.currentUser,
			store: state       => state.storeStore.store,
		}),
		isAuth() {
			return this.$store.getters.isAuth
		},
	},
	created() {
		this.fetchComments()
	}
}
</script>

<style scoped>
.item {
	min-height: 30px;
	min-width: 30px;
	margin: 10px;
}
</style>