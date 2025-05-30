<template>
    <div class="buttons">
        <button @click="toggleFriends">
            <h5>Lista znajomych</h5>
        </button>
        <button @click="toggleInvitations">
             <h5>Zaproszenia</h5>
        </button>
        <button @click="toggleBlockList">
            <h5>Zablokowani użytkownicy</h5>
        </button>
    </div>
    <div class="main-holder">

    <div :class="{'d-none': !showFriends}">
        <div class="friends-holder">
            <h3>Twoi znajomi</h3>
            <div class="add-btn">
                    <button @click="toggleUsers">
                        <img :src="'/images/add.png'" alt="plus" width="30px">
                        <p>Dodaj znajomych</p>
                    </button>
            </div>
            <div :class="{'d-none': !showUsers}">
                <div class="search">
                    <input type="search" v-model="searchQuery" placeholder="Szukaj nowych znajomych">
                </div>
                <div class="users-list">
                    <div class="ul" v-if="filteredUsers.length > 0">
                        <div class="li" v-for="(user, index) in filteredUsers" :key="index">
                            <div class="friend-box">
                                <div class="friend">
                                    <div class="friend-avatar">
                                        <img :src="avatarUrl(user)">
                                    </div>
                                    <div class="friend-name">
                                        <p>{{ user.name }}</p>
                                    </div>
                                </div>
                                <button class="inv-sender-btn" @click="addFriend(user)">Wyślij zaproszenie</button>
                            </div>
                        </div>
                    </div>
                    <p v-else>Brak użytkowników pasujących do wyszukiwania.</p>
                </div>
            </div>
            <hr>
            <div class="friends-list">
                <div class="ul" v-if="friends.length > 0">
                    <div class="li" v-for="(friend,index) in friends" :key="index">
                        <div class="friend-box">
                            <a href="" @click.prevent="openConversation(friend.id)">
                                <div class="friend">
                                    <div class="friend-avatar">
                                        <img :src="avatarUrl(friend)" width="50">
                                    </div>
                                    <div class="friend-name">
                                        <p>{{ friend.name }}</p>
                                    </div>
                                </div>
                            </a>
                            <button class="hamburger-btn" @click="toggleOptions(friend.pivot.id)"><img :src="showOptions !== friend.pivot.id ? '/images/hamburger.png' : '/images/close.png'" width="30"></button>
                            <div class="friend-more-box" :class="{'d-none': showOptions !== friend.pivot.id}">
                                <button @click="deleteFriends(friend.pivot.id)">Usuń</button>
                                <button @click="block(friend.pivot.id)">Zablokuj</button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else>Nie masz jeszcze przyjaciół.</p>
            </div>
        </div>
    </div>

    <div :class="{'d-none': !showInvitations}">
        <div class="invitations-holder">
            <h3>Twoje zaproszenia</h3>
            <div class="friends-status">
                <div class="ul" v-if="invitations.length > 0">
                    <div class="li" v-for="(invitation, index) in invitations" :key="index">
                        <div class="friend-box">
                            <div class="friend">
                                <div class="friend-avatar">
                                    <img :src="avatarUrl(invitation)" alt="avatar">
                                </div>
                                <div class="friend-name">
                                    <p>{{ invitation.name }}</p>
                                </div>
                            </div>
                            <div class="status-btns">
                                <button @click="acceptFriend(invitation.friendship_id)">Akceptuj</button>
                                <button @click="deleteFriends(invitation.friendship_id)">Usuń</button>
                                <button @click="block(invitation.friendship_id)">Blokuj</button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else>Nie masz żadnych zaproszeń.</p>
            </div>
        </div>
    </div>

    <div :class="{'d-none': !showBlockList}">
        <div class="block-list-holder">
            <h3>Lista zablokowanych użytkowników</h3>
            <div class="block-list">
                <div class="ul" v-if="blocklist.length > 0">
                    <div class="li" v-for="(block, index) in blocklist" :key="index">
                        <div class="friend-box">
                            <div class="friend">
                                <div class="friend-avatar">
                                    <img :src="avatarUrl(block)" alt="avatar">
                                </div>
                                <div class="friend-name">
                                    <p>{{ block.name }}</p>
                                </div>
                            </div>
                            <button class="unblock" @click="deleteFriends(block.friendship_id)">Odblokuj <br> (Usuwa znajomość)</button>
                        </div>
                    </div>
                </div>
                <p v-else>Nie masz żadnych blokowanych użytkowników.</p>
            </div>
        </div>
    </div>

    </div>
