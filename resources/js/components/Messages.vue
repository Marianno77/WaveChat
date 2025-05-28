<template>

    <div class="container-message">
        <h2>{{ friendName }} ({{ messages.length }} wiadomości) </h2>
        <hr style="margin-bottom: 40px">
        <div class="all-messages" v-if="messages.length > 0" ref="messageContainer">
            <div class="message" v-for="message in messages">
                <template v-if="message.sender_id !== userId">
                    <div class="message-holder left">
                        <div class="message-user">
                            <div class="message-user-avatar">
                                <img :src="avatarUrl(message.user)" alt="avatar">
                            </div>
                            <div class="message-user-name">
                                <p>{{ message.user?.name || 'Nieznany użytkownik' }}</p>
                            </div>
                        </div>

                        <div class="message-content">
                            <p>{{ message.content }}</p>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="message-holder right">
                        <div class="message-content">
                            <p>{{ message.content }}</p>
                        </div>
                        <div class="message-user">
                            <div class="message-user-avatar">
                                <img :src="avatarUrl(message.user)" alt="avatar">
                            </div>
                            <div class="message-user-name">
                                <p>{{ message.user?.name || 'Nieznany użytkownik' }}</p>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
        <div v-else>
            <p>Brak</p>
        </div>
        <hr style="margin-top: 40px">
        <div class="input">
            <form @submit.prevent="sendMessage">

                <input type="text" v-model="message" placeholder="Wpisz wiadomość">
                <button>
                    <img :src="'/images/send.png'" width="30px">
                </button>
            </form>
            <button class="showEmojiButton" @click="toggleEmoji">😎</button>
        </div>


            <transition name="fade-slide">
                <div v-if="showEmoji" class="input-more">
                    <div class="emojis">
                        <button v-for="emoji in emojiArray" @click="addEmoji(emoji)">
                            <span>{{ emoji }}</span>
                        </button>
                    </div>
                </div>
            </transition>

    </div>
</template>
<script>
import axios from 'axios';


export default {
    data() {
        return{
            messages: [],
            message: '',
            showEmoji: false,
            emojiArray:
            [
                '😀',
                '😎',
                '👍',
                '❤️',
                '😉',
                '🤔',
                '🎉',
                '🚀',
                '🔥',
                '👏',
                '🤩',
                '😘',
                '😍',
                '🥰',
                '🤪',
                '😇',
                '😁',
                '😂',
                '😋',
                '😜',
            ],
        }
    },
    props: {
        conversation: Object,
        friendName: String,
        userId: Number,
    },
    methods: {
      async fetchMessages(conversationId) {
          try {
              const response = await axios.post(`conversations/${conversationId}/get-messages`);
              this.messages = response.data;
              this.$nextTick(() => {
                  this.scrollToBottom();
              })
          } catch (error) {
              console.error(error);
          }
      },

      async sendMessage() {
          if (!this.message.trim()) return;

          try {
              const response = await axios.post(`conversations/${this.conversation.id}/send-message`, {
                  content: this.message,
              });

              this.messages.push(response.data);
              this.message = '';
              this.$nextTick(() => {
                  this.scrollToBottom();
              });
          } catch (error) {
              console.error(error);
          } finally {
              this.fetchMessages(this.conversation.id);
          }
      },

      async addEmoji(emoji) {
          this.message += emoji
      },

      async changeStatus() {
        try{
            await axios.get(`conversations/${this.conversation.id}/change-status`);
        } catch(error) {
            console.error(error);
        }
      },

      toggleEmoji() {
          this.showEmoji = !this.showEmoji;
          console.log('toggleEmoji', this.showEmoji);
      },

      avatarUrl(user) {
          return user?.avatar
              ? `/storage/avatars/${user.avatar}`
              : '/images/user.png';
      },

      scrollToBottom() {
          const container = this.$refs.messageContainer
          if (container) {
              container.scrollTop = container.scrollHeight;
          }
      },

    },
    mounted(){
        console.log(this.showEmoji);
        this.fetchMessages(this.conversation.id);
        this.changeStatus();

        window.Echo.private('conversation.' + this.conversation.id)
            .listen('MessageSent', (e) => {
                this.messages.push(e.message);
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            });
    },
    
    beforeUnmount(){
        this.changeStatus();
    }
}
</script>

<style scoped>
.d-none {
    display: none;
}

.showEmoji {
    display: flex;
}

h2 {
    text-align: center;
    word-spacing: 5px;
}

.all-messages {
    max-height: 1000px;
    overflow-y: auto;
    overflow-x: hidden;
}

.container-message {
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

.message-holder,
.message {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin-top: 20px;
    width: 100%;
    padding-left: 10px;
    padding-right: 10px;
    gap: 10px;
}

.left {
    justify-content: left;
    text-align: left;
}

.right {
    justify-content: right;
    text-align: right;
}

.message-user {
    width: 30%;
    display: flex;
    flex-direction: column;
    justify-content: start;
    align-items: center;
    justify-items: center;
    padding-top: 20px;
}

.message-content {
    word-wrap: break-word;
    white-space: normal;
    height: auto;
    width: 80%;
    background-color: rgba(70, 50, 100, 0.6);
    max-width: 200px;
    min-width: 100px;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 15px;
    color: #eae6f7;
    backdrop-filter: blur(8px);
    font-size: 18px;
}

.message-content p {
    padding: 0 10px;
}

.message-user-avatar {
    width: 80px;
    height: 80px;
    border: 2px solid #fff;
    border-radius: 50%;
    overflow: hidden;
}

.message-user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.input {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    text-align: center;
    margin-top: 40px;
    flex-wrap: wrap;
}

.input form input {
    min-height: 50px;
    height: auto;
    width: 250px;
    border: 0;
    padding-left: 15px;
    padding-right: 15px;
    font-size: 16px;
    border-radius: 20px;
    margin: 5px;
}

.input form input:focus {
    outline: none;
}

.input-more {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    margin-bottom: 20px;
}

.emojis {
    width: 50%;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.emojis button {
    flex: 0 1 calc(20% - 10px);
    box-sizing: border-box;
    height: 50px;
    font-size: 20px;
    text-align: center;
    cursor: pointer;
    border-radius: 10px;
    border: 0;
    transition: transform 0.3s;
}

.emojis button:hover {
    transform: scale(1.05);
}

.fade-slide-enter-active, .fade-slide-leave-active {
    transition: max-height 0.4s ease, opacity 0.4s ease;
    overflow: hidden;
}
.fade-slide-enter-from, .fade-slide-leave-to {
    max-height: 0;
    opacity: 0;
}
.fade-slide-enter-to, .fade-slide-leave-from {
    max-height: 300px; /* dopasuj do swojego emoji boxa */
    opacity: 1;
}

.input form {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.showEmojiButton,
.input form button {
    height: 50px;
    width: 50px;
    border: 0;
    border-radius: 10px;
    cursor: pointer;
    font-size: 20px;
}

@media(max-width: 400px) {
    .input {
        margin-bottom: 20px;
    }

    .input form {
        padding-left: 10px;
        padding-right: 10px;
    }

    .input form input {
        width: 200px;
    }

    .container-message {
        padding: 0;
        padding-top: 20px;
    }

    .message-content {
        width: 50%;
    }

    .right {
        margin-right: 10px;
    }
}

</style>
