<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Klasifikasi</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/klasifikasi/proses" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kabupaten</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='data.kabupaten_id' @change="getKecamatan($event)">
								<option value="">Pilih Kabupaten</option>
								<option v-for="(s, index) in allKabupaten" v-bind:value="s.id" v-bind:key="index">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kabupaten_id">{{ errors.kabupaten_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kecamatan</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='data.kecamatan_id' @change="getKelurahan($event)">
								<option value="">Pilih Kecamatan</option>
								<option v-for="(s, index) in allKecamatan" v-bind:value="s.id" v-bind:key="index">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kecamatan_id">{{ errors.kecamatan_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kelurahan</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='data.kelurahan_id'>
								<option value="">Pilih Kelurahan</option>
								<option v-for="(s, index) in allKelurahan" v-bind:value="s.id" v-bind:key="index">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kelurahan_id">{{ errors.kelurahan_id[0] }}</small>
						</div>
					</div>
					<br>
					<div class="" v-for="(f, i) in form" v-bind:key="i">
						<h3>{{f.label}}</h3>
						<div class="form-group row" v-for="(ff, ii) in f.kriteria" v-bind:key="ii">
							<label for="exampleInputEmail1" class="col-form-label col-sm-4">{{ff.label}}</label>
							<div class="col-sm-8" v-if="ff.type == 'label'">
								<select class="form-control" v-model='data.details[ff.id]'>
									<option value="">Pilih Nilai</option>
									<option v-for="(s) in ff.data" v-bind:value="s" v-bind:key="s">{{s}}</option>
								</select>
							</div>
							<div class="col-sm-8" v-if="ff.type == 'numeric'" >
								<input type="number" step="0.1" v-bind:min="ff.min" v-bind:max="ff.max" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='data.details[ff.id]'>
								<small class="form-text text-muted">Nilai min {{ff.min}}, dan nilai max {{ff.max}}</small>
							</div>
						</div>
					</div>
					<small class="form-text text-danger" v-if="errors.details">{{ errors.details[0] }}</small>
					<button class="btn btn-primary float-right" >Proses</button>
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
				allKecamatan: [],
				allKelurahan: [],
				form: [],
				result: null,
				data: {
					kabupaten_id: '',
					kecamatan_id: '',
					kelurahan_id: '',
					details: []
				},
				dirty: true
			}
		},
		mounted() {
			this.getKabupaten()
			this.getForm()
		},
		watch: {
			data:{
				handler(newVal, oldVal){
					this.dirty = false;
					var self = this;
						console.log(newVal)
					Object.keys(newVal).forEach(function(v,i){
						if(newVal[v] == "") self.dirty = true;
					});
				},
				deep: false
			}
		},

		methods: {
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
				});
			},
			getKecamatan() {
				axios.post(window.baseUrl+"/"+"kecamatan/all", {kabupaten_id: this.data.kabupaten_id}).then(response => {
					this.allKecamatan = response.data;
				});
			},
			getKelurahan() {
				axios.post(window.baseUrl+"/"+"kelurahan/all", {kecamatan_id: this.data.kecamatan_id}).then(response => {
					this.allKelurahan = response.data;
				});
			},
			getForm() {
				axios.post(window.baseUrl+"/"+"kategori/form", {}).then(response => {
					this.form = response.data;
				});
			},
			store(e) {
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value, this.data).then(response => {
					this.errors = [];

					this.goTo("klasifikasi-view", {id: this.data.kelurahan_id})

					this.message = null;
					this.messageType = 'success';
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status == 422) {
							this.errors = error.response.data.errors;
							window.scrollTo(0, 0);
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