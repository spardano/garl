<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Tambah Kelurahan Baru</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>

					<form @submit.prevent="store" action="/kelurahan/save" method="post">
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kabupaten</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='kecamatan.kabupaten_id'  @change="getKecamatan($event)" >
								<option value="">Pilih Kabupaten</option>
								<option v-for="(s, index) in allKabupaten" v-bind:value="s.id" v-bind:key="index">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kabupaten_id">{{ errors.kabupaten_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Kecamatan</label>
						<div class="col-sm-10">
							<select class="form-control" v-model='kelurahan.kecamatan_id'>
								<option value="">Pilih Kecamatan</option>
								<option v-for="(s, index) in allKecamatan" v-bind:value="s.id" v-bind:key="index">{{s.name}}</option>
							</select>
							<small class="form-text text-danger" v-if="errors.kecamatan_id">{{ errors.kecamatan_id[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Nama</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='kelurahan.name'>
							<small class="form-text text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Area</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='kelurahan.area'>
							<small class="form-text text-danger" v-if="errors.area">{{ errors.area[0] }}</small>
						</div>
					</div>
					<div class="form-group row">
						<label for="exampleInputEmail1" class="col-form-label col-sm-2">Polygon</label>
						<div class="col-sm-10">
							<textarea type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model='kelurahan.polygon' @change="onChangePolygon()"></textarea>
							<small class="form-text text-danger" v-if="errors.polygon">{{ errors.polygon[0] }}</small>
						</div>
					</div>
					<button class="btn btn-primary float-right" :disabled="dirty">Simpan</button>
					<a href="javascript:void(0)" @click="goTo('kelurahan', null)" class='btn btn-link text-danger float-right'><i class="material-icons left">&laquo;</i> Back </a>
					<div class="clearfix"/>
					</form>
				</div>
			</div>

		</div>
		<div class="col-sm-6 offset-sm-3">
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
				kecamatan: {
					kabupaten_id: ""
				},
				kelurahan: {
					name: '',
					kabupaten_id: '',
					kecamatan_id: '',
					area: 0,
					polygon: null
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
			kelurahan:{
				handler(newVal, oldVal){
					this.dirty = false;
					var self = this;
					Object.keys(newVal).forEach(function(v,i){
						if(newVal[v] == "") self.dirty = true;
					});
					console.log(newVal)
				}
			}
		},

		methods: {
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
					this.initMap();
				});
			},
			getKecamatan(event) {
				axios.post(window.baseUrl+"/"+"kecamatan/all", {kabupaten_id: this.kelurahan.kabupaten_id}).then(response => {
					this.allKecamatan = response.data;
					this.initMap();
				});
			},
			store(e) {
				axios.post(window.baseUrl+"/"+e.target.attributes.action.value, this.kelurahan).then(response => {
					this.errors = [];

					this.kelurahan = {
						name: '',
						kabupaten_id: ''
					}

					this.message = 'Kelurahan telah ditambahkan.';
					this.messageType = 'success';
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
			initMap(){
				if(this.map != null){
					this.centerTo()
					return
				}
				this.map = L.map('map').setView([-2, 118], 4)
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

				// legend.addTo(map);

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
				return {
					weight: 2,
					opacity: 1,
					color: 'white',
					dashArray: '3',
					fillOpacity: 0.7,
					fillColor: this.getColor(feature.properties.code)
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
				if(this.kecamatan == null || this.allKabupaten.length == 0 || this.kecamatan.kabupaten_id == 0) return
				var center = this.allKabupaten.find(x => x.id === this.kecamatan.kabupaten_id).center_point
				this.map.setView(L.latLng(JSON.parse(center)), 9); 
			},
			onChangePolygon(){
				if(this.kelurahan.polygon == null) return
				this.dirty = false;
				try{
					if(this.geojson !== null)
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
					if(message == 'JSON.parse: unexpected character at line 1 column 1 of the JSON data') message = "Invalid polygon format"
					this.errors.polygon = ([message])
					console.log(e)
				}
			}
		}
	}
</script>