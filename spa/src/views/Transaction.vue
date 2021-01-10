<template>
  <div> Start transaction 
  <button @click="addCard" v-if="!user.card_id" >Get a card</button>
  </div>
</template>
<script>
export default {
    data() {
        return {
            user:{}
        }
    },
    mounted() {
        this.getUser();
    },
    methods: {
        getUser(){
            this.$http.post('http://localhost:8000/api/auth/me', 
            {},
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                }
            })
        },
        test(){
            this.$http.get('http://localhost:8000/api/auth/test', 
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                }
            })
        },
        addCard(){
            this.$http.post('http://localhost:8000/api/auth/card',
            {},
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                }
            });
        }

    },
}
</script>