<template>
	<div>
		<vue-croppie ref=croppieRef  @result="result" :enableResize="false" :enableExif="true" :mouseWheelZoom="false"	@update="update" :viewport="{ width: 150, height: 150, type: 'circle' }" :boundary="{width: 200, height: 200}">
		</vue-croppie>
		<!-- <a @click.stop.prevent="onPickImage" style="width:inherit;height:inherit;border-radius:inherit">
			<v-avatar  size="150"  color="primary">
				<img :src="imageUrl != null ? imageUrl : 'https://www.dofuu.com/img/default.png'" alt="avatar" height="480" width="720" style=" cursor:pointer">
			</v-avatar>
		</a> -->
		<v-layout row wrap class="justify-center">			
			<v-btn color="blue" @click.stop.prevent="onPickImage" round small class="white--text"><v-icon left >camera_alt</v-icon> Tải ảnh</v-btn>
			<v-btn color="white" small class="black--text" icon @click="rotate(-90)"><v-icon>rotate_left</v-icon></v-btn>
			<v-btn color="white" small class="black--text" icon @click="rotate(90)"><v-icon>rotate_right</v-icon></v-btn>
			<input type="file" style="display:none" ref="fileInput" accept="image/*" v-validate="'mimes:image/jpeg, image/png, image/jpg'" @change="onImagePicked">
		</v-layout>
		
	</div>
</template>

<script>
import index from '@/mixins/index'
export default {
	mixins: [index],
	props: ['avatar'],
	data() {
		return {
			imageUrl: null
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
		}
	}
}
</script>

<style>


</style>