<template>
	<v-container fluid v-scroll="onScroll" :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-card style="border-radius: 10px" class="pa-2">	
			<v-card-text flat>
				<!-- TEXT FIELD COMMENT START-->
				<v-textarea id="parent" full-width auto-grow class="btn-custom" color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo hide-details :background-color="focusComment ? 'grey lighten-4' : 'grey lighten-4'" :flat="!focusComment" v-model="comment" @keyup.enter.exact="addComment(0)" @click="clickAction( null, 'comment')" @focus="focusComment = true" @blur="focusComment = false">

					<v-avatar color="grey lighten-4" size="30" slot="prepend" style="top: -10px"> 
						<img :src="image(!!currentUser ? currentUser.avatar : null)">
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
				<!-- TEXT FIELD COMMENT END-->
			</v-card-text>

			<v-divider class="pb-3"></v-divider>

			<v-scroll-y-transition group>
				<!-- LIST COMMENT START -->
				<v-layout row wrap v-for="(comment, index) in comments" :key="index">

					<v-flex class="justify-end pr-2" d-flex xs2 sm1 md1>
						<v-card color="white" max-height="32" max-width="32" style="border-radius: 32px">
							<v-avatar color="transparent" size="32">		
								<img :src="image(comment.avatar)" alt="avatar">
							</v-avatar>	
						</v-card>
					</v-flex>

					<v-flex d-flex xs10 sm11 md11>
						<v-layout row wrap>
							<v-flex>
								<!-- EDIT COMMENT START-->
								<v-layout row wrap v-if="comment.activeEdit && flagEdit == comment.id">
									<v-flex xs10 md11>
										<v-textarea id="editComment" autofocus full-width auto-grow class="btn-custom" color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo background-color="grey lighten-3" v-model="editedItem.body" hint="Nhấn esc để hủy bỏ" :persistent-hint="$vuetify.breakpoint.smAndUp"  @keyup.esc.exact="comment.activeEdit = false">
										</v-textarea>
										<v-layout row wrap class="pt-2">
											<v-spacer></v-spacer>
											<v-btn outline small color="red darken-1"  style="top: -12px" @click.prevent="comment.activeEdit = false" class="my-0 py-0" round>Hủy</v-btn>
											<v-btn color="blue" :loading="comment.process" small style="top: -12px" class="my-0 py-0 white--text" @click.prevent="updateComment(comment)" round>Lưu</v-btn>
										</v-layout>	
									</v-flex>
								</v-layout>
								<!-- EDIT COMMENT END-->
								<v-layout v-else  row wrap>
									<v-flex xs10 md11>				

										<v-card color="grey lighten-4" class="card-radius" flat style="min-height: 32px">
											<v-card-text class="pa-1 px-2">
												<a class="blue--text font-weight-bold"> {{comment.name}} </a>
												<div>{{ comment.body }}</div>
											</v-card-text>
										</v-card>

									</v-flex>

									<v-flex  xs2 md1 v-if="comment.owned">
										<v-card color="transparent" flat>
											<v-menu bottom left>
												<v-btn slot="activator" icon class="ma-0" >
													<v-icon>more_horiz</v-icon>
												</v-btn>

												<v-list dense >
													<v-list-tile @click.prevent="editing(comment)" avatar>
														<v-list-tile-avatar><v-icon>edit</v-icon></v-list-tile-avatar>
														<v-list-tile-content>
															<v-list-tile-title>Chỉnh sửa</v-list-tile-title>
														</v-list-tile-content>
													</v-list-tile>
													<v-list-tile @click.prevent="removing(comments, comment)" avatar>
														<v-list-tile-avatar><v-icon>delete_outline</v-icon></v-list-tile-avatar>
														<v-list-tile-content>
															<v-list-tile-title>Xóa</v-list-tile-title>
														</v-list-tile-content>
													</v-list-tile>
												</v-list>
											</v-menu>
										</v-card>
									</v-flex>
								</v-layout>

							</v-flex>

							<v-flex d-flex xs12>
								<v-layout row wrap>

									<v-flex	d-flex xs12>
										<v-card	color="transparent" flat>
											<span>
												<v-tooltip top>
													<v-btn slot="activator" icon @click="clickAction(comment, 'like')" class="ma-0"> 
														<v-icon size="18" :color="comment.checkedLike ? 'blue' : ''">thumb_up</v-icon>
													</v-btn> 
													<span>{{comment.checkedLike ? 'Bỏ thích' : 'Thích'}}</span>
												</v-tooltip>						
												{{comment.likes}}		
											</span>
											<span>
												<v-btn small flat @click="clickAction(comment, 'reply')" class="pa-0 ma-0">
													<v-icon >reply</v-icon> 
													<span>Trả lời</span>
												</v-btn>
											</span>
											<span class="grey--text caption">{{ comment.createdAt.date | formatDateTimeHumanize }}</span>
										</v-card>
									</v-flex>

									<v-flex	d-flex xs12>
										<v-card	color="transparent" flat>

											<v-card-text class="pa-1"  v-if="comment.totalReply > 0 && comment.replies.length == 0">
												<a @click="fetchReplies(comment)"  class="body-1 primary--text">
													<v-icon color="primary" size="20">subdirectory_arrow_right</v-icon> Xem thêm {{comment.totalReply}} phản hồi
													<v-progress-circular indeterminate color="grey" size="18" v-show="comment.process" ></v-progress-circular>
												</a>
											</v-card-text>

											<v-scroll-y-transition group>
												<!-- LIST REPLIES START-->
												<v-layout row wrap v-for="(reply, i) in comment.replies" :key="i" class="pa-0">

													<v-card color="white" class="mx-2" max-height="24" max-width="24" style="border-radius: 32px">
														<v-avatar color="transparent" size="24">		
															<img :src="image(reply.avatar)" alt="avatar">
														</v-avatar>	
													</v-card>

													<v-flex d-flex xs10 sm11 md11>
														<!-- EDIT REPLY -->
														<v-layout row wrap v-if="reply.activeEdit && flagEdit == reply.id">
															<v-flex xs10 md11>
																<v-textarea id="editComment" autofocus full-width auto-grow class="btn-custom" color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo background-color="grey lighten-3" v-model="editedItem.body" hint="Nhấn esc để hủy bỏ" :persistent-hint="$vuetify.breakpoint.mdAndUp"  @keyup.esc.exact="reply.activeEdit = false">
																</v-textarea>
																<v-layout row wrap class="pt-2">
																	<v-spacer v-if="$vuetify.breakpoint.smAndUp"></v-spacer>
																	<v-btn outline small color="red darken-1" style="top: -12px" @click.prevent="reply.activeEdit = false" class="ma-1 pa-1" round>Hủy</v-btn>
																	<v-btn color="blue" small style="top: -12px" class="ma-1 pa-1 white--text" :loading="reply.process" @click.prevent="updateComment(reply)" round>Cập nhật</v-btn>
																</v-layout>	
															</v-flex>
														</v-layout>

														<v-layout v-else row wrap>

															<v-flex xs12>
																<v-layout row wrap>

																	<v-flex xs10 sm11 md11>
																		<v-card color="grey lighten-4" class="card-radius" flat style="min-height: 32px">
																			<v-card-text class="pa-1 px-2">
																				<a class="blue--text font-weight-bold"> {{reply.name}} </a> 
																				<div>{{reply.body}}</div>
																			</v-card-text>
																		</v-card>
																	</v-flex>


																	<v-card color="transparent" flat v-if="reply.owned">
																		<v-menu bottom left>
																			<v-btn slot="activator" icon class="ma-0" >
																				<v-icon>more_horiz</v-icon>
																			</v-btn>

																			<v-list dense >
																				<v-list-tile @click.prevent="editing(reply)" avatar>
																					<v-list-tile-avatar><v-icon>edit</v-icon></v-list-tile-avatar>
																					<v-list-tile-content>
																						<v-list-tile-title>Chỉnh sửa</v-list-tile-title>
																					</v-list-tile-content>
																				</v-list-tile>
																				<v-list-tile @click.prevent="removing(comment.replies, reply)" avatar>
																					<v-list-tile-avatar><v-icon>delete_outline</v-icon></v-list-tile-avatar>
																					<v-list-tile-content>
																						<v-list-tile-title>Xóa</v-list-tile-title>
																					</v-list-tile-content>
																				</v-list-tile>
																			</v-list>
																			
																		</v-menu>
																	</v-card>

																</v-layout>

															</v-flex>

															<v-flex d-flex xs12>
																<v-layout row wrap>
																	<v-flex	d-flex xs12>
																		<v-card	color="transparent" flat>
																			<span>
																				<v-tooltip top>
																					<v-btn slot="activator" icon @click="clickAction(reply, 'like')" class="ma-0"> 
																						<v-icon size="18" :color="reply.checkedLike ? 'blue' : ''">thumb_up</v-icon>
																					</v-btn> 
																					<span>{{reply.checkedLike ? 'Bỏ thích' : 'Thích'}}</span>
																				</v-tooltip>						
																				{{reply.likes}}		
																			</span>
																			<span>
																				<v-btn small flat @click="clickAction(comment, 'reply')" class="ma-0 pa-0">
																					Trả lời
																				</v-btn>
																			</span>
																			<span class="grey--text caption">
																				{{ reply.createdAt.date | formatDateTimeHumanize }}						
																			</span>
																		</v-card>
																	</v-flex>
																</v-layout>
															</v-flex>
														</v-layout>
													</v-flex>
												</v-layout>
												<!-- LIST REPLIES END -->
											</v-scroll-y-transition>

											<v-card-text v-if="comment.replies.length < comment.totalReply && comment.replies.length > 0 " class="pa-1">
												<a @click="fetchReplies(comment)" >
													Xem thêm bình luận
													<v-progress-circular indeterminate color="grey" size="18" v-if="comment.process" ></v-progress-circular>
												</a>
											</v-card-text>

											<v-card-text  v-show="comment.activeReply" class="px-0">


												<v-flex xs12 sm11 md11>
													<!-- TEXT FIELD REPLY START -->
													<v-textarea	id="reply" full-width class="btn-custom"	auto-grow	color="black" placeholder="Viết cảm nghĩ của bạn..." rows="1" solo	hide-details :background-color="comment.focusCommentInput ? 'grey lighten-4' : 'grey lighten-4'"	:flat="!comment.focusCommentInput"	v-model="comment.reply" @keyup.enter.exact="addReply(comment)"	@click="clickAction( null, 'comment')" @focus="comment.focusCommentInput = true"	@blur="comment.focusCommentInput = false">

														<v-avatar color="grey lighten-4" size="30" slot="prepend" style="top: -10px"> 
															<img src="https://scontent.fsgn5-6.fna.fbcdn.net/v/t1.0-1/p160x160/38758290_1707632599335485_5612655054730297344_n.jpg?_nc_cat=0&oh=a3ab5ff0d44c53510426072013848812&oe=5C2D3109" alt="avatar">
														</v-avatar>

														<v-tooltip top slot="append">
															<v-scroll-y-transition slot="activator" >
																<v-btn v-if="comment.focusCommentInput" class="ma-0 pa-0" style="top:-10px" icon small :color="comment.focusCommentInput ? 'blue' : 'grey lighten-3'" :outline="comment.focusCommentInput" @click.prevent="addReply(comment)" :loading="comment.process">
																	<v-icon :color="comment.focusCommentInput ? 'blue' : 'grey'">send</v-icon>
																</v-btn>	
															</v-scroll-y-transition>
															<span>Gửi bình luận</span>
														</v-tooltip>			

													</v-textarea>													
													<!-- TEXT FIELD REPLY END -->
												</v-flex>
											</v-card-text>

										</v-card>
									</v-flex>
								</v-layout>
							</v-flex>
						</v-layout>
					</v-flex>
				</v-layout>
				<!-- LIST COMMENT END -->
			</v-scroll-y-transition>
			<!-- LAZY LOADING START-->
			<v-card v-if="loading" color="transparent" dark flat>
				<v-card-text class="text-xs-center">
					<v-progress-circular
					indeterminate
					color="grey"
					></v-progress-circular>
				</v-card-text>
			</v-card>	
			<!-- LAZY LOADING END-->
		</v-card>
		
		<vue-confirm ref="confirm"></vue-confirm>
	</v-container>
