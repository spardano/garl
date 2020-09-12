<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Edit Help</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="config/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Help</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='data.value' rows="20"></textarea>
							<small class="form-text text-danger" v-if="errors.value">{{ errors.value[0] }}</small>
						</div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
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
				message: null,
				messageType: 'error',
				errors: [],
				data: {
					key: "help",
					value: ""
				},
				dirty: false
			}
		},
		mounted() {
            this.getHelp();
		},
		watch: {
			data:{
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
			getHelp() {
				axios.post(window.baseUrl+"/"+"config/detail/help", {}).then(response => {
					this.data = response.data;
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
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value , this.data).then(response => {
					this.errors = [];

					this.user = response.data;

					this.message = 'Helper telah diperbaharui.';
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