<template>
	<div>
		<div v-for="(value, index) in email_list">
			<input type="email" name="email" :value="value.email" class="form-control" v-if="value.attempts > 0" style="width:250px;display:inline;" disabled>
			<input type="email" name="email" v-model="value.email" class="form-control" v-else style="width:250px;display:inline;">

			<div style="display:inline;" v-if="value.as_login == 0">
				<button type="button" class="btn btn-danger" v-on:click="deleteEmail(index)">Delete</button>
				<counter v-if="value.activated == 0" :id-email="value.id" component-txt="Verify" verify-str="email" :verify-email="value.email" :activated="value.activated" :attempts-used="value.attempts" :time-updated="value.updated_utc" v-on:verify="incrementAttempts(index)"></counter>
			</div>
			<br><br>
		</div>
	
		<button type="button" class="btn btn-primary" v-on:click="addEmail" style="width:250px;">Add related email</button>
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
				axios('/related-emails')
				.then(function (response) {
					vm.email_list = response.data;
				})
            },
			addEmail() {
				const vm = this;
				axios('/related-emails/create')
				.then(function (response) {
					var date = new Date();
					var time = Math.floor(date.getTime()/1000);
					vm.email_list.push({ "id":response.data ,"email":"","as_login":0,"activated":0,"attempts":0,"updated_utc":time});
				})
			},
			deleteEmail(index) {
				const vm = this;
				axios.delete('/related-emails/' + this.email_list[index].id)
				.then(function (response) {
					vm.email_list.splice(index, 1);
				})
			},
			incrementAttempts(index) {
				this.email_list[index].attempts = 1;
			}
        }
    }
</script>