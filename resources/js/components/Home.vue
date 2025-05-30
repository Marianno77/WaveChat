<template>
    <div class="home-holder">
        <div class="text">
            <h1>
                👋 Witamy w WaveChat
            </h1>
            <p>
                WaveChat to lekki i szybki komunikator tekstowy stworzony z myślą o prostocie i wygodzie. Bez rozpraszaczy, bez reklam – tylko Ty i rozmowa.
            </p>
            <h3>
                ✨ Co oferuje WaveChat?
            </h3>
            <ul>
                <li>
                    Czat w czasie rzeczywistym – rozmawiaj z innymi natychmiastowo.
                </li>
                <li>
                    Nowoczesny i przejrzysty interfejs – skup się na wiadomościach, nie na dodatkach.
                </li>
                <li>
                    Łatwa obsługa – uruchom i korzystaj bez zbędnej konfiguracji.
                </li>
                <li>
                    Lekkość i szybkość działania – idealna dla osób ceniących sobie sprawne i proste narzędzia.
                </li>
            </ul>
        </div>
        <div class="btn">
            <a :href="registerUrl">
                <button>
                    <h5>{{ displayedText }} <span v-if="showCursor">|</span></h5>
                </button>
            </a>
        </div>
    </div>
</template>

<script>
import { onMounted, ref } from 'vue';

export default {
  setup() {
    const fullText = 'Zacznij pisać...'
    const displayedText = ref('')
    const showCursor = ref(true)
    const registerUrl = '/register'

    onMounted(() => {
      let index = 0
      const interval = setInterval(() => {
        if (index < fullText.length) {
          displayedText.value += fullText[index]
          index++
        } else {
          clearInterval(interval)
        }
      }, 100)

      setInterval(() => {
        showCursor.value = !showCursor.value
      }, 500)
    })

    return {
      fullText,
      displayedText,
      showCursor,
      registerUrl
    }
  }
}
</script>

<style scoped>
.text {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 30px 80px;
    font-size: 20px;
    gap: 20px;
}

.text p {
    padding-left: 100px;
    padding-right: 100px;
}

ul {
    line-height: 50px;
}

.btn {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.btn button {
    width: 250px;
    height: 100px;
    font-size: 20px;
    border: none;
    border-radius: 30px;
    -webkit-box-shadow: 8px 8px 24px 5px rgb(87, 22, 131);
    -moz-box-shadow: 8px 8px 24px 5px rgb(87, 22, 131);
    box-shadow: 8px 8px 24px 5px rgb(87, 22, 131);
    background-color: #4E3A65;
    color: #fff;
    cursor: pointer;
    transition: transform 0.3s;
}

.btn button:hover {
    transform: scale(1.05);
}
</style>