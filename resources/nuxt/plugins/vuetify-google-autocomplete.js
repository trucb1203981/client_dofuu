import Vue from 'vue';
import VuetifyGoogleAutocomplete from 'vuetify-google-autocomplete';

Vue.use(VuetifyGoogleAutocomplete, {
  apiKey: 'AIzaSyANKviRikWWh8f9fbGuzh68I2Gl4T8yEEE', // Can also be an object. E.g, for Google Maps Premium API, pass `{ client: <YOUR-CLIENT-ID> }`
  version: '3.31', // Optional
  libraries: 'places', 
  v: '3.31',
  language:'vi',
  region: 'VN'
});