<template>
	<v-app standalone class="grey lighten-2" >
		<vue-toolbar/>
		<v-content>
			<nuxt/>
		</v-content>
		<v-footer height="auto" absolute fixed color="white" app>
			<span>&copy; 2018 Dofuu Company</span>
		</v-footer>
	</v-app>
</template>

<script>
import Toolbar from '~/components/Toolbar.vue'
import { mapState } from "vuex";
export default {
	components: {
		'vue-toolbar' : Toolbar
	},
	asyncData({store}) {
		if(process.server) {
			store.dispatch("fetchCity");
			store.dispatch("fetchType");
		}
		return {

		}
	},
	//SPA
	async created() {
		if(process.client) {
			this.$store.dispatch("fetchCity");
			this.$store.dispatch("fetchType");
		}
	},
	computed: {
		isAuth: function() {
			return this.$store.getters.isAuth
		},
		cities: function() {
			return Array.from(this.$store.getters.getAllCity);
		},
		types: function() {
			return Array.from(this.$store.getters.getAllTypes)
		},
		cityCurrent: function() {
			return this.$store.getters.getCurrentID
		},
		user: function() {
			return this.$store.getters.getUser
		}
	}
};
</script>
