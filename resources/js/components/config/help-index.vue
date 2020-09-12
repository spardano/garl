<template>
	<div class="container-fluid row">
		<div class="col-sm-6 offset-sm-3">
			<div class="card">
				<div class="card-header">
					<h5 class="text-center"> Bantuan</h5>
				</div>
				<div class="card-body" v-html="data.value">
				</div>
			</div>

		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				message: null,
				messageType: 'error',
				errors: [],
				data: {
					key: "help",
					value: ""
				},
				dirty: false
			}
		},
		mounted() {
            this.getHelp();
		},

		methods: {
			getHelp() {
				axios.post(window.baseUrl+"/"+"config/detail/help", {}).then(response => {
					this.data = response.data;
					this.data.value = this.data.value.replace(/\n/g, "<br />");
				}).catch(error => {
					if (!_.isEmpty(error.response)) {
						this.errors = error.response.data;
					}
						this.messageType = 'error'
						this.message = error.response.message || "Gagal, periksa kembali form!";
						if(error.response.status == 404) {
							this.dirty = true;
							this.message = "Data tidak ditemukan.";
							setTimeout(function(){window.history.back()}, 1000);
						}
				});
			}
		}
	}
</script>