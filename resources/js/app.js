import './bootstrap';
import axios from 'axios';
import '../css/app.css';
import { createApp } from 'vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Friend from './components/Friend.vue';
import Profile from './components/Profile.vue';
import Conversation from './components/Conversation.vue';
import Messages from './components/Messages.vue';
import Home from './components/Home.vue';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || window.location.origin;

const app = createApp({})
app.component('login', Login);
app.component('register', Register);
app.component('friend', Friend);
app.component('profile', Profile);
app.component('conversation', Conversation);
app.component('messages', Messages);
app.component('home', Home);

app.mount('#app');
