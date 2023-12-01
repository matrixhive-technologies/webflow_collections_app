import axios from 'axios';
import { userStore } from '@/stores/user';

export default class ajax {
    constructor() {
        this.setHeader('Accept', 'application/json');
        this.user = userStore();
         if (this.user.isLoggedIn) {
             //this.setHeader('test', this.user.getAuthBearerHeader())
         }
    }
    VUE_APP_API_URL: String = import.meta.env.VITE_API_URL;
    user: any = {}
    headers: any = {};
    setHeader(key: string, value: string) {
        this.headers[key] = value;
    }

    async get(url: string, params:any|undefined) {
        params = params? params : {}
        if (this.user.isLoggedIn) {
            params.key = this.user.getAuthBearerHeader();
        }
        let part =    new URLSearchParams(params).toString();
        return await axios.get(this.VUE_APP_API_URL + url+'?'+part, { headers: this.headers });
    }
    async post(url: string, params: any) {
        this.headers['Content-Type'] = 'multipart/form-data'
        params = params ? params : {};
        if (this.user.isLoggedIn) {
            params.key = this.user.getAuthBearerHeader();
        }
        console.log(params, "from params");
        return await axios.post(this.VUE_APP_API_URL + url, params, { headers: this.headers });
    }

    async postForm(url: string, params: any) {
        this.headers['Content-Type'] = 'multipart/form-data'
        if (this.user.isLoggedIn) {
            params.key = this.user.getAuthBearerHeader();
        }
        const form = new FormData();
        console.log(params, "from params");
        for(let i in params){
            form.append(i,params[i])
        }
        return await axios.postForm(this.VUE_APP_API_URL + url, form, { headers: this.headers });
    }
    async put(url: string, params: any) {
        if (this.user.isLoggedIn) {
            params.key = this.user.authToken.value;
        }
        return await axios.put(this.VUE_APP_API_URL + url, params, { headers: this.headers });
    }
    async delete(url: string) {
       
        return await axios.delete(this.VUE_APP_API_URL + url, { headers: this.headers });
    }
}
