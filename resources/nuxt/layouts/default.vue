<template>
	<v-app id="app" standalone class="grey lighten-2">
		<vue-toolbar></vue-toolbar>
		<vue-left-navigation class="hidden-md-and-up"></vue-left-navigation>
		<v-content style="min-height: 400px">
			<nuxt/>
			<v-dialog v-model="dialog" max-width="500">
				<v-card>
					<v-toolbar dense color="grey lighten-4" class="elevation-0">
						<v-avatar size="24px" tile>
							<img src="~/static/dofuu24x24.png">
						</v-avatar>
						<v-toolbar-title>
							Thông Báo Nghỉ Tết Xuân Kỷ Hợi 2019
						</v-toolbar-title>
					</v-toolbar>
					<v-divider></v-divider>
					<v-card-text class="text-xs-center">
						<div class="body-2">
							- Quý khách thân mến, công ty Dofuu xin phép được tạm nghỉ tết từ 30 tết (04/02/2019) đến hết mùng 5 (09/02/2019). Sau đó từ mùng 6 (10/02/2019) hoạt động trở lại bình thường.
						</div>
						<div class="body-2">
							- Xin trân trọng cám ơn quý khách đã ủng hộ Dofuu trong suốt thời gian qua. 
						</div>
						<div class="body-2">
							- Trong năm qua công ty phục vụ còn có nhiều thiếu sót, mong quý khách thông cảm. Dofuu sẽ cải thiện và nâng cao chất lượng phục vụ nhằm mang đến sự hài lòng cho quý khách.
						</div>
						<div class="body-2 red--text text--accent-3">	
							Chúc quý khách "Một Năm Mới Vui Vẻ, Hạnh Phúc, An Khang Thịnh Vượng, Vạn Sự Như Ý".
						</div>
					</v-card-text>
					<v-card-actions>
						<v-btn block color="grey lighten-4" round small @click.native="dialog = false">Đóng</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>
		</v-content>
		<vue-footer />
	</v-app>
</template>

<script>
	import Toolbar from '@/components/Toolbar'
	import LeftNavigation from '@/components/LeftNavigation'
	import AlertDialog from '@/components/commons/alertDialog'
	import Footer from '@/components/Footer'
	import { mapState } from "vuex";
	import Cookies from 'js-cookie'
	export default {
		components: {
			'vue-left-navigation': LeftNavigation,
			'vue-toolbar' : Toolbar,
			'vue-footer': Footer,
			'vue-alert': AlertDialog
		},
		data() {
			return {
				dialog: true
			}
		},
		computed: {
			...mapState({
				tracking: state => state.locationStore.tracking
			})
		},
		methods: {
			open: function() {
				this.$refs.alert.open()
			}
		},
		created: function() {			

			this.$store.dispatch("fetchCity").then(response => {
				if(response.status == 200) {
					if(typeof this.$route.params.city != 'undefined') {
						const city = this.$store.getters.getCityBySlug(this.$route.params.city)
						this.$store.dispatch('getCityCurrent', city.id)
					} else {
						if(typeof Cookies.get('flag_c') != 'undefined') {

							setTimeout(() => {
								this.$store.dispatch('getCityCurrent', Cookies.get('flag_c'))
							}, 100)
							
						} else {

							setTimeout(() => {
								this.$store.dispatch('getCityCurrent', 10001)
							}, 100)
							
						}
					}
				}
			});
			this.$store.dispatch("fetchType");
			this.$store.dispatch('trackingLocation')
			this.$store.dispatch('currentLocation')
		}
	};
</script>

<style>
.card-radius {
	border-radius: 8px;
}
.btn-custom.v-text-field.v-text-field--solo .v-input__control {
	min-height: 30px !important;
	top: 4px
}
.btn-custom.theme--light.v-text-field--solo .v-input__slot {
	border-radius: 18px;
}
.v-card--reveal {
	align-items: center;
	bottom: 0;
	justify-content: center;
	opacity: .5;
	position: absolute;
	width: 100%;
}
</style>
