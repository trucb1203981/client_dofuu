<template>
	<v-dialog v-model="dialog" persistent max-width="500px" :fullscreen="$vuetify.breakpoint.smAndDown">
		<v-card color="grey lighten-4">
			<v-toolbar flat dense color="white">
				<span class="headline">{{title}}</span>
			</v-toolbar>
			<v-divider></v-divider>
			<v-layout row wrap class="justify-center pt-3">			
				<vue-croppie ref="croppieRef"  @result="result" :enableResize="false" :enableExif="true" :mouseWheelZoom="false"	@update="update" :viewport="{ width: 250, height: 250, type: 'circle'}" :boundary="{width: 300, height: 300}">
				</vue-croppie>
				<v-btn color="white" @click.stop.prevent="onPickImage" :loading="loading" round small class="black--text"><v-icon left >camera_alt</v-icon> Tải ảnh</v-btn>
				<v-btn color="white" small class="black--text elevation-1" icon @click="rotate(90)"><v-icon>rotate_left</v-icon></v-btn>
				<v-btn color="white" small class="black--text" icon @click="rotate(-90)"><v-icon>rotate_right</v-icon></v-btn>
				<input type="file" style="display:none" ref="fileInput" accept="image/*" v-validate="'mimes:image/jpeg, image/png, image/jpg'" @change="onImagePicked">
			</v-layout>		
			<v-divider></v-divider>
			<v-card-actions class="white">
				<v-spacer></v-spacer>
				<v-btn color="red darken-1" outline  small class="white--text" round flat @click.native="cancel">Hủy</v-btn>
				<v-btn color="blue" round flat @click.native="agree" small :disabled="disabled" :loading="process">Thay đổi</v-btn>
			</v-card-actions>
		</v-card>
		<vue-confirm ref="confirm"></vue-confirm>
	</v-dialog>
</template>
<script>
	import {mapState} from 'vuex'
	import index from '@/mixins/index'
	import ConfirmDialog from '@/components/commons/confirmDialog'
	export default {
		mixins: [index],
		components: {
			'vue-confirm': ConfirmDialog
		},
		data() {
			return {
				imageUrl: null,
				cropped: null,
				dialog:false,
				width:0,
				height:0,
				title: null,
				resolve: null,
				reject: null,
				viewport: {
					width: 250,
					height: 250, 
					type: 'circle'
				},
				boundary: {
					width: 300, 
					height: 300
				},
				loading: false,
				process: false,
				disabled: true
			}
		},
		methods: {
			open(title) {
				this.dialog  = true
				this.title   = title
				return new Promise((resolve, reject) => {
					this.resolve = resolve
					this.reject  = reject
				})

			},
			cancel() {
				var vm     = this
				if(!!vm.cropped) {
					vm.$refs.confirm.open('Thay đổi chưa lưu', 'các thay đổi bạn thực hiện đối với ảnh đại diện của mình sẽ không được lưu nếu bạn đóng hộp thoại').then((confirm) => {
						if(confirm) {
							vm.cropped = null
							vm.refresh()			
							vm.resolve({status: false})
							vm.dialog  = false
						}
					})
				} else {
					vm.resolve({status: false})
					vm.dialog  = false
				}
			},
			agree() {
				var vm     = this
				if(!vm.process) {
					vm.process = !vm.process
					setTimeout(()=> {
						vm.resolve({status: true, avatar: vm.cropped})
						vm.process = !vm.process
						vm.cropped = null
						vm.refresh()
						vm.dialog  = false
					}, 200)					
				}				

			},
			result(output) {				

			},
			update(val) {
				let options = {
					format: 'jpeg', 
					size: { width: 520, height: 520 },
					circle: false
				}
				this.$refs.croppieRef.result(options, (output) => {
					this.cropped = output;
				});
			},
			// Rotates the image
			rotate(rotationAngle) {
				this.$refs.croppieRef.rotate(rotationAngle);
			},
			//Input pick image
			onPickImage: function(e) {
				this.$refs.fileInput.click();
			},
			//Transfer and resize Image
			onImagePicked: function(event) {
				let vm     = this				
				const file = event.target.files || event.dataTransfer.files
				if(!file.length) {
					return 
				} 
				let filename      = file[0].name
				vm.loading        = !vm.loading
				const fileReader  = new FileReader()
				fileReader.onload = (e) => {
					var image = new Image()
					image.src = fileReader.result
					setTimeout(() => {
						this.$refs.croppieRef.bind({
							url: image.src,
						});				
						vm.loading = !vm.loading
					}, 1000)
					
				}
				fileReader.readAsDataURL(file[0])
			},
			refresh() {
				this.$refs.fileInput.value = ""
				this.$refs.croppieRef.refresh()
			}
		},
		watch: {
			'cropped': function(val, oldVal) {
				if(val) {
					this.disabled = false
				} else {
					this.disabled = true
				}
			}
		}
	}
</script>

<style>


</style>