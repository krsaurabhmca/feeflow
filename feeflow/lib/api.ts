import axios from 'axios';
import * as SecureStore from 'expo-secure-store';

// Replace with your actual development machine IP for local testing
const BASE_URL = 'https://feeflow.offerplant.com/api/v1/';

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

export const getStats = async () => {
    const response = await api.get('dashboard.php');
    return response.data;
};

export const getFees = async (student_id: any = null) => {
    const url = student_id ? `fees.php?student_id=${student_id}` : 'fees.php';
    const response = await api.get(url);
    return response.data;
};

export const getCategories = async () => {
    const response = await api.get('categories.php');
    return response.data;
};

export const addCategory = async (data: any) => {
    const response = await api.post('categories.php', data);
    return response.data;
};

export const register = async (data: any) => {
    const response = await api.post('register.php', data);
    return response.data;
};

export const addClass = async (data: any) => {
    const response = await api.post('classes.php', data);
    return response.data;
};

export const addStudent = async (data: any) => {
    const response = await api.post('students.php', data);
    return response.data;
};

export const updateStudent = async (id: any, data: any) => {
    const response = await api.post(`students.php?action=update&id=${id}`, data);
    return response.data;
};

export const getProfile = async () => {
    const response = await api.get('profile.php');
    return response.data;
};

export const updateProfile = async (data: any) => {
    const response = await api.post('profile.php', data);
    return response.data;
};

export const changePassword = async (data: any) => {
    const response = await api.post('profile.php?action=change_password', data);
    return response.data;
};



