<template>
	<div style="width:250px;display:inline;" v-if="email_valid == true">
		<div style="width:250px;display:inline;" v-if="attempts_used < attempts_max">
			<div style="width:250px;display:inline;" v-if="(minutes > 0 || seconds > 0) && attempts_used > 0 && attemptsUsed > 0">
				{{ attempts_used }}/{{ attempts_max }} attempts used, next in {{ minutes }} min {{ seconds | two_digits }} sec
			</div>
			<div style="width:250px;display:inline;" v-else>
				<button class="btn btn-primary" v-on:click="sendEmail" v-if="sending == 0 && isSending == 0">{{ componentTxt }}</button>
				<button class="btn btn-primary" v-else disabled><img src="/storage/ajax-loader.gif"></button>
			</div>
		</div>
		<div style="width:250px;display:inline;" v-else>
			0 attempts left. Please, contact our support if you still didn`t receive the email.
		</div>
	</div>
	<div style="width:250px;display:inline;font-size:12px;" v-else>
		<br>Please enter valid email address.
	</div>
</template>

<script>
	import axios from 'axios'
	
	export default {
		props: {
			isSending: Number,
			activated: Number,
			verifyStr: String,
			idEmail: Number,
			timeUpdated: Number,
			verifyEmail: String,
			attemptsUsed: Number,
			componentTxt: String,
			childComponent: Number
		},
		data() {
			return {
				sending: this.isSending,
				attempts_used: this.attemptsUsed,
				time_updated: this.timeUpdated,
				attempts_max: 3,
				time_sec_max: 180,
				minutes: 0,
				seconds: 1
			}
		},
		computed: {
			email_valid() {
				if(this.validEmail(this.verifyEmail)) {
					return true;
				}
				else {
					return false;
				}
			}
		},
		methods: {
			timePassed: function () {
				var date = new Date();
				var time = Math.floor(date.getTime()/1000);
				
				// Если компонент вызван в виде дочерного, то 
				// он постоянно обновляет текущее значение внутренных переменных до значения родительских
				
				if(this.childComponent)
				{
					if(this.timeUpdated != 0 && this.attemptsUsed != 0)
					{
						this.attempts_used = this.attemptsUsed;
						this.time_updated = this.timeUpdated;
					}
				}
				
				var time_sec = (Math.abs(time - this.time_updated) > this.time_sec_max) ? 0 : (this.time_sec_max - Math.abs(time - this.time_updated));
					
				if(time_sec == 0){
					if(this.attempts_used < this.attempts_max)
					{
						this.seconds = 0;
					}
					else
					{
						this.seconds = 1;
					}
					
					this.minutes = 0;
				}
				else
				{
					this.minutes = Math.floor(time_sec / 60);
					this.seconds = time_sec % 60;
				}
			},
			sendEmail: function() {
			
				this.attempts_used += 1;
				
				if(this.idEmail == 0) {
					var date = new Date();
					var time = Math.floor(date.getTime()/1000);
				
					this.time_updated = time;
					this.timePassed();
					this.$emit('verify');
				}
				else
				{
					this.sending = 1;
				
					const vm2 = this;
					axios.put('/api/related-emails/' + this.idEmail, {
						email: btoa(this.verifyEmail),
						verify: this.verifyStr
					})
					.then(function (response) {
						vm2.time_updated = response.data;
						
						// для поддержания обновленности (пока родитель среагирует на посланное событие) 
						// временно присваиваем входящим параметрам значения которые вернул сервер
				
						if(vm2.childComponent)
						{
							vm2.attemptsUsed = vm2.attempts_used;
							vm2.timeUpdated = vm2.time_updated;
						}
						
						vm2.timePassed();
						vm2.$emit('verify');
						vm2.sending = 0;
					})
					.catch(function (error) {
						vm2.sending = 1;
					})
				}
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