<template>
	<v-container v-scroll="onScroll">
		<v-card style="border-radius: 10px" class="pa-2">	
			<v-card-text flat>
				<!-- TEXT FIELD COMMENT -->
				<v-textarea id="parent" full-width auto-grow class="btn-custom" color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo hide-details :background-color="focusComment ? 'grey lighten-3' : 'grey lighten-3'" :flat="!focusComment" v-model="comment" @keyup.enter.exact="addComment(0)" @click="clickAction( null, 'comment')" @focus="focusComment = true" @blur="focusComment = false">

					<v-avatar color="grey lighten-4" size="30" slot="prepend" style="top: -10px"> 
						<img src="https://scontent.fsgn5-6.fna.fbcdn.net/v/t1.0-1/p160x160/38758290_1707632599335485_5612655054730297344_n.jpg?_nc_cat=0&oh=a3ab5ff0d44c53510426072013848812&oe=5C2D3109" alt="avatar">
					</v-avatar>			

					<v-tooltip top slot="append">
						<v-scroll-y-transition slot="activator"  >
							<v-btn  v-if="focusComment" class="ma-0" style="top:-10px" icon small :color="focusComment ? 'blue' : 'grey lighten-3'" :outline="focusComment" @click.prevent="addComment(0)" :loading="processComment">
								<v-icon :color="focusComment ? 'blue' : 'grey'" size="20">send</v-icon>
							</v-btn>	
						</v-scroll-y-transition>	
						<span>Gửi bình luận</span>
					</v-tooltip>							

				</v-textarea>

			</v-card-text>

			<v-divider></v-divider>

			<v-card-text>
				<!-- LIST COMMENTS -->
				<v-layout fill-height row v-for="(comment, index) in comments" :key="index">

					<div>
						<v-card flat width="32" height="32" style="margin-right: 9px"> 	
							<v-avatar color="transparent" size="32">		
								<img :src="image(comment.avatar)" alt="avatar">
							</v-avatar>						
						</v-card>
					</div>	

					<v-flex xs12>

						<v-card color="grey lighten-4 card-radius" flat>
							<div class="pa-1 px-2">
								<a class="blue--text font-weight-bold"> {{comment.name}} </a> <span>{{comment.body}}</span>
							</div>
						</v-card>

						<div>
							<span>
								<v-tooltip top>
									<v-btn slot="activator" icon @click="clickAction(comment, 'like')"> 
										<v-icon size="18" :color="comment.like ? 'blue' : ''">thumb_up</v-icon>
									</v-btn> 
									<span>{{comment.like ? 'Bỏ thích' : 'Thích'}}</span>
								</v-tooltip>						
								{{comment.likes}}		
							</span>
							<span>
								<v-btn small flat @click="clickAction(comment, 'reply')">
									<v-icon >reply</v-icon> 
									<span>Trả lời</span>
								</v-btn>
							</span>
							<span><a>{{ comment.createdAt.date | formatDateTimeHumanize }}</a></span>
						</div>

						<v-card flat class="d-block" >

							<v-card-text class="py-0">

								<a @click="fetchCommentReplies(comment)"  v-if="comment.totalReply > 0 && comment.replies.length == 0" class="body-1 primary--text">
									<v-icon color="primary" size="20">subdirectory_arrow_right</v-icon> Xem thêm {{comment.totalReply}} phản hồi
									<v-progress-circular indeterminate color="grey" size="18" v-show="comment.process" ></v-progress-circular>
								</a>

								<v-scroll-y-transition group>
									<!-- LIST COMMENT REPLIES -->
									<v-layout fill-height row v-for="(childComment, i) in comment.replies" :key="i" >

										<div>
											<v-card flat width="32" height="32" style="margin-right: 9px"> 	
												<v-avatar color="transparent" size="32">		
													<img :src="image(childComment.avatar)" alt="avatar">
												</v-avatar>						
											</v-card>
										</div>	

										<div>
											<v-card color="grey lighten-4 card-radius" flat>
												<div class="pa-1 px-2">
													<a class="blue--text font-weight-bold"> {{childComment.name}} </a> <span>{{childComment.body}}</span>
												</div>
											</v-card>
											<div>
												<span>
													<v-tooltip top>
														<v-btn slot="activator" icon @click="clickAction(childComment, 'like')"> 
															<v-icon size="18" :color="childComment.like ? 'blue' : ''">thumb_up</v-icon>
														</v-btn> 
														<span>{{childComment.like ? 'Bỏ thích' : 'Thích'}}</span>
													</v-tooltip>						
													{{childComment.likes}}		
												</span>
												<span>
													<v-btn small flat @click="clickAction(comment, 'reply')">
														Trả lời
													</v-btn>
												</span>
												<span>
													<a>{{ childComment.createdAt.date | formatDateTimeHumanize }}
													</a> 
												</span>
											</div>
										</div>

									</v-layout>
								</v-scroll-y-transition>
								<a @click="fetchCommentReplies(comment)"  v-if="comment.replies.length < comment.totalReply && comment.replies.length > 0 ">
									Xem thêm bình luận
									<v-progress-circular indeterminate color="grey" size="18" v-if="comment.process" ></v-progress-circular>
								</a>

							</v-card-text>

							<v-card-text  v-show="comment.activeReply" class="pt-0">

								<!-- TEXT FIELD REPLY -->
								<v-textarea	id="childComment" full-width class="btn-custom"	auto-grow	color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo	hide-details :background-color="comment.focusCommentInput ? 'grey lighten-3' : 'grey lighten-3'"	:flat="!comment.focusCommentInput"	v-model="comment.reply" @keyup.enter.exact="addCommentReply(comment)"	@click="clickAction( null, 'comment')" @focus="comment.focusCommentInput = true"	@blur="comment.focusCommentInput = false">

									<v-avatar color="grey lighten-4" size="30" slot="prepend" style="top: -10px"> 
										<img src="https://scontent.fsgn5-6.fna.fbcdn.net/v/t1.0-1/p160x160/38758290_1707632599335485_5612655054730297344_n.jpg?_nc_cat=0&oh=a3ab5ff0d44c53510426072013848812&oe=5C2D3109" alt="avatar">
									</v-avatar>			

									<v-tooltip top slot="append">
										<v-scroll-y-transition slot="activator" >
											<v-btn v-if="comment.focusCommentInput" class="ma-0 pa-0" style="top:-10px" icon small :color="comment.focusCommentInput ? 'blue' : 'grey lighten-3'" :outline="comment.focusCommentInput" @click.prevent="addCommentReply(comment)" :loading="comment.process">
												<v-icon :color="comment.focusCommentInput ? 'blue' : 'grey'">send</v-icon>
											</v-btn>	
										</v-scroll-y-transition>
										<span>Gửi bình luận</span>
									</v-tooltip>						

								</v-textarea>

							</v-card-text>

						</v-card>

					</v-flex>

				</v-layout>

			</v-card-text>
			<!-- LAZY LOADING -->
			<v-card v-if="loading" color="transparent" dark flat>
				<v-card-text class="text-xs-center">
					<v-progress-circular
					indeterminate
					color="grey"
					></v-progress-circular>
				</v-card-text>
			</v-card>	
		</v-card>
	</v-container>
