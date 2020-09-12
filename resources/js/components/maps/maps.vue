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
						<h4 class="float-left mr-3 pt-1">Kabupaten</h4>
						<div class="btn-group" role="group" aria-label="Basic example">
							<button type="button" class="btn btn-secondary" v-for="(kab, i) in allKabupaten" v-bind:key="i" @click="changeName(kab.name, kab.center_point)">{{kab.name}}</button>
						</div>
						<h3 class="text-center" v-if="kabName">{{kabName}}</h3>
					</div>
					<br>
					<div v-if="loading" class="row">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
						<g transform="translate(80,50)">
						<g transform="rotate(0)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="1">
						<animateTransform attributeName="transform" type="scale" begin="-0.875s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.875s"></animate>
						</circle>
						</g>
						</g><g transform="translate(71.21320343559643,71.21320343559643)">
						<g transform="rotate(45)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.875">
						<animateTransform attributeName="transform" type="scale" begin="-0.75s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.75s"></animate>
						</circle>
						</g>
						</g><g transform="translate(50,80)">
						<g transform="rotate(90)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.75">
						<animateTransform attributeName="transform" type="scale" begin="-0.625s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.625s"></animate>
						</circle>
						</g>
						</g><g transform="translate(28.786796564403577,71.21320343559643)">
						<g transform="rotate(135)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.625">
						<animateTransform attributeName="transform" type="scale" begin="-0.5s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.5s"></animate>
						</circle>
						</g>
						</g><g transform="translate(20,50.00000000000001)">
						<g transform="rotate(180)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.5">
						<animateTransform attributeName="transform" type="scale" begin="-0.375s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.375s"></animate>
						</circle>
						</g>
						</g><g transform="translate(28.78679656440357,28.786796564403577)">
						<g transform="rotate(225)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.375">
						<animateTransform attributeName="transform" type="scale" begin="-0.25s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.25s"></animate>
						</circle>
						</g>
						</g><g transform="translate(49.99999999999999,20)">
						<g transform="rotate(270)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.25">
						<animateTransform attributeName="transform" type="scale" begin="-0.125s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.125s"></animate>
						</circle>
						</g>
						</g><g transform="translate(71.21320343559643,28.78679656440357)">
						<g transform="rotate(315)">
						<circle cx="0" cy="0" r="6" fill="#85a2b6" fill-opacity="0.125">
						<animateTransform attributeName="transform" type="scale" begin="0s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
						<animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="0s"></animate>
						</circle>
						</g>
						</g>
						</svg>
					</div>
					<div class="row">
						<div class="col-sm-12" v-if="!loading">
							<h5 class="float-left mr-3 pt-1">Kelas</h5>
							<span class="badge badge-pill mr-1 badge-primary p-1" v-bind:style="{backgroundColor: c.color}" v-for="(c, i) in allClass" v-bind:key="i">{{c.class}}</span>
						</div>
						<br>

						<div id="map" class="map col-md-8">
							<div id="popup" class="ol-popup">
								<a href="#" id="popup-closer" class="ol-popup-closer"></a>
								<div id="popup-content"></div>
							</div>
						</div>
						<div class="col-md-4" v-if="!loading">
							<table class="table">
								<thead>
									<tr>
										<th>Kabupaten</th>
										<th>Kesesuaian Lahan</th>
										<th>Luas Area(ha)</th>
										<th>Total Luas Area (ha)</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td rowspan="3">Magetan</td>
										<td>S1, Sangat Sesuai</td>
										<td>7,702.11</td>
										<td>70,143</td>
									</tr>
									<tr>
										<td>S2, Cukup Sesuai</td>
										<td>43,077.36</td>
									</tr>
									<tr>
										<td>S3, Sesuai Marginal</td>
										<td>15,554,01</td>
									</tr>
									<tr>
										<td rowspan="3">Solok</td>
										<td>S1, Sangat Sesuai</td>
										<td>93,910.7</td>
										<td>335,086.53</td>
									</tr>
									<tr>
										<td>S2, Cukup Sesuai</td>
										<td>204,091.9</td>
									</tr>
									<tr>
										<td>S3, Sesuai Marginal</td>
										<td>18,190.08</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-sm-12">
						<hr>
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
				loading: true,
				kabName: null,
				classUsed: null,
				errors: [],
				allData: [],
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
			this.getData()
		},
		watch: {
		},

		methods: {
			getKabupaten() {
				axios.post(window.baseUrl+"/"+"kabupaten/all", {}).then(response => {
					this.allKabupaten = response.data;
				});
			},
			changeName(kabname, center){
				this.kabName = "Kabupaten/Kota: " + kabname
				this.centerTo(center)
			},
			getData() {
				this.allParams = []
				axios.post(window.baseUrl+"/"+"maps/maps").then(response => {
					this.message  = null;
					this.allData = response.data;
					this.classUsed = response.data.class;
					this.generateClass()
					this.initMap()
					this.initGeoJson()
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
				var classes = [];

				for(var i in this.allData.data){
					var val = this.allData.data[i][this.classUsed]

					if(classes.indexOf(val) > -1 ) continue;
					classes.push(val)
					var c = {
						color: this.colorList[this.allClass.length],
						class: val
					}
					this.allClass.push(c)
				}
			},
			initGeoJson(){
				var geoJson = this.allData.geoJson
				this.allData.geoJson = null;
				var result  = {
					type: "FeatureCollection",
					features: []
				};
				for(var i in geoJson){
					var data = this.allData.data[i]
					var cless= data[this.classUsed]
					var iii = this.allClass.find(o => o.class == cless)
					var f = {
						geometry: geoJson[i],
						type: "Feature",
						properties: {
							code: iii.color,
							data: this.toTable(data)
						}
					}

					result.features.push(f)
				}
				geoJson = null
				this.geojson = L.geoJson(result, {
					style: this.style,
					onEachFeature: this.onEachFeature
				}).addTo(this.map);
				
				this.loading= false
			},
			toTable(data){
				var table = "<table>"
				for(var i in data){
					if(i == 'AREA') data[i] += ' m<sup>2</sup>'
					table += "<tr><td>"+i+"</td><td> = </td><td>"+data[i]+"</td></tr>"
				}
				return table + "</table>";
			},
			initMap(){
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
				setTimeout(() => {
					this.map.setView(L.latLng(JSON.parse(center)), 9); 
				}, 1100)
			}
		}
	}
</script>