</template>

<script>
	const fetchEndpoint       = "fetchCommentStoreEndpoint"
	const commentEndpoint     = "commentStoreEndpoint"
	const likeCommentEndPoint = "likeCommentEndPoint"
	import { mapState } from 'vuex'
	import axios from 'axios'
	import { getStoreURL, getHeader } from '@/config'
	import index from '@/mixins/index'
	import confirmDialog from '@/components/commons/confirmDialog'
	export default {
		mixins: [index],
		scrollToTop: true,
		components: {
			'vue-confirm': confirmDialog
		},
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
				showEdit: false,
				editedItem: {
					body:null
				},
				flagEdit: 0
			}
		},
		methods: {
			//GET COMMENTS
			fetchComments: function() {
				var vm        = this
				var offset    = vm.comments.length
				var array     = []
				const storeId = vm.store.id

				const data    = { offset: offset }
				const params  = { endpoint: fetchEndpoint }
				
				if(!vm.loading && !vm.end) {
					vm.loading = true
					axios.post('/api/CommentStore/'+storeId+'/FetchComments', data, { params, withCredentials:true }).then(response => {
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
			fetchReplies: function(comment) {
				var vm         = this 
				const parentId = comment.id
				var offset     = comment.replies.length
				const storeId  = vm.store.id

				const data     = { parentId: parentId, offset: offset }
				const params   = { endpoint: fetchEndpoint }

				if(!comment.activeReply) {					
					comment.activeReply = true
				}
				if(!comment.process) {

					comment.process = true
					setTimeout(() => {

						axios.post('/api/CommentStore/'+storeId+'/FetchReplies', data, { params, withCredentials:true }).then(response => {

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
				var vm        = this
				var comment   = this.comment
				const storeId = vm.store.id

				const data    = { body: comment, parent_id: parentId }
				const params  = { endpoint: commentEndpoint }

				if(!vm.processComment) {

					vm.processComment = true
					
					setTimeout(() => {

						axios.post('/api/CommentStore/'+storeId+'/AddComment', data, { params, withCredentials:true }).then(response => {
							if(response.data.type === 'success') {
								vm.defaultComment(response.data.comment)				
								vm.comments.unshift(response.data.comment)
								vm.comment = null
							}
						}).finally(() => {
							vm.processComment = false
						})

					},300)
				}
			},
			//NEW REPLY
			addReply: function(comment) {

				var vm         = this
				var body       = comment.reply
				const parentId = comment.id
				const storeId  = vm.store.id

				const data     = { body: body, parent_id: parentId }
				const params   = { endpoint: commentEndpoint }

				if(!comment.process) {

					comment.process = true

					setTimeout(() => {

						axios.post('/api/CommentStore/'+storeId+'/AddReply', data, { params, withCredentials:true }).then(response => {
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
				return Object.assign(comment, {'reply': null, 'focusCommentInput': false, 'activeReply': false, 'process': false, 'replies': [], activeEdit: false})
			},
			//CLICK ACTION LIKE
			toggleLikeComment: function(comment) {
				var vm        = this
				var commentId = comment.id

				const data    = {}
				const params  = { endpoint: likeCommentEndPoint }

				if(!vm.processLikeComment) {
					vm.processLikeComment = true
					comment.checkedLike = !comment.checkedLike
					
					setTimeout(() => {	
						axios.post('/api/LikeComment/'+commentId+'/Toggle', data, { params, withCredentials:true}).then(response => {
							if(response.status === 200) {			
								comment.likes     = response.data.likes
							}
						}).finally(() => {
							vm.processLikeComment = false
						})

					}, 300)
					
				}
			},
			// SHOW COMMENT EDIT
			editing(comment) {
				var vm             = this
				comment.activeEdit = true
				vm.flagEdit        = comment.id
				Object.assign(vm.editedItem, comment)
			},
			// UPDATE COMMENT
			updateComment(comment) {
				var vm        = this
				const storeId = vm.store.id

				const data 	  = Object.assign({}, vm.editedItem)
				const params  = { endpoint: commentEndpoint }

				if(!comment.process) {
					comment.process = true
					setTimeout(() => {
						
						axios.post('/api/CommentStore/'+storeId+'/UpdateComment', data, {params, withCredentials: true}).then(response => {
							if(response.status === 200) {
								comment.body   = response.data.comment.body							
							}
						}).finally(() => {
							vm.flagEdit        = 0
							comment.process    = false
							comment.activeEdit = false
						})

					}, 300)
				}			

			},
			removing(arrayComments, comment) {
				var vm        = this
				const storeId = vm.store.id
				
				const data    = Object.assign({}, comment)
				const params  = { endpoint: commentEndpoint }
				this.$refs.confirm.open('Xóa', 'xóa bình luận').then((confirm) => {
					if(confirm) {

						if(!comment.process) {
							comment.process = true
							setTimeout(() => {

								axios.post('/api/CommentStore/'+storeId+'/RemoveComment', data, { params, withCredentials: true }).then(response => {
									if(response.status === 200) {
										arrayComments.splice(arrayComments.indexOf(comment), 1)
									}
								}).finally(() => {
									comment.process = false
								})		

							}, 300)				
						}

					}
					return 					
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