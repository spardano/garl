<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<a href="javascript:;" class="btn btn-primary float-right" @click="goTo('kategori-create', null)">Tambah kategori</a>
			<h3>Manajemen kategori</h3>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<table class="table  table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th class="text-right">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(kategori, index) in kategoris.data" v-bind:key="index">
								<td>{{ ((kategoris.current_page-1) * kategoris.per_page) + (index+1) }} </td>
								<td>{{ kategori.name }} </td>
								<td class="text-right">
									<router-link :to="{ name: 'kategori-view', params: {id: kategori.id}}">Edit</router-link>
									<a class="btn btn-link text-danger" href="javascript:;" @click="setDelete(kategori.id)">Delete</a>
								</td>
							</tr>
							<tr v-if="!kategoris.data || kategoris.data.length == 0"><td colspan="3" class="text-center">Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='kategoris.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(kategoris.prev_page_url)" :href="kategoris.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='kategoris.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(kategoris.next_page_url)" :href="kategoris.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{kategoris.from || 0}} s/d {{kategoris.to || 0}} dari {{kategoris.total}} data <p>
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
				kategoris: [],
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
				axios.post(window.baseUrl+"/"+(url || '/kategori/paginate')).then(response => {
					this.kategoris = response.data;
				})
			},
			setDelete(id){
				this.deleteId = id
				$('#exampleModal').modal('toggle')
			},
			doDelete(){
				axios.post(window.baseUrl+"/kategori/delete/"+this.deleteId).then(response => {
					this.message = 'Kategori telah dihapus.';
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