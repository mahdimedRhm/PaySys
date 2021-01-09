<template>
  <div> Start transaction 
  <button @click="test">aaaaaaa</button>
  </div>
</template>
<script>
export default {
    methods: {
        test(){
            console.log()
            this.$http.get('http://localhost:8000/api/auth/test', 
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                }
            })
        }
    },
}
</script>