</template>

<script>

export default {
    data() {
        return {
            friends: [],
            users: [],
            searchQuery: "",
            invitations: [],
            blocklist: [],
            showUsers: false,
            showOptions: false,
            showFriends: true,
            showInvitations: false,
            showBlockList: false,
        }
    },
    computed: {
        filteredUsers() {
            if (!this.searchQuery.trim()) {
                return [];
            }
            return this.users.filter(user => user.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        }
    },
    methods: {
        async fetchFriends() {
            try {
                const response = await axios.get('/friends/get-friends');
                this.friends = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        async fetchUsers() {
          try {
              const response = await axios.get('friends/all-users');
              this.users = response.data;
          } catch (error) {
              console.error(error);
          }
        },

        async addFriend(user) {
            try {
                const response = await axios.post('/friends/add', {friend_id: user.id});
                if (response.status == 200 && response.data.success) {
                    this.users = this.users.filter(u => u.id !== user.id);
                    this.$toast.success('Zaproszenie zostało wysłane.')
                } else {
                    this.$toast.error('Wystąpił błąd serwera.')
                }
            } catch (error) {
                console.error(error);

                this.$toast.error('Błąd połączenia z serwerem.');
            }
        },

        async fetchInvitations() {
          try {
              const response = await axios.get('/friends/get-invitations');
              this.invitations = response.data;
          }  catch (error) {
              console.error(error);
          }
        },

        async fetchBlockList(){
          try {
              const response = await axios.get('/friends/get-block-list');
              this.blocklist = response.data.users || response.data;
          }  catch (error) {
              console.error(error);
          }
        },

        async acceptFriend(friend_id) {
          try {
              const response = await axios.post('/friends/accept', {friend_id: friend_id});
              if (response.data.success) {
                  this.invitations = this.invitations.filter(invitation => invitation.friendship_id !== friend_id);
                  this.fetchFriends();
                  this.$toast.success(response.data.message);
              } else {
                  this.$toast.error(response.data.message);
              }
          } catch (error) {
              console.error(error);
              this.$toast.error('Błąd połączenia z serwerem.');
          }
        },

        async block(friendshipId) {
            try {
                const response = await axios.post('/friends/block', {friendship_id: friendshipId});
                if (response.data.success) {
                    this.invitations = this.invitations.filter(invitation => invitation.friendship_id !== friendshipId);
                    this.friends = this.friends.filter(friend => friend.pivot.id !== friendshipId);
                    this.$toast.success(response.data.message);
                    await this.fetchBlockList();
                    await this.fetchFriends();
                    //window.location.reload();
                } else {
                    this.$toast.error(response.data.message);
                }
            } catch (error) {
                console.error(error);
                this.$toast.error('Błąd połączenia z serwerem.');
            }
        },

        async deleteFriends(friendshipId) {
            try {
                const response = await axios.post('/friends/delete', {friendship_id: friendshipId});
                if (response.data.success) {
                    this.invitations = this.invitations.filter(invitation => invitation.friendship_id !== friendshipId);
                    this.friends = this.friends.filter(friend => friend.pivot.id !== friendshipId);
                    this.blocklist = this.blocklist.filter(blocklist => blocklist.friendship_id !== friendshipId);
                    this.$toast.success(response.data.message);
                } else {
                    this.$toast.error(response.data.message);
                }
            } catch (error) {
                console.error(error);
                this.$toast.error('Błąd połączenia z serwerem.');
            }
        },

        async openConversation(friendId) {
            try {
                const response = await axios.post('/api/conversations/check-or-create', {user_id: friendId});
                const conversation = response.data;

                window.location.href = `/conversations/${ conversation.id }`;
            } catch (error) {
                console.error(error);
            }
        },

        avatarUrl(user) {
            return user?.avatar
                ? `/storage/avatars/${user.avatar}`
                : '/images/user.png';
        },

        toggleUsers() {
            this.showUsers = !this.showUsers;
            if(this.showUsers) {
                this.fetchUsers();
            }
        },

        toggleOptions(id) {
            this.showOptions = this.showOptions === id ? null : id;
        },

        toggleFriends() {
            this.showFriends = true;
            this.showInvitations = false;
            this.showBlockList = false;
            if (this.showFriends) {
                this.fetchFriends();
            }
        },

        toggleInvitations() {
            this.showInvitations = true;
            this.showFriends = false;
            this.showBlockList = false;
            if (this.showInvitations) {
                this.fetchInvitations();
            }
        },

        toggleBlockList() {
            this.showBlockList = true;
            this.showFriends = false;
            this.showInvitations = false;
            if (this.showBlockList) {
                this.fetchBlockList();
            }
        }
    },
    mounted() {
        this.fetchFriends();
    }
}
</script>

<style scoped>
.d-none {
    display: none;
    width: 100%;
}

.showUsers, .showFriends, .showInvitations, .showBlockList, .showOptions {
    display: block;
}


.showFriends , .showInvitations, .showBlockList {
    min-width: 100%;
}

.buttons {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.unblock,
.status-btns button,
.add-btn button,
.buttons button {
    width: 200px;
    height: 50px;
    border: 2px solid #000;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.3s;
    color: #FAF9FF;
    background-color: #4E3A65;
}


.add-btn,
.add-btn button {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.buttons button h5 {
    margin: 0;
    padding: 0;
}

.unblock:hover,
.status-btns button:hover,
.inv-sender-btn:hover,
.friend-more-box button:hover,
.add-btn button:hover,
.buttons button:hover {
    transform: scale(1.05);
    background-color: #685D84;
}

.block-list-holder h3,
.invitations-holder h3,
.friends-holder h3 {
    text-align: center;
    padding-left: 20px;
    padding-right: 20px;
    font-size: 30px;
}

.background,
.block-list-holder .ul,
.invitations-holder .ul,
.friends-holder .ul {
    width: 100%;
    list-style: none;
}

.friends-status,
.friends-list {
    margin-top: 40px;
    font-size: 20px;
}

.friend-box {
    font-size: 20px;
    width: 250px;
    min-height: 150px;
    display:flex;
    flex-direction: row;
    justify-content: space-around;

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
    padding-left: 10px;
    padding-right: 10px;
}

.friend-box a {
    color: #fff;
    text-decoration: none;
}

.friend-box:hover {
    transform: scale(1.05);
}

.friend {
    margin-top: 15px;
    margin-bottom: 15px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 15px;
}

.friend-avatar {
    width: 100px;
    height: 100px;
    border: 2px solid #fff;
    border-radius: 50%;
    overflow: hidden;
}

.friend-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.friend-more-box {
    padding: 10px;
}

.inv-sender-btn {
    width: 200px;
    height: 50px;
    border: 2px solid #000;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.3s;
    color: #FAF9FF;
    background-color: #4E3A65;
    margin-bottom: 30px;
}

.friend-more-box button {
    width: 100px;
    height: 50px;
    border: 2px solid #000;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.3s;
    color: #FAF9FF;
    background-color: #4E3A65;
    margin: 5px;
}

.hamburger-btn {
    background-color: transparent;
    border: 0;
    cursor: pointer;
    transition: transform 0.3s;
}

.hamburger-btn:hover {
    transform: scale(1.2);
}

.search {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.search input {
    width: 300px;
    height: 50px;
    border-radius: 30px;
    padding-left: 20px;
    font-size: 18px;
    border: 2px solid transparent;
}

.search input:focus {
    border: 2px solid #9834db;
    outline: none;
}

.search input::-webkit-search-cancel-button{
    position:relative;
    right:20px;    
    font-size: 25px;
    cursor: pointer;
}

.users-list {
    font-size: 18px;
    margin-top: 20px;
}

.status-btns {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
    gap: 10px;
}

.unblock {
    padding: 10px;
    height: auto !important;
    margin-bottom: 20px;
}

.friends-holder hr {
    margin-top: 40px;
    width: 90%;
}

.main-holder {
    margin-top: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-width: 300px;
    max-width: 600px;
    width: 80%;
    background-color: rgba(20, 10, 35, 0.55);
    backdrop-filter: blur(6px);
    border: 2px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

@media (max-width: 480px) {
    .friends-holder hr {
        width: 250px;
    }
}

.li {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.friends-list p,
.friends-status p,
.block-list p {
    text-align: center;
    margin-top: 20px;
}

</style>
