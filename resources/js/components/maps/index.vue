<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center">Maps</h5>
				</div>
				<div class="card-body">
					<div class="col-sm-12">
						<h4 class="float-left mr-4 pt-1">Kabupaten</h4>
						<div class="btn-group" role="group" aria-label="Basic example">
							<button type="button" class="btn btn-secondary" v-for="(kab, i) in allKabupaten" v-bind:key="i" @click="centerTo(kab.center_point)">{{kab.name}}</button>
						</div>
					</div>
					<br>
					<div class="col-sm-12">
						<h5 class="float-left mr-3 pt-1">Kelas</h5>
						<span class="badge badge-pill mr-1 badge-primary p-1" v-bind:style="{backgroundColor: c.color}" v-for="(c, i) in allClass" v-bind:key="i">{{c.class}}</span>
					</div>
					<br>

					<div class="row">
						<div id="map" class="map col-sm-8">
							<div id="popup" class="ol-popup">
								<a href="#" id="popup-closer" class="ol-popup-closer"></a>
								<div id="popup-content"></div>
							</div>
						</div>

						<div class="col-md-4">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Kesesuaian Lahan</th>
										<th>Luas Area</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(c, i) in allClass" v-bind:key="i">
										<td>{{c.class}}</td>
										<td>{{c.area}}</td>
									</tr>
								</tbody>
							</table>
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
				allClass: [],
				maps: [],
				dirty: false,
				map: null,
				info: null,
				geojson: null,
				legend: null
			}
		},
		mounted() {
            this.getKelurahan();
			this.getKabupaten()
		},
		watch: {
		},

		methods: {
			getClass() {
				axios.post(window.baseUrl+"/"+"class/all", {}).then(response => {
					this.allClass = response.data;
				});
			},
			getKelurahan() {
				axios.post(window.baseUrl+"/"+"kelurahan/maps", {}).then(response => {
					this.maps = response.data.maps;
					this.allClass = response.data.kelas;
					this.initMap();
				});
			},
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
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
				this.geojson = L.geoJson(this.maps, {
					style: this.style,
					onEachFeature: this.onEachFeature
				}).addTo(this.map);

				this.legend = L.control({position: 'bottomright'});

				this.legend.onAdd = function (map) {

				};

				// legend.addTo(map);

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