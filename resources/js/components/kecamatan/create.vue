<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Kecamatan Baru</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/kecamatan/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kabupaten</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='kecamatan.kabupaten_id'>
								<option value="">Pilih Kabupaten</option>
								<option v-for="(s, index) in allKabupaten" v-bind:value="s.id">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kabupaten_id">{{ errors.kabupaten_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Nama</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='kecamatan.name'>
							<small class="form-text text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
						</div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
					<a href="javascript:void(0)" @click="goTo('kecamatan', null)" class='btn btn-link text-danger float-right'><i class="material-icons left">&laquo;</i> Back </a>
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
				allKabupaten: [],
				kecamatan: {
					name: '',
					kabupaten_id: ''
				},
				dirty: true
			}
		},
		mounted() {
			this.getKabupaten()
		},
		watch: {
			kecamatan:{
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
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
				});
			},
			store(e) {
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value, this.kecamatan).then(response => {
					this.errors = [];

					this.kecamatan = {
						name: '',
						kabupaten_id: ''
					}

					this.message = 'Kecamatan telah ditambahkan.';
					this.messageType = 'success';
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status == 422) {
							this.errors = error.response.data.errors;
						}
							console.log(error.response.data.errors)
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
					}
				});
			}
		}
	}
</script>