import Vue from 'vue'
import Router from 'vue-router'
import Chat from '@/components/Chat'
import Login from '@/components/Login'
import Register from '@/components/Register'
import {loginByToken} from '../api/api.js'

Vue.use(Router)

const notLoginJump = (to, form, next) => {
  let token = localStorage.getItem('token')
  if (token) {
    loginByToken({token: token}).then(res => {
      if (parseInt(res.status) === 1) {
        console.log('haha')
        localStorage.setItem('user', JSON.stringify(res.user))
        next()
      } else {
        next('/')
      }
    })
  } else {
    next('/')
  }
}

const loginJump = (to, form, next) => {
  let token = localStorage.getItem('token')
  if (token) {
    loginByToken({token: token}).then(res => {
      if (parseInt(res.status) === 1) {
        localStorage.setItem('user', JSON.stringify(res.user))
        next('/chat')
      } else {
        next()
      }
    })
  } else {
    next()
  }
}

const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/chat',
      name: 'chat',
      component: Chat,
      beforeEnter: notLoginJump
    },
    {
      path: '/',
      name: 'Login',
      component: Login,
      beforeEnter: loginJump
    },
    {
      path: '/register',
      name: 'register',
      component: Register,
      beforeEnter: loginJump
    }
  ]
})

export default router
