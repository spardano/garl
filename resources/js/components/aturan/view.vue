<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Edit Basis Aturan</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/aturan/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kabupaten</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='aturan.kabupaten_id'>
								<option value="">Pilih Kabupaten</option>
								<option v-for="(s, index) in allKabupaten" v-bind:value="s.id">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kabupaten_id">{{ errors.kabupaten_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Class</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='aturan.class'>
								<option value="">Pilih Class</option>
								<option v-for="(s, index) in allClass" v-bind:value="s.id">{{s.class}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.class">{{ errors.class[0] }}</small>
						</div>
					</div>
					<br>
					<br>
					<a href="javascript:;" class="btn btn-primary float-right" @click="addDetail()">Tambah Aturan</a>
					<h5>Nilai Basis Aturan</h5>
					<hr>
					<div class="row" v-for="(d, index) in aturan.details">
						<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Kriteria</label>
								<select class="form-control" v-model='d.param' :key="index" @change="getValue($event.target.value, index)">
									<option value="">Pilih Kriteria</option>
									<option v-for="(s) in allKriteria" v-bind:value="s.id" v-bind:key="s.id">{{s.name}}</option>
								</select>
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".param"]'>{{ errors["details."+index+".param"][0] }}</small>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Nilai</label>
								<select class="form-control" v-model="d.value" :key="index">
									<option value="">Pilih Nilai</option>
									<option v-for="(v) in allValue[index]" v-bind:value="v.keterangan" v-bind:key="v.id">{{v.keterangan}}</option>
								</select>
								<small id="emailHelp" class="form-text text-danger" v-if='errors["details."+index+".value"]'>{{ errors["details."+index+".value"][0] }}</small>
							</div>
						</div>
						<div class="col-md-1 pt-4" v-if="aturan.details.length > 1" style="padding-top:10px">
							<a href="javascript:;" class="btn btn-danger" @click="deleteDetail(index)">X</a>
						</div>
						<div class="col-sm-12"><hr></div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
					<a href="javascript:void(0)" @click="goTo('aturan', null)" class='btn btn-link text-danger float-right'><i class="material-icons left">&laquo;</i> Back </a>
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
				allClass: [],
				allKriteria: [],
				allValue: [],
				allKabupaten: [],
				aturan: {
					kabupaten_id: '',
					class: '',
					details: [{
						param: '',
						value: ''
					}]
				},
				detail: {
					param: '',
					value: ''
				},
				dirty: true
			}
		},
		mounted() {
            this.getUser();
			this.getKabupaten()
			this.getClass()
		},
		watch: {
			aturan:{
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
				this.aturan.details.push(x)
			},
			deleteDetail(index){
				this.aturan.details.splice(index, 1)
			},
			getClass() {
				axios.post(window.baseUrl+"/"+"class/all", {}).then(response => {
					this.allClass = response.data;
				});
			},
			getKriteria() {
				axios.post(window.baseUrl+"/"+"kriteria/all", {}).then(response => {
					this.allKriteria = response.data;
					
					for(var det in this.aturan.details){
						this.getValue(this.aturan.details[det].param, det)
					}
				});
			},
			getValue(e, index) {
				axios.post(window.baseUrl+"/"+"kriteria/details/all", {kriteria_id:  e}).then(response => {
					this.allValue.splice(index, 0, response.data);
				});
			},
			genValue(){
				this.getKriteria()
			},
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
				});
			},
			getUser() {
				axios.post(window.baseUrl+"/"+"aturan/detail/"+this.$route.params.id, {}).then(response => {
					this.aturan = response.data;
					this.genValue()
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						this.errors = error.response.data;

						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
						if(error.response.status == 404) {
							this.dirty = true;
							this.message = "Data tidak ditemukan.";
							setTimeout(function(){window.history.back()}, 1000);
						}
					}
				});
			},
			store(e) {
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value+"/"+this.$route.params.id, this.aturan).then(response => {
					this.errors = [];

					this.aturan = response.data;

					this.message = 'Basis Aturan telah diperbaharui.';
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