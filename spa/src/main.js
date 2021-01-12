import Vue from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import VueAxios from 'vue-axios';
import { JSEncrypt } from 'jsencrypt';

Vue.config.productionTip = false
Vue.use(VueAxios, axios)

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')

Vue.prototype.$encryptedData = function(publicKey, data) {
  //new an object
  let encrypt = new JSEncrypt()
  //Setting public key
  encrypt.setPublicKey(publicKey)
  //password is the data to be encrypted. You don't need to pay attention to the + sign here, because rsa itself has already transcoded base64, and there is no +. It's all binary data
  let result = encrypt.encrypt(password);
  return result
}