<template>
    <div>
        <button @click="redirect" class="btn btn-info">Go to Page...</button>
        <h1>Hihi</h1>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },

        methods: {
            redirect() {
                this.$router.push({
                    name: 'default'
                })
            }
        }
    }
</script>
