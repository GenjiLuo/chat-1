import axios from 'axios'
import qs from 'qs'
import store from '../store/index'
// const base = 'http://127.0.0.1:8081'
// export const ws = 'ws://127.0.0.1:9501'
const base = 'http://192.168.2.238:8081'
export const ws = 'ws://192.168.2.238:9501'
axios.interceptors.request.use(
  config => {
    config.headers = {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
    if (store.state.token !== '') {
      config.headers.Authorization = 'Bearer ' + store.state.token
    }
    return config
  })
axios.interceptors.response.use(
  res => {
    return res
  },
  err => {
    return err
  }
)
export const login = params => {
  return axios.put(`${base}/token`, qs.stringify(params)).then(res => res.data)
}
export const register = params => {
  return axios.post(`${base}/user`, qs.stringify(params)).then(res => res.data)
}
export const checkUsername = params => {
  return axios.get(`${base}/checkUsername`, {params: params}).then(res => res.data)
}
export const loginByToken = params => {
  return axios.put(`${base}/token`, qs.stringify(params)).then(res => res.data)
}
export const deleteChat = id => {
  return axios.delete(`${base}/chat/${id}`).then(res => res.data)
}
export const createChat = params => {
  return axios.post(`${base}/chat`, qs.stringify(params)).then(res => res.data)
}
export const updateUser = params => {
  return axios.put(`${base}/user/${params.id}`, qs.stringify(params)).then(res => res.data)
}
export const friendList = params => {
  return axios.get(`${base}/friend`, {params: params}).then(res => res.data)
}
export const deleteFriend = id => {
  return axios.delete(`${base}/friend/${id}`).then(res => res.data)
}
export const createApply = params => {
  return axios.post(`${base}/apply`, qs.stringify(params)).then(res => res.data)
}
export const avatarUrl = `${base}/avatar`
