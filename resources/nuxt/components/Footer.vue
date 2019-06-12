<template>
	<v-card class="flex" flat tile color="grey lighten-5" >
		<v-layout :row="$vuetify.breakpoint.smAndUp" :column="$vuetify.breakpoint.xsOnly" >
	      <v-flex xs12 md4 order-md1 order-xs3>
			<v-card tile flat color="transparent">
	          	<v-card-text>
	          		<h3>Công ty</h3>
	          		<nuxt-link to="/privacy">Chính sách</nuxt-link>
				</v-card-text>
	        </v-card>
	      </v-flex>
	      <v-flex xs12 md4 order-md2 order-xs2>
	        <v-card tile flat color="transparent">
	          	<v-card-text class="text-xs-center">
					<div> &copy;2018 — <strong>Công ty TNHH Dofuu.</strong> </div>
					<div><strong>GPĐKKD:</strong> 1801598954 do sở Kế hoạch và Đầu tư TP. Cần Thơ cấp ngày 26/04/2018. </div>
					<div><strong>Địa chỉ:</strong> 160/18, đường 30/4, Phường An Phú, Quận Ninh Kiều, Cần Thơ.</div>
					<div><strong>Hotline:</strong> 0888.735.739</div>
					<div><strong>Email:</strong> dofuu.company@gmail.com</div>					
				</v-card-text>
	        </v-card>
	      </v-flex>
	      <v-flex xs12 md4 order-md3 order-xs1>
	      	<v-card tile flat color="transparent">
	          	<v-card-text class="text-xs-center">
	          		<h3>Kết nối</h3>
					<v-btn icon flat color="indigo lighten-1" target="_blank" href="https://www.facebook.com/dofuuct/">
						<v-icon>fab fa-facebook-square</v-icon>
					</v-btn>
					<v-btn icon flat color="purple accent-2" target="_blank" href="https://www.instagram.com/dofuu.company/">
						<v-icon>fab fa-instagram</v-icon>
					</v-btn>
					<h3>Tải ứng dụng trên điện thoại</h3>
					<v-card :ripple="true" flat href="https://play.google.com/store/apps/details?id=com.dofuu&hl=vi"  max-height="48" target="_blank" color="transparent" >
						<v-avatar size="36" color="white" tile>
							<v-img src="/google-play-badge.png" alt="dofuu-logo" width="144" height="40"  />
						</v-avatar>
					</v-card>
				</v-card-text>
	        </v-card>
	      </v-flex>
	    </v-layout>		
	</v-card>
</template>

<script>
	
</script>