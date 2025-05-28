<template>
    <div class="user-panel">
        <h2>Panel użytkownika</h2>
        <div v-if="user">
            <div class="img" @click="triggerFileInput">
                <img class="base-img" :src="avatarUrl" alt="Avatar" >
                <img class="overlay-img" src="/public/images/pen.png" alt="Edit">
                <input type="file" ref="fileInput" @change="handleFileChange" accept="image/*" style="display: none">
            </div>
            <div class="user-info">
                <h5>Nazwa: {{ user.name }}</h5>
                <h5>E-mail: {{ user.email }}</h5>
            </div>
        </div>
        <div v-else>
            <h5>Ładowanie danych...</h5>
        </div>
        <div>
            <form @submit.prevent="logout">
                <button type="submit" class="delete">Wyloguj się</button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";
export default {
    data() {
        return {
            user: null,
        };
    },
    methods: {
        async fetchUser() {
            try {
                await axios.get('/sanctum/csrf-cookie');

                const response = await axios.get('/api/user',{
                    withCredentials: true,
                });
                this.user = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async logout() {
            try {
                await axios.post('/logout',{},{
                    withCredentials: true,
                });
                window.location.href = '/';
            } catch (error) {
                console.error(error);
            }
        },

        triggerFileInput() {
            this.$refs.fileInput.click();
        },

        async handleFileChange(event) {
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('avatar', file);

          try {
              await axios.post('/api/user/avatar', formData, {
                  headers: {
                      'content-type': 'multipart/form-data',
                  },
                  withCredentials: true,
              });
              this.fetchUser();
          } catch (error) {
              console.error(error);
          }
        },
    },
    computed: {
        avatarUrl() {
            return this.user?.avatar
                ? `/storage/avatars/${this.user.avatar}`
                : '/images/user.png';
        }
    },
    mounted() {
        this.fetchUser();
    },
};
</script>

<style>
.user-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 25px;

    width: 80%;
    min-width: 300px;
    max-width: 600px;
    background-color: rgba(20, 10, 35, 0.55);
    backdrop-filter: blur(6px);
    border: 2px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

.user-panel h2 {
    margin-bottom: 80px;
}

.user-panel button {
    width: 200px;
    height: 50px;
    border: 1px solid #fff;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.3s;
    margin-top: 80px;
    background-color: #b80924;
    color: #fff;
    margin-bottom: 80px;
}

.user-panel button:hover {
    transform: scale(1.05);
}

.img {
    position: relative;
    width: 150px;
    height: 150px;
    overflow: hidden;
    margin: 0 auto;
    border: 2px solid #fff;
    border-radius: 50%;
    cursor: pointer;
}

.base-img, .overlay-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.4s ease-in-out;
}

.overlay-img {
    opacity: 0;
    pointer-events: none;
}

.img:hover .overlay-img {
    opacity: 1;
}

.img:hover .base-img {
    opacity: 0.3;
}

.user-info {
    margin-top: 90px;
}

@media (max-width: 370px) {
    .user-panel {
        padding: 0;
        padding-top: 20px;
    }
}

</style>

