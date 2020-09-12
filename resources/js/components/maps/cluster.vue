<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center">Maps</h5>
				</div>
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{message}}
						<ul>
							<li v-for="(e, i) in errors" v-bind:key="i">{{e}}</li>
						</ul>
					</div>

					<div class="col-sm-12">
						<h4 class="float-left mr-4 pt-1">Kabupaten</h4>
						<div class="btn-group" role="group" aria-label="Basic example">
							<button type="button" class="btn btn-secondary" v-for="(kab, i) in allKabupaten" v-bind:key="i" @click="getData(kab.name, kab.center_point)">{{kab.name}}</button>
						</div>
						<h3 class="text-center" v-if="kabName">{{kabName}}</h3>
					</div>
					<br>

					<div v-if="allParams['cluster']" class="row">
						<div class="col-sm-12">
							<h5 class="float-left mr-3 pt-1">Kelas</h5>
							<span class="badge badge-pill mr-1 badge-primary p-1" v-bind:style="{backgroundColor: c.color}" v-for="(c, i) in allClass['cluster']" v-bind:key="i">{{c.class}}</span>
						</div>
						<br>

						<div id="mapcluster" class="mapcluster col-sm-12">
							<div id="popup" class="ol-popup">
								<a href="#" id="popup-closer" class="ol-popup-closer"></a>
								<div id="popup-content"></div>
							</div>
						</div>

						<div class="col-md-12">
							<table class="table table-hover">
								<thead>
									<tr>
										<th v-for="(t, ti) in allData.table.header['cluster']" v-bind:key="ti">{{t}}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(c, i) in allData.table.data['cluster']" v-bind:key="i">
										<td v-for="(cc, ii) in c" v-bind:key="ii">{{cc}}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-sm-12">
						<hr>
					</div>
					<div v-if="allParams['class']" class="row">
						<div class="col-sm-12">
							<h5 class="float-left mr-3 pt-1">Kelas</h5>
							<span class="badge badge-pill mr-1 badge-primary p-1" v-bind:style="{backgroundColor: c.color}" v-for="(c, i) in allClass['class']" v-bind:key="i">{{c.class}}</span>
						</div>
						<br>

						<div id="mapclass" class="mapclass col-sm-12">
							<div id="popup" class="ol-popup">
								<a href="#" id="popup-closer" class="ol-popup-closer"></a>
								<div id="popup-content"></div>
							</div>
						</div>

						<div class="col-md-12">
							<table class="table table-hover">
								<thead>
									<tr>
										<th v-for="(t, ti) in allData.table.header['class']" v-bind:key="ti">{{t}}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(c, i) in allData.table.data['class']" v-bind:key="i">
										<td v-for="(cc, ii) in c" v-bind:key="ii">{{cc}}</td>
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
				kabName: null,
				errors: [],
				allParams: [],
				allKabupaten: [],
				allClass: [],
				colorList: ["#2ecc71", "#f39c12", "#c0392b", "#2980b9", "#8e44ad", "#2c3e50", "#7f8c8d", "#7bed9f", "#F22613", "#5352ed", "#DB5A6B", "#FCC9B9", "#875F9A", "#BF55EC"],
				map: {
					cluster: null,
					class: null
				},
				info: {
					cluster: null,
					class: null
				},
				geojson: {
					cluster: null,
					class: null
				},
				legend: null
			}
		},
		mounted() {
			this.getKabupaten()
		},
		watch: {
		},

		methods: {
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
				});
			},
			getData(kabName, center) {
				this.kabName = "Kabupaten/Kota: " + kabName
				this.allParams = []
				axios.post(window.baseUrl+"/"+"maps/cluster", {kabupaten: kabName}).then(response => {
					this.allData = response.data;
					this.allParams = response.data.parameters
					this.generateClass()
					this.initMaps()
					this.centerTo(center)
					this.initGeoJson()
					this.message  = null;
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						if (error.response.status == 422) {
							this.errors = error.response.data.errors;
						}
						this.messageType = 'error'
						this.message = error.response.message || "Gagal!";
					}
				});
			},
			generateClass(){
				for(var iii in this.allParams){
					this.allClass[iii] = []
					var classes = [];

					var key = this.allParams[iii]
					for(var i in this.allData.data){
						var val = this.allData.data[i][key]

						if(classes.indexOf(val) > -1 ) continue;
						classes.push(val)
						var c = {
							color: this.colorList[this.allClass[iii].length],
							class: (val.length <= 2) ? 'Cluster '+val : val
						}
						this.allClass[iii].push(c)
					}
				}
			},
			initMaps(){
				setTimeout(() => {
					for(var iii in this.allParams){
						this.initMap(iii)
					}
				}, 1000)
			},
			initGeoJson(){
				setTimeout(() => {
					for(var iii in this.allParams){
						var key = this.allParams[iii]
						this.changeGeoJson(iii, key)
					}
				}, 1100)
			},
			changeGeoJson(index, key){
				var geoJson = this.allData.geoJson
				var result  = {
					type: "FeatureCollection",
					features: []
				};
				for(var i in geoJson){
					var data = this.allData.data[i]
					var cless= (data[key].length <= 2) ? 'Cluster '+data[key] : data[key]
					var iii = this.allClass[index].find(o => o.class == cless)
					var f = {
						geometry: geoJson[i],
						type: "Feature",
						properties: {
							sub: index,
							code: iii.color,
							data: this.toTable(data)
						}
					}

					result.features.push(f)
				}
				this.geojson[index] = L.geoJson(result, {
					style: this.style,
					onEachFeature: this.onEachFeature
				}).addTo(this.map[index]);
			},
			toTable(data){
				var table = "<table>"
				for(var i in data){
					table += "<tr><td>"+i+"</td><td> = </td><td>"+data[i]+"</td></tr>"
				}
				return table + "</table>";
			},
			initMap(index){
				this.map[index] = L.map('map'+index).setView([-2, 118], 4)

				this.info[index] = L.control()
				L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
					maxZoom: 18,
					attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
					'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
					'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
					id: 'mapbox.light'
				}).addTo(this.map[index]);

				this.info[index].onAdd = function (map) {
					this._div = L.DomUtil.create('div', 'info');
					this.update();
					return this._div;
				};
				this.info[index].update = function (props) {
					this._div.innerHTML = '<h4>Land Suitability Class</h4>' +  (props ?
					 props.data 
					: 'Hover over a class');
				};
				this.info[index].addTo(this.map[index]);
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

				this.info[layer.feature.properties.sub].update(layer.feature.properties);
			},
			resetHighlight(e) {
				this.geojson[e.target.feature.properties.sub].resetStyle(e.target);
				this.info[e.target.feature.properties.sub].update();
			},
			zoomToFeature(e) {
				if(this.map['cluster']) this.map['cluster'].fitBounds(e.target.getBounds());
				if(this.map['class']) this.map['class'].fitBounds(e.target.getBounds());
			},
			onEachFeature(feature, layer) {
				layer.on({
					mouseover: this.highlightFeature,
					mouseout: this.resetHighlight,
					click: this.zoomToFeature
				});
			},
			centerTo(center){
				setTimeout(() => {
					if(this.map['cluster']) this.map['cluster'].setView(L.latLng(JSON.parse(center)), 9); 
					if(this.map['class']) this.map['class'].setView(L.latLng(JSON.parse(center)), 9); 
				}, 1100)
			}
		}
	}
</script>

<style scoped>
#mapcluster {
	width: 100%;
	height: 450px;
}
#mapclass {
	width: 100%;
	height: 450px;
}
</style>