<template>
  <v-dialog :value="show" fullscreen hide-overlay transition="dialog-bottom-transition">
    <v-btn slot="activator" color="primary" dark>Open Dialog</v-btn>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click.native="dialog = false">
          <v-icon>close</v-icon>
      </v-btn>
      <v-toolbar-title>Settings</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
          <v-btn dark flat @click.native="dialog = false">Save</v-btn>
      </v-toolbar-items>
  </v-toolbar>
  <v-list three-line subheader>
    <v-subheader>User Controls</v-subheader>
    <v-list-tile avatar>
      <v-list-tile-content>
        <v-list-tile-title>Content filtering</v-list-tile-title>
        <v-list-tile-sub-title>Set the content filtering level to restrict apps that can be downloaded</v-list-tile-sub-title>
    </v-list-tile-content>
</v-list-tile>
<v-list-tile avatar>
  <v-list-tile-content>
    <v-list-tile-title>Password</v-list-tile-title>
    <v-list-tile-sub-title>Require password for purchase or use password to restrict purchase</v-list-tile-sub-title>
</v-list-tile-content>
</v-list-tile>
</v-list>
<v-divider></v-divider>
<v-list three-line subheader>
    <v-subheader>General</v-subheader>
    <v-list-tile avatar>
      <v-list-tile-action>
        <v-checkbox v-model="notifications"></v-checkbox>
    </v-list-tile-action>
    <v-list-tile-content>
        <v-list-tile-title>Notifications</v-list-tile-title>
        <v-list-tile-sub-title>Notify me about updates to apps or games that I downloaded</v-list-tile-sub-title>
    </v-list-tile-content>
</v-list-tile>
<v-list-tile avatar>
  <v-list-tile-action>
    <v-checkbox v-model="sound"></v-checkbox>
</v-list-tile-action>
<v-list-tile-content>
    <v-list-tile-title>Sound</v-list-tile-title>
    <v-list-tile-sub-title>Auto-update apps at any time. Data charges may apply</v-list-tile-sub-title>
</v-list-tile-content>
</v-list-tile>
<v-list-tile avatar>
  <v-list-tile-action>
    <v-checkbox v-model="widgets"></v-checkbox>
</v-list-tile-action>
<v-list-tile-content>
    <v-list-tile-title>Auto-add widgets</v-list-tile-title>
    <v-list-tile-sub-title>Automatically add home screen widgets</v-list-tile-sub-title>
</v-list-tile-content>
</v-list-tile>
</v-list>
</v-card>
</v-dialog>
</template>

<script>

import axios from 'axios'

export default {
    computed: {
        ...mapState({
            user: state        => state.authStore.currentUser,
            currentCity: state => state.cityStore.currentCity,
            show : state       => state.cartStore.dialog,
            cart: state        => state.cartStore.cart,
            coupon: state      => state.cartStore.coupon
        }),
        counts: function() {
            return this.$store.getters.counts
        },
        subTotal: function() {
            return this.$store.getters.subTotal
        },
        total: function() {
            return Math.floor(numeral(this.subTotal).value() - numeral(this.subTotal).value()*this.discount/100 + numeral(this.deliveryPrice).value())
        },
        dealPrice: function() {
            if(this.coupon != null) {
                return this.subTotal - Math.floor(numeral(this.subTotal).value()*numeral(this.coupon.discountPercent).value()/100)
            }
            return 0
        },
        minTime: function() {
            var activities = this.store.activities // Active time of store
            var day        = null // Day current of store
            var now        = moment().locale('vi') // Date time now 
            var n          = now.day()      // Day of week 
            var hs         = null // Hour start of store 
            var regex      = new RegExp(':', 'g') // Regular 
            // Find activity of store in current day
            day            = activities.find(item => {
                return item.number == n
            })
            // Find start time of store in current day
            day.times.filter(item => {
                if(parseInt(item.from.replace(regex, ''),10) > parseInt(now.format('HH:mm').replace(regex, ''),10)) {
                    hs = item.from
                } else {
                    hs = now.format('HH:mm')
                }
            })
            return hs
        },
        maxTime: function() {
            var activities = this.store.activities // Active time of store
            var day        = null // Day current of store
            var now        = moment().locale('vi') // Date time now 
            var n          = now.day()      // Day of week 
            var he         = null // End time of store 
            var regex      = new RegExp(':', 'g') // Regular 
            // Find activity of store in current day
            day            = activities.find(item => {
                return item.number == n
            })
            // Find end time of store in current day
            day.times.filter(item => {
                if(item.to) {
                    he = item.to
                }
            })
            return he
        },
        intendTime: function() {
            const totalTime = numeral(this.matrix.duration.slice(0,-5)).value() + numeral(this.store.prepareTime).value() // Total time of prepare time and duration time
            var now         = moment().locale('vi') // Date time current
            var intendTime  = now.add(totalTime, 'minutes') //Intent time when after direction
            var mm          = Math.floor(parseInt(intendTime.format('mm'))/5) // Calculate minutes of intentime divide 5
            // Less than 15 minutes
            var time        = null
            
            if(mm >= 0 && mm < 3) {
                var minTemp = Math.floor(15 - parseInt(intendTime.format('mm')))    
                time = intendTime.add(minTemp, 'minutes')
            } else if(mm >= 3 && mm < 6) {
                var minTemp = Math.floor(30 - parseInt(intendTime.format('mm')))    
                time = intendTime.add(minTemp, 'minutes')
            } else if(mm >= 6 && mm < 9) {
                var minTemp = Math.floor(45 - parseInt(intendTime.format('mm')))    
                time = intendTime.add(minTemp, 'minutes')
            } else if (mm >= 9 && mm < 12) {
                var minTemp = Math.floor(60 - parseInt(intendTime.format('mm')))    
                time = intendTime.add(minTemp, 'minutes')
            }
            this.editedItem.time = time.format('HH:mm')

            return totalTime
        },
        discount: function() {
            return this.$store.getters.discount
        }
    }, 
}
</script>