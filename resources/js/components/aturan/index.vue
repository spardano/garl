<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<a href="javascript:;" class="btn btn-primary float-right" @click="goTo('aturan-create', null)" v-if="userData.role == 'ADMIN'">Tambah aturan</a>
			<h3>Manajemen Aturan</h3>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Kabupaten</th>
								<th>Aturan</th>
								<th>Kelas</th>
								<th class="text-right" v-if="userData.role == 'ADMIN'">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(aturan, index) in aturans.data" v-bind:key="index">
								<td>{{aturan.id}} </td>
								<td>{{ aturan.kabupaten.name }} </td>
								<td>
									<p>JIKA 
										<span v-for="(d, i) in aturan.details" v-bind:key="d.id">
											<span v-if="i > 0"> DAN </span> {{d.kriteria.name}} = {{d.value}}
										</span>
									</p>	
								</td>
								<td>{{ aturan.kelas.class }} </td>
								<td class="text-right" v-if="userData.role == 'ADMIN'">
									<router-link :to="{ name: 'aturan-view', params: {id: aturan.id}}">Edit</router-link>
									<a class="btn btn-link text-danger" href="javascript:;" @click="setDelete(aturan.id)">Delete</a>
								</td>
							</tr>
							<tr v-if="!aturans.data || aturans.data.length == 0"><td colspan="5" class="text-center">Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='aturans.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(aturans.prev_page_url)" :href="aturans.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='aturans.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(aturans.next_page_url)" :href="aturans.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{aturans.from || 0}} s/d {{aturans.to || 0}} dari {{aturans.total}} data <p>
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
				aturans: [],
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
				axios.post(window.baseUrl+"/"+(url || '/aturan/paginate')).then(response => {
					this.aturans = response.data;
				})
			},
			setDelete(id){
				this.deleteId = id
				$('#exampleModal').modal('toggle')
			},
			doDelete(){
				axios.post(window.baseUrl+"/aturan/delete/"+this.deleteId).then(response => {
					this.message = 'Aturan telah dihapus.';
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