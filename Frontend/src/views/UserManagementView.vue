<template>
    <div id="app">
        <h1>User Management</h1>
        <button class="action-mini" @click="openRegisterModal">Register New User</button>

        <DataTable :data="users" :columns="columns" class="display">
            <template #actions="props">
                <button class="action-mini" @click="viewUser(props.rowData.id)">View</button>
                <button class="action-mini" style="margin-left: 2%;" @click="editUser(props.rowData)">Edit</button>
                <button class="action-mini" style="margin-left: 2%;" @click="deleteUser(props.rowData.id)">Delete</button>
            </template>
        </DataTable>

        <!-- View User Modal -->
        <div v-if="isViewModalOpen" class="modal">
            <div class="modal-content">
                <span class="close" @click="closeViewModal">&times;</span>
                <div class="update">
                    <div class="top">
                        <div class="logo"></div>
                        <div class="title">User Details</div>
                    </div>

                    <p class="w100" style="color: black;">
                        <strong>ID:</strong> 
                        {{ selectedUser.id }}
                    </p>
                    <p class="w100" style="color: black;">
                        <strong>First Name:</strong> 
                        {{ selectedUser.first_name }}
                    </p>
                    <p class="w100" style="color: black;">
                        <strong>Last Name:</strong>
                        {{ selectedUser.last_name }}
                    </p>
                    <p class="w100" style="color: black;">
                        <strong>Email:</strong>
                        {{ selectedUser.email }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div v-if="isEditModalOpen" class="modal">
            <div class="modal-content">
                <span class="close" @click="closeEditModal">&times;</span>
                <div class="update">
                    <div class="top">
                        <div class="logo"></div>
                        <div class="title">Update User</div>
                    </div>

                    <form @submit.prevent="updateUser">
                        <input
                            type="text"
                            class="w100"
                            placeholder="First name"
                            v-model="selectedUser.first_name"
                            required
                        />

                        <input
                            type="text"
                            class="w100"
                            placeholder="Last name"
                            v-model="selectedUser.last_name"
                            required
                        />

                        <input
                            type="text"
                            class="w100"
                            placeholder="Email"
                            v-model="selectedUser.email"
                            pattern="^[\w\.\-]+@[\w\.\-]+\.\w+$"
                            required
                        />

                        <button type="submit" class="action">Update</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Register User Modal -->
        <div v-if="isRegisterModalOpen" class="modal">
            <div class="modal-content">
                <span class="close" @click="closeRegisterModal">&times;</span>
                
                <div class="register">
                    <div class="top">
                        <div class="logo"></div>
                        <div class="title">Register New User</div>
                    </div>

                    <form @submit.prevent="registerUser">
                        <input
                            type="text"
                            class="w100"
                            placeholder="First name"
                            v-model="newUser.first_name"
                            required
                        />

                        <input
                            type="text"
                            class="w100"
                            placeholder="Last name"
                            v-model="newUser.last_name"
                            required
                        />

                        <input
                            type="text"
                            class="w100"
                            placeholder="Email"
                            v-model="newUser.email"
                            pattern="^[\w\.\-]+@[\w\.\-]+\.\w+$"
                            required
                        />
                        <input
                            type="password"
                            class="w100"
                            placeholder="Password"
                            v-model="newUser.password"
                            required
                        />

                        <button type="submit" class="action">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
    import axios from 'axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-dt';

    DataTable.use(DataTablesCore);

    export default {
        components: {
            DataTable,
        },

        data() {
            return {
                users: [],
                selectedUser: {},
                newUser: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                },
                isViewModalOpen: false,
                isEditModalOpen: false,
                isRegisterModalOpen: false,

                columns: [
                    { data: 'id', title: 'ID' },
                    { data: 'first_name', title: 'First Name' },
                    { data: 'last_name', title: 'Last Name' },
                    { data: 'email', title: 'Email' },
                    {
                        data: null,
                        title: 'Actions',
                        render: '#actions',
                    },
                ],
            };
        },

        methods: {
            fetchUsers() {
                axios.get('http://localhost:8000/api/users')
                    .then((response) => {
                        this.users = response.data.data;
                    })
                    .catch((error) => {
                        console.error('Error fetching users:', error);
                    });
            },
            viewUser(userId) {
                axios
                .get(`http://localhost:8000/api/users/${userId}`)
                .then((response) => {
                    this.selectedUser = response.data.data;
                    this.isViewModalOpen = true;
                })
                .catch((error) => {
                    console.error('Error fetching user details:', error);
                });
            },
            editUser(user) {
                this.selectedUser = { ...user };
                this.isEditModalOpen = true;
            },
            updateUser() {
                axios
                .put(`http://localhost:8000/api/users/${this.selectedUser.id}`, this.selectedUser)
                .then(() => {
                    this.fetchUsers();
                    this.closeEditModal();
                })
                .catch((error) => {
                    console.error('Error updating user:', error);
                });
            },
            registerUser() {
                axios
                .post('http://localhost:8000/api/users', this.newUser)
                .then(() => {
                    this.fetchUsers();
                    this.closeRegisterModal();
                    this.newUser = {
                        first_name: '',
                        last_name: '',
                        email: '',
                        password: '',
                    };
                })
                .catch((error) => {
                    console.error('Error registering user:', error);
                });
            },
            deleteUser(userId) {
                axios
                .delete(`http://localhost:8000/api/users/${userId}`)
                .then((response) => {
                    this.fetchUsers();
                })
                .catch((error) => {
                    console.error('Error fetching user details:', error);
                });
            },
            openRegisterModal() {
                this.isRegisterModalOpen = true;
            },
            closeViewModal() {
                this.isViewModalOpen = false;
            },
            closeEditModal() {
                this.isEditModalOpen = false;
            },
            closeRegisterModal() {
                this.isRegisterModalOpen = false;
            },
        },

        mounted() {
            this.fetchUsers();
        },
    };
</script>
    
<style>
    @import 'datatables.net-dt';

    /* Add your styles here */
    .modal {
        display: flex;
        /* display: block; */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        /* background-color: #888;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%; */

        background: #fff;
        border-radius: 15px;
        max-width: 400px;
        padding: 25px 55px;
        animation: slideInTop 1s;
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* ALI HASSAN: Styles Copy from LoginView.vue */
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

    .action-mini
    {
        height: 20px;
        text-transform: uppercase;
        border-radius: 25px;
        width: 100%;
        border: none;
        cursor: pointer;
        background: green;
        color: #fff;
        font-size: 0.6rem;
        box-shadow: 1px 1px 2px 1px #ccc;
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

    .action-mini-disabled
    {
        color: #eee;
        background: #aaa;
        cursor: auto;
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

    /* .loginBox
    {
        background: #fff;
        border-radius: 15px;
        max-width: 400px;
        padding: 25px 55px;
        animation: slideInTop 1s;
    } */

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
  