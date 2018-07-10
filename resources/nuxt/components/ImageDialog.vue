<template>
	<v-dialog v-model="dialog" persistent max-width="500px" :fullscreen="$vuetify.breakpoint.smAndDown">
		<v-card>
			<v-card-title>
				<span class="headline">User Profile</span>
			</v-card-title>
			<v-card-text>
				<vue-croppie ref=croppieRef  @result="result" :enableResize="true" :enableExif="true" :mouseWheelZoom="false"	@update="update" :viewport="{ width: 250, height: 250}" :boundary="{width: 300, height: 300}">
				</vue-croppie>

				<v-layout row wrap class="justify-center">			
					<v-btn color="blue" @click.stop.prevent="onPickImage" round small class="white--text"><v-icon left >camera_alt</v-icon> Tải ảnh</v-btn>
					<v-btn color="white" small class="black--text" icon @click="rotate(-90)"><v-icon>rotate_left</v-icon></v-btn>
					<v-btn color="white" small class="black--text" icon @click="rotate(90)"><v-icon>rotate_right</v-icon></v-btn>
					<input type="file" style="display:none" ref="fileInput" accept="image/*" v-validate="'mimes:image/jpeg, image/png, image/jpg'" @change="onImagePicked">
				</v-layout>
			</v-card-text>			
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn color="blue darken-1" flat @click.native="dialog = false">Close</v-btn>
				<v-btn color="blue darken-1" flat @click.native="dialog = false">Save</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>
<script>
import {mapState} from 'vuex'
import index from '@/mixins/index'
export default {
	mixins: [index],
	props: ['avatar'],
	data() {
		return {
			imageUrl: null,
			dialog:false,
			width:0,
			height:0
		}
	},
	methods: {
		result(output) {
			console.log(output)
			this.cropped = output;
		},
		update(val) {
			console.log(val);
		},
		rotate(rotationAngle) {
            // Rotates the image
            this.$refs.croppieRef.rotate(rotationAngle);
        },
		//Input pick image
		onPickImage: function(e) {
			this.$refs.fileInput.click();
		},
		//Transfer and resize Image
		onImagePicked: function(event) {
			let vm = this
			const file = event.target.files || event.dataTransfer.files
			let filename = file[0].name

			if(!file.length) {
				return 
			} 

			const fileReader = new FileReader()
			fileReader.onload = (e) => {
				var image = new Image()
				image.src = fileReader.result
				this.$refs.croppieRef.bind({
					url: image.src,
				});				
			}
			fileReader.readAsDataURL(file[0])
		},
	},
	computed: {
		...mapState({
			imageDialog: state => state.authStore.imageDialog
		})
	},
	watch: {
		avatar(val) {
			console.log(val)
			if(val == null) {

				this.imageUrl =  imageUrl + '/img/default.png'

			} else {
				if(val.lastIndexOf('data')>-1) {

					this.imageUrl = val

				} else {

					this.imageUrl = imageUrl + val
				}
			}
		},
		imageDialog: function(val) {
			if(val) this.dialog = true
		},
		dialog(val) {
			var w = window
			var d = document
			var e = d.documentElement
			var g = d.getElementsByTagName('body')[0]
			var x = w.innerWidth || e.clientWidth || g.clientWidth
			var y = w.innerHeight|| e.clientHeight|| g.clientHeight
			if(!val) {
				this.$store.commit('CLOSE_IMAGE_DIALOG')
			}
		}
	}
}
</script>

<style>


</style>