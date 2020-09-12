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

					<form @submit.prevent="store" action="/klasifikasi/proses-new" method="post">
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
						<div class="form-group row" >
							<label for="exampleInputEmail1" class="col-form-label col-sm-4">{{f.label}}</label>
							<div class="col-sm-8" v-if="f.type == 'label'">
								<select class="form-control" v-model='data.details[f.id]'>
									<option value="">Pilih Nilai</option>
									<option v-for="(s) in f.data" v-bind:value="s" v-bind:key="s">{{s}}</option>
								</select>
							</div>
							<div class="col-sm-8" v-if="f.type == 'numeric'" >
								<input type="number" step="0.1" v-bind:min="f.min" v-bind:max="f.max" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='data.details[f.id]'>
								<small class="form-text text-muted">Nilai min {{f.min}}, dan nilai max {{f.max}}</small>
							</div>
						</div>
					</div>
					<small class="form-text text-danger" v-if="errors.details">{{ errors.details[0] }}</small>
					<button class="btn btn-primary float-right" :disable="!hasNext" v-bind:class="{disabled: !hasNext}">{{hasNext ? 'Selanjutnya' : "SELESAI"}}</button>
					<button class="btn btn-danger float-right mr-2" @click="reset()" type="button">RESET</button>
					<div class="clearfix"/>
					</form>
				</div>
			</div>

		</div>

		<div class="container-fluid row" v-if="!hasNext">
			<div class="col-sm-12">
				<br><hr><br>
			</div>
			<div class="col-sm-6">
				<div class="card">
					<div class="card-header">
						<h5 class="text-center"> Klasifikasi </h5>
					</div>
					<div class="card-body">
						<h4>Hasil kesesuaian lahan bawang putih berdasarkan inputan user adalah </h4>
						<br>
						
						<h4>Inputan:</h4>
						<ul>
							<li v-for="(rekom, index) in kelurahan.data.rekom" v-bind:key="index">
								<span>{{rekom.kriteria.name}} = {{kelurahan.dataraw[index]}}, keterangan: {{rekom.keterangan}}</span>
							</li>
						</ul>
						<br><br>

						<h2 v-if="kelurahan.kelas">{{kelurahan.kelas.class}}</h2>
						<h2 v-if="!kelurahan.kelas">Unclassified</h2>
						<small class="text-muted" v-if="!kelurahan.data.rules && kelurahan.kelas && kelurahan.kelas.class != 'Unclassified'">Rule ID: tidak terdeteksi, silahkan lakukan klasifikasi ulang</small>
						<small class="text-muted" v-if="kelurahan.data.rules">Rule ID: {{kelurahan.data.rules.id}}
							<span v-if="aturan">( JIKA 
								<span v-for="(d, i) in aturan.details" v-bind:key="d.id">
									<span v-if="i > 0"> DAN </span> {{d.kriteria.name}} = {{d.value}}
								</span>)
							</span>
						</small>
						<br><br><br>
						<h4>Hasil Rekomendasi</h4>
						<ul>
							<li v-for="(rekom, index) in kelurahan.data.rekom" v-bind:key="index">
								<span v-if="!rekom.rekomendasi">Tidak ditemukan rekomendasi untuk kategori {{rekom.kriteria.name}}</span>
								<span v-if="rekom.rekomendasi">{{rekom.rekomendasi}}, karena pada {{rekom.kriteria.name}} masuk kedalam kelas {{rekom.kesesuaian}}</span>
							</li>
						</ul>
					</div>
				</div>

			</div>
			<div class="col-sm-6">
				<div class="card">
					<div class="card-header">
						<h5 class="text-center">Maps</h5>
					</div>
					<div class="card-body">
						<div id="map" class="map">
						<div id="popup" class="ol-popup">
							<a href="#" id="popup-closer" class="ol-popup-closer"></a>
							<div id="popup-content"></div>
						</div>
						</div>
					</div>
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
				kelurahan: null,
				aturan: null,
				kecamatan: {
					kabupaten_id: 0
				},
				form: [],
				result: null,
				hasNext: true,
				data: {
					kabupaten_id: '',
					kecamatan_id: '',
					kelurahan_id: '',
					details: []
				},
				dirty: true,
				map: null,
				info: null,
				geojson: null,
				legend: null
			}
		},
		mounted() {
			this.getKabupaten()
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
			reset(){
				this.message = '';
				this.messageType = 'error';
				this.errors = [];
				this.allKecamatan = [];
				this.allKelurahan = [];
				this.kelurahan = null;
				this.aturan = null;
				this.kecamatan = {
					kabupaten_id: 0
				};
				this.form = [];
				this.result = null;
				this.hasNext = true;
				this.data = {
					kabupaten_id: '',
					kecamatan_id: '',
					kelurahan_id: '',
					details: []
				};
				this.dirty = true;
				this.map = null;
				this.info = null;
				this.geojson = null;
				this.legend = null;
			},
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
			getAturan(id) {
				axios.post(window.baseUrl+"/"+"aturan/detail/"+id, {}).then(response => {
					this.aturan = response.data;
				});
			},
			getForm(id) {
				axios.post(window.baseUrl+"/"+"kategori/form/"+id, {}).then(response => {
					if(typeof this.data.details[response.data.id] == 'undefined'){
						this.form.push(response.data)
						this.data.details[response.data.id] = ""
						window.scrollTo(0,document.body.scrollHeight);
					}
				});
			},
			store(e) {
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value, this.data).then(response => {
					this.errors = [];

					// this.goTo("klasifikasi-view", {id: this.data.kelurahan_id})
					if(response.data.finish == false){ 
						this.getForm(response.data.next);
					}else{
						this.hasNext = false;
						this.kelurahan = response.data.kelurahan;
						this.kelurahan.data = JSON.parse(this.kelurahan.data)
						this.kelurahan.dataraw = JSON.parse(this.kelurahan.dataraw)
						this.kelurahan.area += "";
						this.kecamatan = this.kelurahan.kecamatan
						if(this.kelurahan.data.rules) this.getAturan(this.kelurahan.data.rules.id);
						setTimeout(() => { this.initMap() }, 1000)
					}

					this.message = null;
					this.messageType = 'success';
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status == 422) {
							this.errors = error.response.data.errors;
							window.scrollTo(0, 0);
						}
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
					}
				});
			},
			initMap(){
				if(this.map != null){
					this.centerTo()
					return
				}
				try{
					this.map = L.map('map').setView([-2, 118], 4)
				}catch(e){
					console.log(e)
				}
				console.log("sampe")
				this.info = L.control()
				L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
					maxZoom: 18,
					attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
					'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
					'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
					id: 'mapbox.light'
				}).addTo(this.map);

				this.info.onAdd = function (map) {
					this._div = L.DomUtil.create('div', 'info');
					this.update();
					return this._div;
				};
				this.info.update = function (props) {
					this._div.innerHTML = '<h4>Land Suitability Class</h4>' +  (props ?
					'<b>' + props.attr + '</b><br />' + props.area + ' m<sup>2</sup>'
					: 'Hover over a class');
				};

				this.info.addTo(this.map);
				var land = {
					type: "Feature",
					geometry: JSON.parse(this.kelurahan.polygon),
					properties: {
						attr: this.kelurahan.name,
						area: this.kelurahan.area,
						code: ''
					}
				}
				this.geojson = L.geoJson(land, {
					style: this.style,
					onEachFeature: this.onEachFeature
				}).addTo(this.map);

				this.legend = L.control({position: 'bottomright'});

				this.legend.onAdd = function (map) {

				var div = L.DomUtil.create('div', 'info legend'),
				grades = [0,1,2,3,4,5],
				labels = [],
				from, to;

				for (var i = 0; i < grades.length; i++) {
					from = grades[i];
					to = grades[i + 1];

					labels.push(
						'<i style="background:' + this.getColor(from + 0) + '"></i> ' +
						from + (to ? '&ndash;' + to : ''));
					}

					div.innerHTML = labels.join('<br>');
					return div;
				};

				var that = this
				navigator.geolocation.getCurrentPosition(function(location) {
					var latlng = new L.LatLng(location.coords.latitude, location.coords.longitude);
					var marker = L.marker(latlng).addTo(that.map);
				});
				this.centerTo()
			},
			getColor(d) {
				return d > 4 ? '#009999' :
					d > 3 ? '#808080' :
					d > 2 ? '#e60000' :
					d > 1 ? '#ffa31a' :
					d > 0 ? '#248f24' :
						'#005ce6';
			},
			style(feature) {
				var color = '#005ce6'
				if(this.kelurahan.kelas != null) color = this.kelurahan.kelas.color
				return {
					weight: 2,
					opacity: 1,
					color: 'white',
					dashArray: '3',
					fillOpacity: 0.7,
					fillColor: color
				};
			},
			highlightFeature(e) {
				var layer = e.target;

				layer.setStyle({
				weight: 5,
				color: 'white',
				dashArray: '',
				fillOpacity: 0.7
				});

				if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
				layer.bringToFront();
				}

				this.info.update(layer.feature.properties);
			},
			resetHighlight(e) {
				this.geojson.resetStyle(e.target);
				this.info.update();
			},
			zoomToFeature(e) {
				console.log(e.target.feature.geometry);
				this.map.fitBounds(e.target.getBounds());
			},
			onEachFeature(feature, layer) {
				layer.on({
				mouseover: this.highlightFeature,
				mouseout: this.resetHighlight,
				click: this.zoomToFeature
				});
			},
			centerTo(){
				var center = this.allKabupaten.find(x => x.id === this.data.kabupaten_id).center_point
				this.map.setView(L.latLng(JSON.parse(center)), 9); 
			},
			onChangePolygon(){
				if(this.kelurahan.polygon == null) return
				this.dirty = false;
				try{
					this.map.removeLayer(this.geojson)
					var land = {
						type: "Feature",
						geometry: JSON.parse(this.kelurahan.polygon),
						properties: {
							attr: this.kelurahan.name,
							area: this.kelurahan.area,
							code: ''
						}
					}
					this.geojson = L.geoJson(land, {
						style: this.style,
						onEachFeature: this.onEachFeature
					}).addTo(this.map);
				}catch(e){
					this.dirty = true
					var message = e.message;
					if(message == 'JSON.parse: unexpected character at line 1 column 1 of the JSON data') message = "Invalid geoJson format"
					this.errors.polygon = ([message])
					console.log(e.code)
				}
			}
		}
	}
</script>