<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center">Maps</h5>
				</div>
				<div class="card-body">
					<div class="col-sm-12">
						<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
							{{ message }}
						</div>

						<form @submit.prevent="upload" action="/maps/shp" method="post">
							<div class="form-group row">
								<label for="exampleInputEmail1" class="col-form-label col-sm-2">File</label>
								<div class="col-md-4">
									<div class="custom-file">
										<input type="file" class="custom-file-input" accept=".shp,.cpg,.dbf,.prj,.qpj,.shx" id="customFile" @change="changeFile($event)" multiple >
										<label class="custom-file-label" for="customFile">{{filename}}</label>
									</div>
									<small class="form-text text-muted">File Format: .shp,.cpg,.dbf,.prj,.qpj,.shx</small>
									<small class="form-text text-danger" v-if="errors.file">{{ errors.file[0] }}</small>
								</div>
								<div class="col-sm-2">
									<button class="btn btn-primary float-left" :disabled="!uploadFile">Upload</button>
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-12" v-if="results">
							<div class="form-group row">
								<label for="exampleInputEmail1" class="col-form-label col-sm-2">Pilih Legend</label>
								<div class="col-sm-6">
									<select class="form-control" @change="generateClass($event)">
										<option value="">--Legends--</option>
										<option v-for="(s, index) in allLegend" v-bind:value="s" v-bind:key="index">{{s}}</option>
									</select>
								</div>
							</div>
						<h5 class="float-left mr-3 pt-1" v-if="allClass.length > 0">Kelas</h5>
						<span class="badge badge-pill mr-1 badge-primary p-1" v-bind:style="{backgroundColor: c.color}" v-for="(c, i) in allClass" v-bind:key="i">{{c.class}}</span>
						<br>

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
				allClass: [],
				selectedClass: null,
				allLegend: [],
				filename: "Choose file",
				uploadFile: null,
				dirty: false,
				map: null,
				info: null,
				geojson: null,
				legend: null,
				results: null,
				colorList: ["#C91F37", "#DC3023", "#9D2933", "#CF000F", "#E68364", "#F22613", "#CF3A24", "#C3272B", "#8F1D21", "#D24D57", "#F08F907", "#F47983", "#DB5A6B", "#C93756", "#FCC9B9", "#FFB3A7", "#F62459", "#F58F84", "#875F9A", "#5D3F6A", "#89729E", "#763568", "#8D608C", "#A87CA0", "#5B3256", "#BF55EC", "#8E44AD", "#9B59B6", "#BE90D4", "#4D8FAC", "#5D8CAE", "#22A7F0", "#19B5FE", "#59ABE3", "#48929B", "#317589", "#89C4F4", "#4B77BE", "#1F4788", "#003171", "#044F67", "#264348", "#7A942E", "#8DB255", "#5B8930", "#6B9362", "#407A52", "#006442", "#87D37C", "#26A65B", "#26C281", "#049372", "#2ABB9B", "#16A085", "#36D7B7", "#03A678", "#4DAF7C", "#D9B611", "#F3C13A", "#F7CA18", "#E2B13C", "#A17917", "#F5D76E", "#F4D03F", "#FFA400", "#E08A1E", "#FFB61E", "#FAA945", "#FFA631", "#FFB94E", "#E29C45", "#F9690E", "#CA6924", "#F5AB35", "#BFBFBF", "#F2F1EF", "#BDC3C7", "#ECF0F1", "#D2D7D3", "#757D75", "#EEEEEE", "#ABB7B7", "#6C7A89", "#95A5A6"]
			}
		},
		mounted() {
		},
		watch: {
		},

		methods: {
			changeFile(event){
				this.uploadFile = event.target.files
				this.filename = event.target.value
			},
			generateClass(param){
				if(this.map == null) this.initMap()

				this.allClass = [];
				var classes = [];

				var key = param.target.value
				this.selectedClass = key
				for(var i in this.results.data){
					var val = this.results.data[i][key]

					if(classes.indexOf(val) > -1 ) continue;
					classes.push(val)
					var c = {
						color: this.colorList[this.allClass.length],
						class: val
					}
					this.allClass.push(c)
				}
				this.changeGeoJson()
			},
			changeGeoJson(){
				var geoJson = this.results.geoJson
				var result  = {
					type: "FeatureCollection",
					features: []
				};
				for(var i in geoJson){
					var data = this.results.data[i]
					var c = data[this.selectedClass]
					var iii = this.allClass.find(o => o.class == c)
					var f = {
						geometry: JSON.parse(geoJson[i]),
						type: "Feature",
						properties: {
							code: iii.color,
							data: this.toTable(data)
						}
					}

					result.features.push(f)
				}
				this.geojson = L.geoJson(result, {
					style: this.style,
					onEachFeature: this.onEachFeature
				}).addTo(this.map);
			},
			toTable(data){
				var table = "<table>"
				for(var i in data){
					table += "<tr><td>"+i+"</td><td> = </td><td>"+data[i]+"</td></tr>"
				}
				return table + "</table>";
			},
			upload(e){
				this.message 	= null;
				this.errors 	= [];
				
				const formData= new FormData()
				var files = [];
				for(var i in this.uploadFile){
					formData.append('files[' + i + ']', this.uploadFile[i]);
				}

				axios.post(window.baseUrl+"/"+e.target.attributes.action.value, formData).then(response => {
					console.log(response)
					this.results = response.data;
					this.allLegend = response.data.parameters
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
					 props.data
					: 'Hover over a class');
				};

				this.info.addTo(this.map);

				var that = this
				navigator.geolocation.getCurrentPosition(function(location) {
					var latlng = new L.LatLng(location.coords.latitude, location.coords.longitude);
					var marker = L.marker(latlng).addTo(that.map);
				});
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
					fillColor: feature.properties.code
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
			centerTo(center){
				this.map.setView(L.latLng(JSON.parse(center)), 9); 
			}
		}
	}
</script>

<style scoped>
#map {
	width: 100%;
	height: 450px;
}
</style>