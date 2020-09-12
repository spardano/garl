<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Edit Kategori</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/kategori/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Nama</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='kategori.name'>
							<small class="form-text text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
						</div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
					<a href="javascript:void(0)" @click="goTo('kategori', null)" class='btn btn-link text-danger float-right'><i class="material-icons left">&laquo;</i> Back </a>
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
				kategori: {
					name: '',
				},
				dirty: false
			}
		},
		mounted() {
			this.getKategori()
		},
		watch: {
			kategori:{
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
			getKategori() {
				axios.post(window.baseUrl+"/"+"kategori/detail/"+this.$route.params.id, {}).then(response => {
					this.kategori = response.data;
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
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value +"/"+this.$route.params.id, this.kategori).then(response => {
					this.errors = [];

					this.kategori = response.data;

					this.message = 'Data kategori telah diperbaharui.';
					this.messageType = 'success';
					window.scrollTo(0, 0);
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status = 422) {
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