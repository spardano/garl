<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<!-- <a href="javascript:;" class="btn btn-primary float-right" @click="goTo('class-create', null)">Tambah class</a> -->
			<h3>Manajemen class</h3>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<table class="table  table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Class</th>
								<th>Color</th>
								<th class="text-right">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(c, index) in classs.data" v-bind:key="index">
								<td>{{ ((classs.current_page-1) * classs.per_page) + (index+1) }} </td>
								<td>{{ c.class }} </td>
								<td>{{ c.color }} <span class="p-1" v-bind:style="{backgroundColor: c.color}"> &nbsp;</span></td>
								<td class="text-right">
									<router-link :to="{ name: 'class-view', params: {id: c.id}}">Edit</router-link>
									<a class="btn btn-link text-danger" href="javascript:;" @click="setDelete(c.id)">Delete</a>
								</td>
							</tr>
							<tr v-if="!classs.data || classs.data.length == 0"><td colspan="3" class="text-center">Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='classs.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(classs.prev_page_url)" :href="classs.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='classs.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(classs.next_page_url)" :href="classs.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{classs.from || 0}} s/d {{classs.to || 0}} dari {{classs.total || 0}} data <p>
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
				classs: [],
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
				axios.post(window.baseUrl+"/"+(url || '/class/paginate')).then(response => {
					this.classs = response.data;
				})
			},
			setDelete(id){
				this.deleteId = id
				$('#exampleModal').modal('toggle')
			},
			doDelete(){
				axios.post(window.baseUrl+"/class/delete/"+this.deleteId).then(response => {
					this.message = 'Kelas telah dihapus.';
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