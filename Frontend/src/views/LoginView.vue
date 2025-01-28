<template>
    <div class="loginBox">
        <div class="inner">
            <div class="signIn" v-if="signIn">
                <div class="top">
                    <div class="logo"></div>
                    <div class="title">Sign in</div>
                    <div class="subtitle">
                        Don't have an account?
                        <span class="subtitle-action" @click="signIn = !signIn">
                        Create Account
                        </span>
                    </div>
                </div>
                <form @submit.prevent="login">
                    <div class="form">
                        <input
                            required
                            aria-required="true"
                            aria-invalid="false"
                            aria-label="E-mail"
                            type="email"
                            pattern="^[\w\.-]+@[\w\.-]+\.\w+$"
                            class="w100"
                            :class="{ invalid: email.error }"
                            ref="email"
                            placeholder="Email"
                            autofocus
                            @blur="validateEmail"
                            @keydown="validateEmail"
                            v-model="email.value"
                        />

                        <input
                            required
                            aria-required="true"
                            type="password"
                            class="w100"
                            :class="{ invalid: password.error }"
                            placeholder="Password"
                            v-model="password.value"
                            @blur="validatePassword"
                            @keydown="validatePassword"
                        />
                    </div>

                    <input
                        type="submit"
                        value="Submit"
                        class="action"
                        :class="{ 'action-disabled': !loginValid }"
                    />
                </form>
            </div>

            <div class="register" v-else>
                <div class="top">
                    <div class="logo"></div>
                <div class="title">Create an Account</div>
                <div class="subtitle">
                    Already have an account?
                    <span class="subtitle-action" @click="signIn = !signIn">
                    Sign In
                    </span>
                </div>
                </div>

                <div class="form">
                    <input
                        type="text"
                        placeholder="First name"
                        autofocus
                        v-model="firstName"
                        class="w100"
                    />

                    <input
                        type="text"
                        placeholder="Last name"
                        v-model="lastName"
                        class="w100"
                    />

                    <input
                        type="text"
                        class="w100"
                        placeholder="Email"
                        v-model="email.value"
                    />
                    <input
                        type="password"
                        class="w100"
                        placeholder="Password"
                        v-model="password.value"
                    />
                </div>

                <button class="action" :class="{ 'action-disabled': !registerValid }" @click="register">
                    Create Account
                </button>
            </div>
        </div>
    </div>
</template>
  
<script>
    import axios from 'axios';

    export default
    {
        data()
        {
            return {
                emailRegex: /^[\w\.-]+@[\w\.-]+\.\w+$/,
                passwordRegex: /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/,
        
                firstName: "",
                lastName: "",
        
                password: {
                    value: "",
                    error: false
                },
        
                email: {
                    value: "",
                    error: false
                },
        
                signIn: true
            };
        },
    
        methods:
        {
            validateEmail(event)
            {
                if (this.email.value == "") this.email.error = true;
                else this.email.error = false;
            },
        
            validatePassword(event)
            {
                if (this.password.value == "") this.password.error = true;
                else this.password.error = false;
            },

            async register()
            {
                try
                {
                    const response = await axios.post(
                        'http://localhost:8000/api/users',
                        {
                            first_name: this.firstName,
                            last_name: this.lastName,
                            email: this.email.value,
                            password: this.password.value
                        },
                        {
                            withCredentials: true
                        }
                    );

                    if (response.data.success)
                    {
                        this.$router.push('/login');
                        this.signIn = true;
                        
                        alert('Account Created Successfully! Please Log in');
                    }
                    else
                    {
                        console.log(response);
                        const errorMessage = response.data.message;
                        
                        alert(errorMessage);
                    }
                }
                catch (error)
                {
                    const errorMessage = error.response?.data?.message || 'An error occurred during registration.';
                    
                    alert(errorMessage)
                }
            },

            async login()
            {
                try
                {
                    const response = await axios.post(
                        'http://localhost:8000/api/login',
                        {
                            email: this.email.value,
                            password: this.password.value
                        },
                        {
                            withCredentials: true
                        }
                    );

                    localStorage.setItem('session_key', response.data.session_key);

                    this.$router.push('/');
                }
                catch (error)
                {
                    // this.errorMessage = error.response.data.message || 'Login failed!';
                }
            },
        },
    
        mounted()
        {
            // this.$refs.email.focus();
        },
    
        computed:
        {
            validFirstName()
            {
                return this.firstName.length > 0;
            },
        
            validLastName()
            {
                return this.lastName.length > 0;
            },
        
            emailValid()
            {
                return this.emailRegex.test(this.email.value);
            },
        
            passwordValid()
            {
                return this.password.value.length > 0;
            },
        
            loginValid()
            {
                return this.emailValid && this.passwordValid;
            },
        
            registerValid()
            {
                return (
                this.emailValid &&
                this.passwordValid &&
                this.validFirstName &&
                this.validLastName
                );
            }
        }
    };
</script>
  
<!-- Use preprocessors via the lang attribute! e.g. <style lang="scss"> -->
<style scoped>
    input[type="text"],
    input[type="email"],

    input[type="password"]
    {
        border: 1px solid #aaa;
        height: 40px;
        padding: 10px;
        margin-top: 20px;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .invalid
    {
        border: 2px solid red !important;
    }

    .invalid::placeholder
    {
        color: red;
    }

    .errorMessage
    {
        color: red;
        margin: 10px;
        top: 5px;
    }

    .w100
    {
        width: 100%;
    }

    .logo
    {
        width: 300px;
        margin-bottom: 10px;
    }

    .action
    {
        height: 40px;
        text-transform: uppercase;
        border-radius: 25px;
        width: 100%;
        border: none;
        cursor: pointer;
        background: green;
        margin-top: 20px;
        color: #fff;
        font-size: 1.2rem;
        box-shadow: 1px 1px 2px 1px #ccc;
    }

    .action-disabled
    {
        color: #eee;
        background: #aaa;
        cursor: auto;
    }

    .top
    {
        display: flex;
        align-items: center;
        flex-direction: column;
        margin-bottom: 10px;
    }

    .title
    {
        width: 100%;
        font-size: 1.8rem;
        margin-bottom: 10px;
        text-align: center;
        color: #000;
    }

    .subtitle
    {
        color: green;
        font-weight: bold;
        cursor: pointer;
    }

    html
    {
        background-repeat: no-repeat;
        background: linear-gradient(
            to bottom,
            rgba(96, 108, 136, 1) 0%,
            rgba(63, 76, 107, 1) 100%
        );
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: sans-serif;
    }

    .loginBox
    {
        background: #fff;
        border-radius: 15px;
        max-width: 400px;
        padding: 25px 55px;
        animation: slideInTop 1s;
    }

    @keyframes slideInTop
    {
        from {
            opacity: 0;
            transform: translateY(-30%);
        }
        to {
            opacity: 100;
            transform: translateY(0%);
        }
    }

    @media screen and (min-width: 440px)
    {
        .loginBox {
            box-shadow: 1px 1px 2px 1px #ccc;
        }
    }

    @media screen and (max-width: 440px)
    {
        html {
            background: #fff;
            align-items: start;
            justify-content: start;
        }

        .loginBox {
            padding: 25px 25px;
            max-width: 100vw;
        }
    }
</style>