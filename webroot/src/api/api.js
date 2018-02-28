import axios from 'axios'
import qs from 'qs'

const base = 'http://192.168.92.121'
export const ws = 'ws://192.168.92.121:9501'
axios.interceptors.request.use(
  config => {
    config.headers = {
      'Content-Type': 'application/x-www-form-urlencoded'
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
export const avatarUrl = `${base}/api/user/avatar`
