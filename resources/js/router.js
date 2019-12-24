import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/login',
        component: require('./views/auth/LoginPage.vue').default
    },
    {
        path: '/account',
        component: require('./views/account/Index.vue').default,
        meta: {
            breadcrumb: 'Account'
        },
        children: [
            {
                path: '/',
                component: require('./views/account/AccountInfo.vue').default,
                meta: {
                    breadcrumb: 'Account info'
                }
            },
            {
                path: 'edit',
                component: require('./views/account/EditAccountInfo.vue').default,
                meta: {
                    breadcrumb: 'Edit account info'
                }
            },
            {
                path: 'update-email',
                component: require('./views/account/UpdateEmail.vue').default,
                meta: {
                    breadcrumb: 'Update email'
                }
            },
            {
                path: 'change-password',
                component: require('./views/account/ChangePassword.vue').default,
                meta: {
                    breadcrumb: 'Change password'
                }
            }
        ]
    },
    {
        path: '/people',
        component: require('./views/people/Index.vue').default,
        meta: {
            breadcrumb: 'People'
        },
        children: [
            {
                path: 'customers',
                component: require('./views/people/customers/CustomerList.vue').default,
                meta: {
                    breadcrumb: 'Customers'
                }
            },
            {
                path: 'users',
                component: require('./views/people/users/UserList.vue').default,
                meta: {
                    breadcrumb: 'Users'
                }
            },
            {
                path: 'users/add',
                component: require('./views/people/users/AddEditUser.vue').default,
                meta: {
                    breadcrumb: 'Create new user'
                }
            },
            {
                path: 'users/:id/edit',
                component: require('./views/people/users/AddEditUser.vue').default,
                meta: {
                    breadcrumb: 'Edit user'
                }
            }
        ]
    },
    {
        path: '/pos',
        component: require('./views/pos/Index.vue').default,
        meta: {
            breadcrumb: 'POS'
        },
        children: [
            {
                path: 'products',
                component: require('./views/pos/Products.vue').default,
                meta: {
                    breadcrumb: 'Products'
                }
            },
            {
                path: 'services',
                component: require('./views/pos/Services.vue').default,
                meta: {
                    breadcrumb: 'Services'
                }
            }
        ]
    },
    {
        path: '/remote-activation',
        component: require('./views/remote-activation/Index.vue').default,
        meta: {
            breadcrumb: 'Remote activation'
        },
        children: [
            {
                path: 'remote-panel',
                component: require('./views/remote-activation/RemotePanel.vue').default,
                meta: {
                    breadcrumb: 'Remote panel'
                }
            }
        ]
    },
    {
        path: '/loyalty',
        component: require('./views/loyalty/Index.vue').default,
        meta: {
            breadcrumb: 'Loyalty'
        },
        children: [
            {
                path: 'discounts',
                component: require('./views/loyalty/discounts/DiscountsList.vue').default,
                meta: {
                    breadcrumb: 'Discounts'
                }
            },
            {
                path: 'points',
                component: require('./views/loyalty/points/PointsSetup.vue').default,
                meta: {
                    breadcrumb: 'Points'
                }
            }
        ]
    },
    {
        path: '/preferences',
        component: require('./views/preferences/Index.vue').default,
        meta: {
            breadcrumb: 'Preferences'
        },
        children: [
            {
                path: 'job-order',
                component: require('./views/preferences/job-order/Index.vue').default,
                meta: {
                    breadcrumb: 'Job order'
                }
            }
        ]
    }
];

export default new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});
