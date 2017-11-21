import axios from 'axios'
import qs from 'qs'

const base = 'http://localhost/chat'
axios.interceptors.response.use(
  res => {
    return res
  },
  err => {
    return err
  }
)
export const login = params => {
  return axios.post(`${base}/api/user/login`, qs.stringify(params)).then(res => res.data)
}
export const register = params => {
  return axios.post(`${base}/api/user/create`, qs.stringify(params)).then(res => res.data)
}
export const checkUsername = params => {
  return axios.get(`${base}/api/user/check`, {params: params}).then(res => res.data)
}
export const loginByToken = params => {
  return axios.get(`${base}/api/user/loginByToken`, {params: params}).then(res => res.data)
}
export const avatarUrl = `${base}/api/user/avatar`