</template>

<script>
	import { mapState } from 'vuex'
	import axios from 'axios'
	import { getStoreURL, getHeader } from '@/config'
	import index from '@/mixins/index'
	const fetchEndpoint            = "fetchCommentStoreEndpoint"
	const addEndpoint              = "commentStoreEndpoint"
	const likeCommentEndPoint      = "likeCommentEndPoint"
	const checkLikeCommentEndPoint = "checkLikeCommentEndPoint"
	export default {
		mixins: [index],
		scrollToTop: true,
		data() {
			return {
				loading: false,
				comment:null,
				comments: [],
				focusComment: false,
				processComment: false,
				processLikeComment: false,
				trigger: 100,
				end: false,
			}
		},
		methods: {
			//GET COMMENTS
			fetchComments: function() {
				var vm       = this
				var offset   = vm.comments.length
				var array    = []
				
				const data   = { offset: offset }
				const params = { name: fetchEndpoint }
				
				if(!vm.loading && !vm.end) {
					vm.loading = true
					axios.post('/api/CommentStore/'+vm.store.id+'/FetchComments', data, { params, withCredentials:true }).then(response => {
						if(response.data.type === 'success') {		
							if(response.data.comments.length == 0) {
								vm.end = true
							}				
							array = response.data.comments.map(item => {
								vm.defaultComment(item)
								vm.comments.push(item)
								return item
							})
						}
					}).finally(() => {
						vm.loading = false
					})
				}
				
			},
			//GET REPLIES
			fetchCommentReplies: function(comment) {
				var vm         = this 
				const parentId = comment.id
				var offset     = comment.replies.length
				
				const data     = { parentId: parentId, offset: offset }
				const params   = { name: fetchEndpoint }

				if(!comment.activeReply) {					
					comment.activeReply = true
				}
				if(!comment.process) {

					comment.process = true
					setTimeout(() => {

						axios.post('/api/CommentStore/'+vm.store.id+'/FetchCommentReplies', data, { params, withCredentials:true }).then(response => {

							if(response.data.type === 'success') {

								response.data.comments.map(item => {
									vm.defaultComment(item)
									comment.replies.push(item)
								})

								vm.comments.map(item => {
									if(item.id === comment.id) {
										Object.assign({}, comment)
									}
								})

							} 
						}).finally(() => {
							comment.process = false
						})

					}, 300) 
				}

			},
			//NEW COMMENT
			addComment: function(parentId = 0) {
				var vm         = this
				var comment    = this.comment

				const data = { body: comment, parent_id: parentId }
				const params = { name: addEndpoint }

				if(!vm.processComment) {

					vm.processComment = true
					
					setTimeout(() => {

						axios.post('/api/CommentStore/'+vm.store.id+'/AddComment', data, { params, withCredentials:true }).then(response => {
							if(response.data.type === 'success') {
								vm.defaultComment(response.data.comment)				
								vm.comments.unshift(response.data.comment)
								vm.parentComment = null
							}
						}).finally(() => {
							vm.processComment = false
						})

					},300)
				}
			},
			//NEW REPLY
			addCommentReply: function(comment) {

				var vm         = this
				var body       = comment.reply
				const parentId = comment.id

				const data   = { body: body, parent_id: parentId }
				const params = { name: addEndpoint }

				if(!comment.process) {

					comment.process = true

					setTimeout(() => {

						axios.post('/api/CommentStore/'+this.store.id+'/AddCommentReply', data, { params, withCredentials:true }).then(response => {
							if(response.data.type === 'success') {			
								vm.defaultComment(response.data.comment)			
								comment.replies.push(response.data.comment)
								comment.reply = null
							}
						}).finally(() => {
							comment.process = false
						})

					}, 300)
				}
			},
			//CHECK ACTION 
			clickAction: function(comment = null, name) {
				var btnName = new String(name).toLowerCase()
				if(this.isAuth) {
					switch(btnName) {
						case 'comment':
						return
						break
						case 'reply':
						if(!comment.activeReply) {
							comment.activeReply = true
						}
						return
						break
						case 'like':
						this.toggleLikeComment(comment)
						return
						break
					} 
				} else {
					this.$router.push({name: 'login', query: {redirect: this.$route.path}})
				}
			},
			//SET DEFAULT
			defaultComment: function(comment) {
				return Object.assign(comment, {'reply': null, 'focusCommentInput': false, 'activeReply': false, 'process': false, 'replies': [], like: false})
			},
			//CLICK ACTION LIKE
			toggleLikeComment: function(comment) {
				var vm        = this
				var commentId = comment.id
				const data    = {}
				const params  = { name: likeCommentEndPoint }

				if(!vm.processLikeComment) {
					vm.processLikeComment = true
					
					setTimeout(() => {
						
						axios.post('/api/LikeComment/'+commentId+'/Toggle', data, { params, withCredentials:true}).then(response => {
							if(response.status === 200) {
								comment.like  = !comment.like
								comment.likes = response.data.comment.likes
							}
						}).finally(() => {
							vm.processLikeComment = false
						})

					}, 300)
					
				}
			},
			//CHECK LIKE BY USER
			checkLikeComment: function() {
				var vm = this
				var data = {}
				const params = {name: checkLikeCommentEndPoint}

				return axios.post('/api/LikeComment/'+vm.store.id+'/Check', data, { params, withCredentials:true }).then(response => {
					if(response.data.type === 'success') {						
						
					}
				})
			},
			//ONSCROLL LAZY LOAD
			onScroll: async function(e) {
				var vm = this
				if(window.innerHeight + window.scrollY >= (document.body.offsetHeight - vm.trigger) ) {
					if(!vm.loading) {
						vm.fetchComments()
					}
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
			if(this.isAuth) {
				this.checkLikeComment()
			}
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