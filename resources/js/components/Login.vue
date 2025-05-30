<template>
    <h2>Logowanie</h2>
    <div class="login-form">
        <form @submit.prevent="loginUser">
            <section>
                <label>Email</label>
                <input v-model="email" type="email" id="email" required />
            </section>

            <section>
                <label>Hasło</label>
                <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="password"
                    id="password"
                />

                <button class="showPasswordClass" type="button" @click="togglePasswordVisibility">
                    <img :src="showPassword ? '/images/eyeclose.png' : '/images/eye.png'" alt="Pokaż/Ukryj" width="30px">
                </button>
            </section>

            <section>
                <button type="submit">Zaloguj się</button>
            </section>

            <div v-if="error" class="error-message">
                <p>{{ error }}</p>
            </div>
        </form>
    </div>
</template>

<script>
import  axios from 'axios'
export default {
    data() {
        return {
            email: '',
            password: '',
            error: null,
            showPassword: false,
        };
    },
    methods: {
        async loginUser() {
            this.error = null;
            try {
                await axios.get('/sanctum/csrf-cookie', {
                    withCredentials: true,
                });

                const response = await axios.post('/login', {
                    email: this.email,
                    password: this.password,
                },{
                    withCredentials: true,
                });

                if (response.status === 200 || response.status === 204) {
                    window.location.href = '/';
                }
            } catch (error) {
                if (error.response && error.response.data) {
                    this.error = error.response.data.message || 'Błąd logowania';
                } else {
                    this.error = 'Nieoczekiwany błąd';
                }
            }
        },

        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
        },
    },
};
</script>

<style scoped>
h2 {
    margin-bottom: 80px;
    font-size: 40px;
}

.login-form {
    text-align: center;
    background-color: rgba(20, 10, 35, 0.55);
    backdrop-filter: blur(6px);
    border: 2px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    color: #f0eaff;
    width: 60%;
    min-width: 200px;
    max-width: 500px;
    padding: 60px 40px;
    list-style: none;
    margin-bottom: 80px;
    box-shadow: 0 0px 20px rgba(0, 0, 0, 0.2);
}

.login-form form {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    justify-items:  center;
}

.login-form form section {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    gap: 10px;
    margin-bottom: 40px;
    font-size: 20px;
}

.login-form form section label {
    font-size: 22px;
}

.login-form form section input {
    width: 90%;
    max-width: 300px;
    height: 45px;
    border-radius: 30px;
    padding-left: 20px;
    font-size: 18px;
    border: 2px solid transparent;
}

.login-form form section input:focus {
    outline: none;
    border: 2px solid #9834db;
}

.login-form form section button:not(.showPasswordClass) {
    width: 200px;
    height: 50px;
    border: 2px solid #000;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.3s;
    margin-top: 40px;
}
.login-form form section button:hover {
    transform: scale(1.05);
    border: 2px solid #9834db;
}

.showPasswordClass {
    margin-top: 15px;
    padding: 5px 10px 5px 10px;
    border: 2px solid #000;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.3s;
}
</style>
