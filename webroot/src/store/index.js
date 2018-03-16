import Vuex from 'vuex'
import Vue from 'vue'
Vue.use(Vuex)
const store = new Vuex.Store({
  state: {
    token: ''
  },
  mutations: {
    setToken (state, payload) {
      state.token = payload.token
    }
  }
})
export default store
