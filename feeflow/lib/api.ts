import axios from 'axios';
import * as SecureStore from 'expo-secure-store';

// Replace with your actual development machine IP for local testing
const BASE_URL = 'http://192.168.1.4/feeflow/api/v1/';

const api = axios.create({
    baseURL: BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    },
});

api.interceptors.request.use(async (config) => {
    const token = await SecureStore.getItemAsync('api_key');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;

export const login = async (email: any, password: any) => {
    const response = await api.post('login.php', { email, password });
    return response.data;
};

export const getDashboard = async () => {
    const response = await api.get('dashboard.php');
    return response.data;
};

export const getStudents = async (search: string = '') => {
    const response = await api.get(`students.php?search=${search}`);
    return response.data;
};

export const getClasses = async () => {
    const response = await api.get('classes.php');
    return response.data;
};

export const collectFee = async (data: any) => {
    const response = await api.post('fees.php', data);
    return response.data;
};
