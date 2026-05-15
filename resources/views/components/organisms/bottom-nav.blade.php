<nav class="bottom-nav">
    <x-molecules.bottom-nav-item icon="reports" label="Dashboard" href="{{route('affiliator.index')}}" :active="request()->routeIs('affiliator.index')" />
    <x-molecules.bottom-nav-item icon="revenue" label="Katalog" href="{{route('affiliator.catalog.index')}}" :active="request()->routeIs('affiliator.catalog.index')" />
    <x-molecules.bottom-nav-item icon="journal" label="Tugas" href="{{route('affiliator.task.index')}}" :active="request()->routeIs('affiliator.task.index')" />
    <x-molecules.bottom-nav-item icon="trend-up" label="Peringkat" href="{{route('affiliator.leaderboard.index')}}" :active="request()->routeIs('affiliator.leaderboard.index')" />
    <x-molecules.bottom-nav-item icon="profile" label="Profil" href="{{route('affiliator.profile.index')}}" :active="request()->routeIs('affiliator.profile.index')" />
</nav>