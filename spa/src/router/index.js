import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Register from '../views/Register.vue'
import Transaction from '../views/Transaction.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/transaction',
    name: 'Transaction',
    component: Transaction
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to, from, next) => {
  if (to.name == 'Home' && localStorage.getItem('access_token')) next({ name: 'Transaction' })
  if (to.name == 'Register' && localStorage.getItem('access_token')) next({ name: 'Transaction' })
  if (to.name == 'Transaction' && !localStorage.getItem('access_token')) next({ name: 'Home' })

  // if the user is not authenticated, `next` is called twice
  next()
})
export default router
