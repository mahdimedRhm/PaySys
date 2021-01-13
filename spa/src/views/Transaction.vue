<template>
  <div> 
    <button @click="addCard"  >Get a card</button>
    <div v-if="card.code">
        your card number: {{card.code}}  
        your key: {{card.key}}
        your amount: {{card.amount}}

    </div>
    <div class="home">
        <div class="container">
            <label ><b>your card number</b></label>
            <input type="number" v-model="from.code"  required><br/>
    
            <label ><b>your card key</b></label>
            <input type="number" v-model="from.key" required><br/>
    
            <label ><b>the card number you want to send money to</b></label>
            <input type="number" v-model="to.code" required><br/>
    
            <label ><b>the key card you want to send money to</b></label>
            <input type="number"  v-model="to.key"><br/>

            <label ><b>amount:</b></label>
            <input type="number"  v-model="amount"><br/>

            <button @click="sendMoney()">Send</button>
        </div>
    </div>
  </div>
</template>
<script>
import { JSEncrypt } from 'jsencrypt'

export default {
    data() {
        return {
            user: {},
            card: {
                code: null,
                key: null,
                amount: 0
            },
            from:{},
            to:{},
            amount: 0,
            pubKey:""
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
            }).then(res => {
                this.user = res.data;
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
            this.$http.post('http://localhost:8000/api/card',
            {},
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                }
            }).then(response=>{
                this.card = response.data;
            });
        },
        sendMoney(){
            this.$http.get('http://localhost:8000/api/auth/pubkey', 
                {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                }
            }).then(res => {
                this.pubKey = res.data;
                if (!this.pubKey) return; 
                this.encryptData();
                this.$http.post('http://localhost:8000/api/auth/transaction', 
                    {
                        from: this.from,
                        to: this.to,
                        amount: this.amount

                    },
                    {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem("access_token")}` 
                    }
                }).then(res => {
                    console.log('sent');
                })
            })

            
            
            
        },
        encryptData(){
            let encryptor = new JSEncrypt();
            encryptor.setPublicKey(this.pubKey);
            this.from.code = encryptor.encrypt(this.from.code);
            this.from.key = encryptor.encrypt(this.from.key); 
            this.to.code = encryptor.encrypt(this.to.code); 
            this.to.key = encryptor.encrypt(this.to.key); 

            console.log(this.from, this.to);
            // encryptor.setPrivateKey();
            // console.log(encryptor.decrypt(secretWord));
        },
        getPubkey(){
            
        }
    },
}
</script>