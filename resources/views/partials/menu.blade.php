<div class="top">
    <div class="logo">

    </div>
    <div class="name">
        <h1><a href="{{ route('home') }}"> WaveChat </a></h1>
    </div>
</div>
<div class="bottom">
    <div class="menu">
        @auth
            <div class="menu-chat-btn"><a href="{{ route('conversations') }}"><button> Czat </button></a></div>
            <div class="menu-friend-btn"><a href="{{ route('friends') }}"><button> Znajomi </button></a></div>
            <div class="menu-profile-btn"><a href="{{ route('profile') }}"><button> Profil </button></a></div>
        @endauth

        @guest
            <div class="menu-login-btn"><a href="{{ route('login') }}"><button> Logowanie </button></a></div>
            <div class="menu-register-btn"><a href="{{ route('register') }}"><button> Rejestracja </button></a></div>
        @endguest
    </div>
</div>