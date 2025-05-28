<template>
    <h2>Rejestracja</h2>
    <div class="register-form">
        <form @submit.prevent="registerUser">
            <section>
                <label>Nazwa</label>
                <input v-model="name" type="text" id="name" required />
            </section>

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
                    required
                />
            </section>

            <section>
                <label>Powtórz hasło</label>
                <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="password_confirmation"
                    id="password_confirmation"
                    required
                />
            </section>

            <button class="showPasswordClass" type="button" @click="togglePasswordVisibility">
                <img :src="showPassword ? '/images/eyeclose.png' : '/images/eye.png'" alt="Pokaż/Ukryj" width="30px">
            </button>

            <section>
                <button type="submit">Zarejestruj się</button>
            </section>

            <div v-if="error" class="error-message">
                <p>{{ error }}</p>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    data() {
        return{
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        error: null,
        showPassword: false,
        };
    },
    methods: {
        async registerUser() {
            try {
                const response = await axios.post('/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });

                if (response.status === 200) {
                    window.location.href ='/';
                }
            } catch (error) {
                if (error.response && error.response.data) {
                    this.error = error.response.data.message || 'Błąd rejestracji';
                }
            }
        },

        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
        }
    },
};
</script>

<style scoped>
h2 {
    margin-bottom: 80px;
    font-size: 40px;
}

.register-form {
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

.register-form form {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    justify-items:  center;
}

.register-form form section {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    gap: 10px;
    margin-bottom: 40px;
    font-size: 20px;
}

.register-form form section label {
    font-size: 22px;
}

.register-form form section input {
    width: 300px;
    height: 45px;
    border-radius: 30px;
    border: 2px solid transparent;
    padding-left: 20px;
    font-size: 18px;
}

.register-form form section input:focus {
    outline: none;
    border: 2px solid #9834db;
}

.register-form form section button {
    width: 200px;
    height: 50px;
    border: 2px solid #000;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.3s;
    margin-top: 40px;
}
.showPasswordClass:hover,
.register-form form section button:hover {
    transform: scale(1.05);
    border: 2px solid #9834db;
}

.showPasswordClass {
    margin-top: 0;
    margin-bottom: 20px;
    padding: 5px 10px 5px 10px;
    border: 2px solid #000;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.3s;
}

</style>
