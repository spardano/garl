<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Edit Kriteria</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/kriteria/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kategori</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='kriteria.kategori_id'>
								<option value="">Pilih Kategori</option>
								<option v-for="(s, index) in allKategori" v-bind:value="s.id">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kategori_id">{{ errors.kategori_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Nama</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='kriteria.name'>
							<small class="form-text text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Tipe</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='kriteria.type'>
								<option value="">Pilih Tipe</option>
								<option value="numeric">Numeric</option>
								<option value="label">Label</option>
							</select>
							<small class="form-text text-danger" v-if="errors.type">{{ errors.type[0] }}</small>
						</div>
					</div>
					<br>
					<br>
					<a href="javascript:;" class="btn btn-primary float-right" @click="addDetail()">Tambah Nilai</a>
					<h5>Nilai Kriteria</h5>
					<p class="text-muted" v-if="kriteria.type == 'numeric'">Nb: Masukkan nilai 99999 untuk nilai tak terhingga</p>
					<hr>
					<div class="row" v-for="(d, index) in kriteria.details">
						<div class="col-md-2">
							<div class="form-group">
								<label for="exampleInputEmail1">Min {{index}}</label>
								<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="d.min" :key="index">
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".min"]'>{{ errors["details."+index+".min"][0] }}</small>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="exampleInputEmail1">Max</label>
								<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="d.max" :key="index">
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".max"]'>{{ errors["details."+index+".max"][0] }}</small>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Kesesuaian</label>
								<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="d.kesesuaian" :key="index">
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".kesesuaian"]'>{{ errors["details."+index+".kesesuaian"][0] }}</small>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Keterangan</label>
								<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="d.keterangan" :key="index">
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".keterangan"]'>{{ errors["details."+index+".keterangan"][0] }}</small>
							</div>
						</div>
						<div class="col-md-1" v-if="kriteria.details.length > 1" style="padding-top:30px">
							<a href="javascript:;" class="btn btn-danger" @click="deleteDetail(index)">X</a>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputEmail1">Rekomendasi</label>
								<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="d.rekomendasi" :key="index">
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".rekomendasi"]'>{{ errors["details."+index+".rekomendasi"][0] }}</small>
							</div>
						</div>
						<div class="col-sm-12"><hr></div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
					<a href="javascript:void(0)" @click="goTo('kriteria', null)" class='btn btn-link text-danger float-right'><i class="material-icons left">&laquo;</i> Back </a>
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
				allKategori: [],
				kriteria: {
					name: '',
					kategori_id: '',
					type: '',
					details: []
				},
				detail: {
						min: '',
						max: '',
						kesesuaian: '',
						keterangan: '',
						rekomendasi: ''
				},
				dirty: true
			}
		},
		mounted() {
            this.getUser();
			this.getKategori()
		},
		watch: {
			kriteria:{
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
			addDetail(){
				var x = this.clone(this.detail)
				this.kriteria.details.push(x)
			},
			deleteDetail(index){
				this.kriteria.details.splice(index, 1)
			},
			getKategori() {
				axios.post(window.baseUrl+"/"+"kategori/all", {}).then(response => {
					this.allKategori = response.data;
				});
			},
			getUser() {
				axios.post(window.baseUrl+"/"+"kriteria/detail/"+this.$route.params.id, {}).then(response => {
					this.kriteria = response.data;
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
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value+"/"+this.$route.params.id, this.kriteria).then(response => {
					this.errors = [];

					this.message = 'Kriteria telah diperbaharui.';
					this.messageType = 'success';
					window.scrollTo(0, 0);
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status == 422) {
							this.errors = error.response.data.errors;
						}
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
					}
				});
			},
			clone(Obj) {
				let buf; // the cloned object
				if (Obj instanceof Array) {
					buf = []; // create an empty array
					var i = Obj.length;
					while (i --) {
					buf[i] = this.clone(Obj[i]); // recursively clone the elements
					}
					return buf;
				} else if (Obj instanceof Object) {
					buf = {}; // create an empty object
					for (const k in Obj) {
					if (Obj.hasOwnProperty(k)) { // filter out another array's index
						buf[k] = this.clone(Obj[k]); // recursively clone the value
					}     
					}
					return buf;
				} else {
					return Obj;
				}
			}
		}
	}
</script>