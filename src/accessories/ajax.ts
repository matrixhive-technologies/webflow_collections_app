import axios from 'axios';
import { userStore } from '@/stores/user';

export default class ajax {
    constructor() {
        this.setHeader('Accept', 'application/json');
        this.user = userStore();
        // if (this.user.isLoggedIn) {
        //     this.setHeader('Authorization', this.user.getAuthBearerHeader())
        // }
    }
    VUE_APP_API_URL: String = import.meta.env.VITE_API_URL;
    user: any = {}
    headers: any = {};
    setHeader(key: string, value: string) {
        this.headers[key] = value;
    }

    async get(url: string) {
        return await axios.get(this.VUE_APP_API_URL + url, { headers: this.headers });
    }
    async post(url: string, params: any) {
        this.headers['Content-Type'] = 'multipart/form-data'
        return await axios.post(this.VUE_APP_API_URL + url, params, { headers: this.headers });
    }
    async put(url: string, params: any) {
        return await axios.put(this.VUE_APP_API_URL + url, params, { headers: this.headers });
    }
    async delete(url: string) {
        return await axios.delete(this.VUE_APP_API_URL + url, { headers: this.headers });
    }
}
