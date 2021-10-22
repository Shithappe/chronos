<template>
    <input id="tab-1" type="radio" name="tab" checked><label for="tab-1">Sign In</label>
    <input id="tab-2" type="radio" name="tab" ><label for="tab-2">Sign Up</label>

    <form class="login" @submit="login">
        <input type="text" class="input" placeholder="E-mail" v-model="username">
        <input type="password" class="input" placeholder="Password" data-type="password" v-model="password">
        <input type="submit" class="button" value="Sign In">
    </form>

    <form class="register" @submit="register">
        <input  type="text" class="input" placeholder="Name" v-model="username">
        <input  type="password" class="input" placeholder="Password" data-type="password" v-model="password">

        <input type="password" class="input" placeholder="Repeat Password" data-type="password">
        <input type="text" class="input" placeholder="E-mail" v-model="email">
        <input type="submit" class="button" value="Sign Up">
      </form>

      <input id="tab-3" type="radio" name="tab" ><label for="tab-3">Forgot password</label>
      <form class="forgot_password" @submit="forgot_password">
           <input type="text" class="input" placeholder="E-mail" v-model="email">
           <input type="submit" class="button" value="Send email">
      </form>

        <div class="error">{{error}}</div>
</template>

<script>
import axios from "axios";
import Cookies from 'js-cookie';

export default {
    name: 'Auth',
    data() {
        return {
            username: '',
            email: '',
            password: '',
            error: ''
        }
    },

    methods: {
        async login(event) {
          event.preventDefault()
            axios.post('http://127.0.0.1:8000/api/login', {
                'email': this.username,
                'password': this.password
            })
            .then((response) => {
                console.log(response);
                Cookies.set('token', response.data.token);
                this.$router.push({path: '/'});
            }).catch((error) => {
                console.log(error.response.statusText);
                this.error = error.response.statusText;
            })
        },

        async register(event) {
            event.preventDefault()
            axios.post('http://127.0.0.1:8000/api/register', {
                'name':this.username,
                'email':this.email,
                'password':this.password,
                })
                .then((response) => {
                    console.log(response);
                })
                .catch((error) => {
                    this.error = error.response.statusText;
                })
        },

        async forgot_password(event){
          event.preventDefault();
          axios.post('http://127.0.0.1:8000/api/forgot_password', {
            'email': this.email,
          })
          .then((response) => {
              alert('Check your email');
              window.location.reload();
              console.log(response);
          })
        }
    }


}
</script>

<style scoped>

.login, .register, .forgot_password{
    display: none;
    flex-direction: column;
}

#tab-1:checked ~ .login{
    display: flex;
}

#tab-2:checked ~ .register{
    display: flex;
}

#tab-3:checked ~ .forgot_password{
  display: flex;
}

input[type="text"], input[type="email"], input[type="password"], input[type="submit"]{
  width: 25em;
  text-align: center;
  margin: 1em auto;
}

</style>