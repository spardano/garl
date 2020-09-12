<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<a href="javascript:;" class="btn btn-primary float-right" @click="goTo('kriteria-create', null)"  v-if="userData.role == 'ADMIN'">Tambah kriteria</a>
			<h3>Manajemen kriteria Kesesuaian Lahan Bawang Putih</h3>
			<h7>sumber data : Balai Besar Sumber Daya Lahan Pertanian (BBSDLP), Badan Meteorologi Klimatologi dan Geofisika (BMKG) dan United States Geological survey (USGS)</h7>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Kategori</th>
								<th>Nama</th>
								<th>Type</th>
								<th>Nilai</th>
								<th class="text-right" v-if="userData.role == 'ADMIN'">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(kriteria, index) in kriterias.data" v-bind:key="index">
								<td>{{ ((kriterias.current_page-1) * kriterias.per_page) + (index+1) }} </td>
								<td>{{ kriteria.kategori.name }} </td>
								<td>{{ kriteria.name }} </td>
								<td>{{ kriteria.type }} </td>
								<td>
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Min</th>
												<th>Max</th>
												<th>Ket.</th>
												<th>Kesesuaian</th>
												<th>Rekomendasi</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="(det, index) in kriteria.details" v-bind:key="index">
												<td>{{det.min}}</td>
												<td>{{det.max}}</td>
												<td>{{det.keterangan}}</td>
												<td>{{det.kesesuaian}}</td>
												<td>{{det.rekomendasi}}</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td class="text-right" v-if="userData.role == 'ADMIN'">
									<router-link :to="{ name: 'kriteria-view', params: {id: kriteria.id}}">Edit</router-link>
									<a class="btn btn-link text-danger" href="javascript:;" @click="setDelete(kriteria.id)">Delete</a>
								</td>
							</tr>
							<tr v-if="!kriterias.data || kriterias.data.length == 0"><td colspan="5" class="text-center">Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='kriterias.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(kriterias.prev_page_url)" :href="kriterias.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='kriterias.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(kriterias.next_page_url)" :href="kriterias.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{kriterias.from || 0}} s/d {{kriterias.to || 0}} dari {{kriterias.total}} data <p>
					<div class='clearfix'/>
				</div>
			</div>
		</div>

		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Konfirmasi</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Kamu yakin akan menghapus data ini?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger" @click="doDelete()">Hapus</button>
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
				kriterias: [],
				message: '',
				messageType: 'error',
				deleteId: null
			}
		},

		mounted() {
			this.paginate(null)
		},

		methods: {

			paginate(url) {
				axios.post(window.baseUrl+"/"+(url || '/kriteria/paginate')).then(response => {
					this.kriterias = response.data;
				})
			},
			setDelete(id){
				this.deleteId = id
				$('#exampleModal').modal('toggle')
			},
			doDelete(){
				axios.post(window.baseUrl+"/kriteria/delete/"+this.deleteId).then(response => {
					this.message = 'Kriteria telah dihapus.';
					this.messageType = 'success';
					this.paginate(null)
					$('#exampleModal').modal('toggle')
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, coba kembali!";
					}
				});
			}
		}
	}
</script>