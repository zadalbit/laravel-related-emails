<template>
	<div>
		<div v-for="(value, index) in email_list">
			<input type="email" name="email" :value="value.email" class="form-control" v-if="value.attempts > 0" style="width:250px;display:inline;" disabled>
			<input type="email" name="email" v-model="value.email" class="form-control" v-else style="width:250px;display:inline;">
			<div style="display:inline;" v-if="value.as_login == 0">
				<button type="button" class="btn btn-danger" v-on:click="deleteEmail(index)">Delete</button>
				<counter v-if="value.activated == 0" :id-email="value.id" component-txt="Verify" :is-sending="value.is_sending" verify-str="email" :verify-email="value.email" :activated="value.activated" :attempts-used="value.attempts" :child-component="1" :time-updated="value.updated_utc" v-on:verify="incrementAttempts(index)"></counter>
			</div>
			<br><br>
		</div>
	
		<button type="button" class="btn btn-primary" v-on:click="addEmail" style="width:250px;"><i class="glyphicon glyphicon-plus"></i> Add related email</button>
	</div>
</template>

<script>
 
    import axios from 'axios'
	import counter from './Counter.vue';
 
    export default {
        mounted() {
            this.refresh();
        },
		components: {
			'counter': counter
		},
        data() {
            return {
                email_list: []
            }
        },
        methods: {
            refresh() {
				const vm = this;
				axios('/api/related-emails')
				.then(function (response) {
					response.data.forEach(function(item, index) {
						item.is_sending = 0;
						Vue.set(vm.email_list, index, item);
					});
				})
            },
			addEmail() {
				this.email_list.push({ "id":0 ,"email":"","as_login":0,"activated":0,"attempts":0,"updated_utc":0, "is_sending":0});
			},
			verifyEmail(ind) {
				this.email_list[ind].is_sending = 1;
				
				const vm = this;
				axios.post('/api/related-emails', {
					email: btoa(this.email_list[ind].email)
				})
				.then(function (response) {
					vm.email_list[ind].id = response.data.id;
					vm.email_list[ind].email = response.data.email;
					vm.email_list[ind].attempts = response.data.attempts;
					vm.email_list[ind].updated_utc = response.data.updated_utc;
					vm.email_list[ind].is_sending = 0;
				})
			},
			deleteEmail(index) {
				if(this.email_list[index].attempts > 0) { axios.delete('/api/related-emails/' + this.email_list[index].id);}
				this.email_list.splice(index, 1);
				
				this.email_list[index].is_sending = 1;
				window.setTimeout(() => { this.email_list[index].is_sending = 0; }, 1000);
			},
			incrementAttempts(index) {
				var date = new Date();
				var time = Math.floor(date.getTime()/1000);
					
				this.email_list[index].attempts += 1;
				this.email_list[index].updated_utc = time;
					
				if(this.email_list[index].attempts == 1) { 
					this.verifyEmail(index);
				}
			}
        }
    }
</script>