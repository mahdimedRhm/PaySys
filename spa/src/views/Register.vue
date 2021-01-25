<template>
  <div class="home">
    <div  method="post">
      <div class="container">
        <label for="uname"><b>Email</b></label>
        <input type="text" placeholder="Enter email" v-model="user.email" >

        <label for="uname"><b>Name</b></label>
        <input type="text" placeholder="Enter Name" v-model="user.name">

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter password" v-model="user.password">

        <label for="psw"><b>Confirm password</b></label>
        <input type="password" placeholder="Enter password" v-model="user.password_confirmation" >

        <span v-if="error" style="color: red;"> {{errorMesage}} </span>

        <button @click="createAccount()">Create an account</button>
      </div>
    </div>
    <button @click="loginPage()" >Login</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {},
      errorMesage:'',
      error: false

    }
  },
  methods: {
    loginPage(){
      this.$router.push('/')
    },
    createAccount(){
      this.error = false;
      this.errorMesage = "";
      this.validate();
      if (this.error) {
        return;
      }
      this.$http.post('http://localhost:8000/api/auth/register', this.user).then(res=>{
        console.log(res);
        this.$router.push('/'); 
      });
    },
    isValidEmail(value) {
      let pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      return pattern.test(value);
    },
    validate(){
      if (!this.isValidEmail(this.user.email) ){
        this.errorMesage = "please check email";
        this.error = true;
        return;
      }
      if (!this.user.name || !this.user.password){
        this.errorMesage = "please check name or password";
        this.error = true;
        return;
      }
      if ((this.user.password_confirmation != this.user.password)){
        this.errorMesage = "please check password confirmation";
        this.error = true;
        return;
      }
    }
  },
  computed: {
    
  },
}
</script>
<style>
form {
  border: 3px solid #f1f1f1;
}

/* Full-width inputs */
input[type=text], input[type=password] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

/* Set a style for all buttons */
button {
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
}

/* Add a hover effect for buttons */
button:hover {
  opacity: 0.8;
}

/* Extra style for the cancel button (red) */
.cancelbtn {
  width: auto;
  padding: 10px 18px;
  background-color: #f44336;
}

/* Center the avatar image inside this container */
.imgcontainer {
  text-align: center;
  margin: 24px 0 12px 0;
}

/* Avatar image */
img.avatar {
  width: 40%;
  border-radius: 50%;
}

/* Add padding to containers */
.container {
  padding: 16px;
}

/* The "Forgot password" text */
span.psw {
  float: right;
  padding-top: 16px;
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
  span.psw {
    display: block;
    float: none;
  }
  .cancelbtn {
    width: 100%;
  }
}
</style>
