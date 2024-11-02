<template>
    <div>
        <header>
            <nav>
                <router-link to="/">Home</router-link>
                <router-link to="/login">Login</router-link>
                <router-link to="/register">Register</router-link>
                <router-link to="/diagrams">Diagrams</router-link>
            </nav>
        </header>

        <!-- This renders the component matching the current route -->
        <router-view></router-view>
    </div>
</template>
