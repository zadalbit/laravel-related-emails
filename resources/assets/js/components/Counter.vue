<template>
	<div style="width:250px;display:inline;" v-if="email_valid == true">
		<div style="width:250px;display:inline;" v-if="att_left > 0">
			<div style="width:250px;display:inline;" v-if="(minutes > 0 || seconds > 0) && (att_left < attempts_max)">
				Next attempt ({{ att_left }} left) in {{ minutes }} min {{ seconds | two_digits }} sec
			</div>
			<div style="width:250px;display:inline;" v-else>
				<button class="btn btn-primary" v-on:click="sendEmail" v-if="sending == 0">{{ componentTxt }}</button>
				<button class="btn btn-primary" v-else disabled><img src="/storage/ajax-loader.gif"></button>
			</div>
		</div>
		<div style="width:250px;display:inline;" v-else>
			0 attempts left. Please, contact our support if you still didn`t receive the email.
		</div>
	</div>
	<div style="width:250px;display:inline;" v-else>
		Please enter valid email address.
	</div>
</template>

<script>
	import axios from 'axios'
	
	export default {
		props: {
			activated: Number,
			verifyStr: String,
			idEmail: Number,
			timeUpdated: Number,
			verifyEmail: String,
			attemptsUsed: Number,
			componentTxt: String
		},
		data() {
			return {
				attempts: this.attemptsUsed,
				updated_at: this.timeUpdated,
				attempts_max: 3,
				time_sec_max: 180,
				sending: 0,
				minutes: 0,
				seconds: 1
			}
		},
		computed: {
			att_left() { return (this.attempts_max - this.attempts)},
			email_valid: {
				get: function () {
					if(this.validEmail(this.verifyEmail)) {
						return true;
					}
					else {
						return false;
					}
				}
			}
		},
		methods: {
			timePassed: function () {
				var date = new Date();
				var time = Math.floor(date.getTime()/1000);
					
				var time_sec = (Math.abs(time - this.updated_at) > this.time_sec_max) ? 0 : (this.time_sec_max - Math.abs(time - this.updated_at));
					
				if(time_sec == 0){
					this.minutes = 0;
					this.seconds = 0;
				}
				else
				{
					this.minutes = Math.floor(time_sec / 60);
					this.seconds = time_sec % 60;
				}
			},
			sendEmail: function() {
				this.sending = 1;
				const vm2 = this;
				axios.put('/related-emails/' + this.idEmail, {
					email: btoa(this.verifyEmail),
					verify: this.verifyStr
				})
				.then(function (response) {
					var date = new Date();
					var time = Math.floor(date.getTime()/1000);
				
					vm2.attempts += 1;
					vm2.sending = 0;
					vm2.updated_at = time;
					vm2.timePassed();
					vm2.$emit('verify');
				})
				.catch(function (error) {
					vm2.sending = 1;
				})
			},
			validEmail: function(email) {
				var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email);
			}
		},
		mounted() {
			this.timePassed();
			window.setInterval(() => {
				this.timePassed();
			},1000);
		}
    }
	
	Vue.filter('two_digits', function (value) {
		if(value.toString().length <= 1)
		{
			return "0"+value.toString();
		}
		return value.toString();
	});
</script>