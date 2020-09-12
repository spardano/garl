<template>
	<div class="container-fluid row">
		<div class="col-sm-12">
			<h3>Data Kabupaten</h3>
			<div class="clearfix"></div>
			<div class="card mt-2">
				<div class="card-body">
					<table class="table  table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>Titik Maps (Lat, Lang)</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(kabupaten, index) in kabupatens.data">
								<td>{{ ((kabupatens.current_page-1) * kabupatens.per_page) + (index+1) }} </td>
								<td>{{ kabupaten.name }} </td>
								<td>{{ kabupaten.center_point }} </td>
							</tr>
							<tr v-if="!kabupatens.data"><td>Tidak ada data</td></tr>
						</tbody>
					</table>
					<br>

					<a v-if='kabupatens.prev_page_url' class='btn btn-danger float-left' @click.prevent="paginate(kabupatens.prev_page_url)" :href="kabupatens.prev_page_url">&laquo;
						Sebelumnya</a>
					<a v-if='kabupatens.next_page_url'  class='btn btn-primary float-right py-2' @click.prevent="paginate(kabupatens.next_page_url)" :href="kabupatens.next_page_url">Selanjutnya
						&raquo;</a>
					<p class='text-center'>Menampilkan : {{kabupatens.from}} s/d {{kabupatens.to}} dari {{kabupatens.total}} data <p>
					<div class='clearfix'/>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				kabupatens: []
			}
		},

		mounted() {
			axios.post(window.baseUrl+"/"+'/kabupaten/paginate').then(response => {
				this.kabupatens = response.data;
			});
		},

		methods: {
			paginate(url) {
				axios.post(window.baseUrl+"/"+url).then(response => {
					this.kabupatens = response.data;
				})
			}
		}
	}
</script>