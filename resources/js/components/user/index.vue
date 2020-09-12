<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<a href="javascript:;" class="btn btn-primary float-right" @click="goTo('user-create', null)">Tambah user</a>
			<h3>Manajemen user</h3>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<div v-if="message" class="alert" v-bind:class="(messageType=='error') ? 'alert-danger' : 'alert-success'">
						{{ message }}
					</div>
					<table class="table  table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>Email</th>
								<th>Role</th>
								<th class="text-right">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(user, index) in users.data" v-bind:key="index">
								<td>{{ ((users.current_page-1) * users.per_page) + (index+1) }} </td>
								<td>{{ user.name }} </td>
								<td>{{ user.email }} </td>
								<td>{{ user.role }} </td>
								<td class="text-right">
									<router-link :to="{ name: 'user-view', params: {id: user.id}}">Edit</router-link>
									<a class="btn btn-link text-danger" href="javascript:;" @click="setDelete(user.id)">Delete</a>
								</td>
							</tr>
							<tr v-if="!users.data"><td>Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='users.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(users.prev_page_url)" :href="users.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='users.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(users.next_page_url)" :href="users.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{users.from}} s/d {{users.to}} dari {{users.total}} data <p>
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
				users: [],
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
				axios.post(window.baseUrl+"/"+(url || '/user/paginate')).then(response => {
					this.users = response.data;
				})
			},
			setDelete(id){
				this.deleteId = id
				$('#exampleModal').modal('toggle')
			},
			doDelete(){
				axios.post(window.baseUrl+"/user/delete/"+this.deleteId).then(response => {
					this.message = 'User telah dihapus.';
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