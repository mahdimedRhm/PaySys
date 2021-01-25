<template>
  <div class="home">
    <div>
      <div class="container">
        <label for="uname"><b>Email</b></label>
        <input type="text" placeholder="Enter Email" v-model="user.email" required>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" v-model="user.password" required>
        
        <span v-if="error" style="color: red;"> {{errorMessage}} </span>

        <button @click="login" type="submit">Login</button>
        <button @click="registerPage()">Register</button>
      </div>
    </div>
  </div>
</template>
<script>
// @ is an alias to /src

export default {
  name: 'Home',
  data() {
    return {
      user:{},
      error: false,
      errorMessage: ""
    }
  },
  methods: {
    registerPage(){
      this.$router.push('/register')
    },
    login(){
      this.error = false;
      this.errorMessage = "";

      this.validate()
      if (this.error) return;
      this.$http.post('http://localhost:8000/api/auth/login', this.user).then(res => {
        localStorage.setItem('access_token', res.data.access_token);
        console.log(res);
        this.$router.push('/transaction');
      }, error => {
        this.errorMessage = "unauthorized";
        this.error = true;

      });
    },
    isValidEmail(value) {
      let pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      return pattern.test(value);
    },
    validate(){
      if (!this.isValidEmail(this.user.email) ){
        this.errorMessage = "please check email";
        this.error = true;
        return;
      }
      if (!this.user.password){
        this.errorMessage = "please check password";
        this.error = true;
        return;
      }
    }
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
