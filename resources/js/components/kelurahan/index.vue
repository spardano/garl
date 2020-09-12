<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<!-- <a href="javascript:;" class="btn btn-primary float-right" @click="goTo('kelurahan-create', null)">Tambah kelurahan</a> -->
			<h3>Manajemen kelurahan</h3>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<table class="table  table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>Kecamatan</th>
								<th>Kabupaten</th>
								<th>Area</th>
								<th class="text-right">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(kelurahan, index) in kelurahans.data" v-bind:key="index">
								<td>{{ ((kelurahans.current_page-1) * kelurahans.per_page) + (index+1) }} </td>
								<td>{{ kelurahan.name }} </td>
								<td>{{ kelurahan.kecamatan.name }} </td>
								<td>{{ kelurahan.kecamatan.kabupaten.name }} </td>
								<td>{{ kelurahan.area }} </td>
								<td class="text-right">
									<router-link :to="{ name: 'kelurahan-view', params: {id: kelurahan.id}}">Edit</router-link>
									<a class="btn btn-link text-danger" href="javascript:;" @click="setDelete(kelurahan.id)">Delete</a>
								</td>
							</tr>
							<tr v-if="!kelurahans.data"><td>Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='kelurahans.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(kelurahans.prev_page_url)" :href="kelurahans.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='kelurahans.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(kelurahans.next_page_url)" :href="kelurahans.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{kelurahans.from}} s/d {{kelurahans.to}} dari {{kelurahans.total}} data <p>
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
				kelurahans: [],
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
				axios.post(window.baseUrl+"/"+(url || '/kelurahan/paginate')).then(response => {
					this.kelurahans = response.data;
				})
			},
			setDelete(id){
				this.deleteId = id
				$('#exampleModal').modal('toggle')
			},
			doDelete(){
				axios.post(window.baseUrl+"/kelurahan/delete/"+this.deleteId).then(response => {
					this.message = 'Kelurahan telah dihapus.';
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