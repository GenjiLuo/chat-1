import Vuex from 'vuex'
import Vue from 'vue'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    token: '',
    info: {}
  },
  mutations: {
    setToken (state, payload) {
      state.token = payload.token
    },
    setInfo (state, payload) {
      state.info = payload
    },
    setAvatar (state, payload) {
      state.info.avatar = payload
    }
  },
  getters: {
    getInfo () {
      return this.info
    },
    headers () {
      return {
        Authorization: 'Bearer ' + store.state.token
      }
    }
  },
  actions: {

  }
})
export default store
