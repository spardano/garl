<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Edit User</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/user/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Nama</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='user.name'>
							<small class="form-text text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="Email" class="col-form-label col-sm-2">Email</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="Email" aria-describedby="emailHelp" v-model='user.email'>
							<small class="form-text text-danger" v-if="errors.email">{{ errors.email[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Role</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='user.role'>
								<option value="">Pilih Role</option>
								<option value="ADMIN">ADMIN</option>
								<option value="PENELITI">PENELITI</option>
							</select>
							<small class="form-text text-danger" v-if="errors.role">{{ errors.role[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="Password" class="col-form-label col-sm-2">Password</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" id="Password" aria-describedby="emailHelp" v-model='user.password' placeholder="Optional">
							<small class="form-text text-danger" v-if="errors.password">{{ errors.password[0] }}</small>
						</div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
					<a href="javascript:void(0)" @click="goTo('user', null)" class='btn btn-link text-danger float-right'><i class="material-icons left">&laquo;</i> Back </a>
					<div class="clearfix"/>
					</form>
				</div>
			</div>

		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				message: '',
				messageType: 'error',
				errors: [],
				user: {
					name: '',
					email: '',
					password: '',
					role: ''
				},
				dirty: false
			}
		},
		mounted() {
            this.getUser();
		},
		watch: {
			user:{
				handler(newVal, oldVal){
					this.dirty = false;
					var self = this;
					Object.keys(newVal).forEach(function(v,i){
						if(newVal[v] == "") self.dirty = true;
					});
				},
				deep: true
			}
		},

		methods: {
			getUser() {
				axios.post(window.baseUrl+"/"+"user/detail/"+this.$route.params.id, {}).then(response => {
					this.user = response.data;
					setTimeout(function(){
						M.updateTextFields();
					}, 100);
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						this.errors = error.response.data;
					}
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
						if(error.response.status == 404) {
							this.dirty = true;
							this.message = "Data tidak ditemukan.";
							setTimeout(function(){window.history.back()}, 1000);
						}
				});
			},
			store(e) {
				this.loading = true;
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value +"/"+this.$route.params.id, this.user).then(response => {
					this.errors = [];

					this.user = response.data;

					this.message = 'Data user telah diperbaharui.';
					this.messageType = 'success';
					this.loading = false;
					window.scrollTo(0, 0);
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status == 422) {
							this.errors = error.response.data;
						}
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
						this.loading = false;
					}
				});
			}
		}
	}
</script>