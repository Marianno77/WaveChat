<template>
    <div class="conversations">
        <h2>Twoje rozmowy</h2>
        <div class="ul" v-if="conversations.length > 0">
            <div class="li" v-for="conversation in conversations">
                <a :href="`/conversations/${ conversation.id }`">
                    <div class="user" :class="conversation.notification>0 ? 'red' : 'none'">
                        <div class="user-avatar">
                            <img :src="avatarUrl(conversation.user)" alt="avatar">
                        </div>
                        <div class="user-text">
                            <p>{{ conversation.user.name }}</p>
                        </div>
                        <div class="notification">
                            <div class="notification-img">
                                <img :src="'/images/notification.png'" alt="notification-image" width="30" :class="conversation.notification>0 ? 'bell-animate' : 'bell'">
                            </div>
                            <div class="notification-txt">
                                <h5>{{ conversation.notification }}</h5>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <p v-else>Nie masz jeszcze konwersacji</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            conversations: [],
        };
    },
    methods: {
        async fetchConversations() {
            try {
                const response = await axios.post('conversations/get-conversations')
                this.conversations = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        avatarUrl(user) {
            return user?.avatar
                ? `/storage/avatars/${user.avatar}`
                : '/images/user.png';
        },
    },
    mounted(){
        this.fetchConversations();
    }
}
</script>

<style scoped>

.conversations{
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

h2 {
    margin-bottom: 80px;
    font-size: 40px;
}

.ul {
    background-color: rgba(20, 10, 35, 0.55);
    backdrop-filter: blur(6px);
    border: 2px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    color: #f0eaff;
    width: 80%;
    min-width: 300px;
    max-width: 600px;
    padding: 30px 20px;
    list-style: none;
    margin-bottom: 80px;
    box-shadow: 0 0px 20px rgba(0, 0, 0, 0.2);
}

@media(max-width: 800px ) {
    .ul{
        padding: 0;
        padding-top: 30px;
    }

    .user {
        padding-top: 10px;
        max-width: 200px;
        min-height: 200px;
    }
}

.li {
    display: flex;
    align-items: center;
    justify-content: center;
}

.user {
    font-size: 20px;
    width: 300px;
    height: 150px;
    display:flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 20px;
    background-color: rgba(70, 50, 100, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 15px;
    margin-bottom: 30px;
    color: #eae6f7;
    cursor: pointer;
    transition: transform 0.3s;
    backdrop-filter: blur(8px);
    flex-wrap: wrap;
}

.user:hover {
    transform: scale(1.05);
}

a {
    text-decoration: none;
}

.user-avatar {
    width: 100px;
    height: 100px;
    border: 2px solid #fff;
    border-radius: 50%;
    overflow: hidden;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.notification { 
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-left: 20px;
    font-size: 25px;
}

.red {
    border: 2px solid purple;
}

.none {
    border: 2px solid rgba(255, 255, 255, 0.08);
}

.bell-animate {
    animation: shake 2s ease-in-out infinite;
    transform-origin: top center;
}

.bell {
    animation: none;
}

@keyframes shake {
  0%   { transform: rotate(0deg); }
  10%  { transform: rotate(-15deg); }
  20%  { transform: rotate(15deg); }
  30%  { transform: rotate(-10deg); }
  40%  { transform: rotate(10deg); }
  50%  { transform: rotate(0deg); }
  100% { transform: rotate(0deg); } 
}

</style>
