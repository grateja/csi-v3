import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/login',
        component: require('./views/auth/LoginPage.vue').default
    },
    {
        path: '/client',
        component: require('./views/client/Index.vue').default
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
        path: '/daily-report',
        component: require('./views/daily-report/Index.vue').default
    },
    {
        path: '/sales-report',
        component: require('./views/sales-report/Index.vue').default,
        children: [
            // {
            //     path: 'pos-transactions/:monthIndex?/:year?',
            //     component: require('./views/sales-report/PosTransactions.vue').default
            // },
            {
                path: 'calendar-view',
                component: require('./views/sales-report/calendar/Index.vue').default
            },
            {
                path: 'weekly-view',
                component: require('./views/sales-report/WeekView.vue').default
            },
            {
                path: 'monthly-view',
                component: require('./views/sales-report/montly-view/Index.vue').default
            },
            {
                path: 'yearly-view',
                component: require('./views/sales-report/yearly-view/Index.vue').default
            },
            {
                path: 'custom-date',
                component: require('./views/sales-report/date-range/Index.vue').default
            }
        ]
    },
    {
        path: '/new-transaction',
        component: require('./views/transactions/Index.vue').default,
        meta: {
            breadcrumb: 'Transaction'
        },
        children: [
            {
                path: 'qr',
                component: require('./views/transactions/QRScanner.vue').default
            },
            {
                path: 'services',
                component: require('./views/transactions/Services.vue').default
            },
            {
                path: 'per-kilo',
                component: require('./views/transactions/PerKilo.vue').default
            },
            {
                path: 'scarpa-cleanings',
                component: require('./views/transactions/ScarpaCleanings.vue').default
            },
            {
                path: 'products',
                component: require('./views/transactions/Products.vue').default
            },
            {
                path: 'lagoon',
                component: require('./views/transactions/Lagoon.vue').default
            },
            {
                path: 'elux',
                component: require('./views/transactions/EluxServices.vue').default
            }
        ]
    },
    {
        path: '/transaction-reports',
        component: require('./views/transaction-reports/Index.vue').default,
        children: [
            {
                path: 'by-job-orders',
                component: require('./views/transaction-reports/ByJobOrders.vue').default
            },
            {
                path: 'by-service-items',
                component: require('./views/transaction-reports/ByServiceItems.vue').default
            },
            {
                path: 'by-product-items',
                component: require('./views/transaction-reports/ByProductItems.vue').default
            }
        ]
    },
    {
        path: '/unpaid-transactions',
        component: require('./views/unpaid-transactions/Index.vue').default
    },
    {
        path: '/remote-panel/:mt?',
        component: require('./views/remote-panel/Index.vue').default
    },
    {
        path: '/pending-services',
        component: require('./views/pending-services/Index.vue').default
    },
    {
        path: '/products',
        component: require('./views/products/Index.vue').default,
        meta: {
            breadcrumb: 'Products'
        }
    },
    {
        path: '/services',
        component: require('./views/services/Index.vue').default,
        children: [
            {
                path: 'washing-services',
                component: require('./views/services/washing-services/Index.vue').default
            },
            {
                path: 'drying-services',
                component: require('./views/services/drying-services/Index.vue').default
            },
            {
                path: 'other-services',
                component: require('./views/services/other-services/Index.vue').default
            },
            {
                path: 'full-services',
                component: require('./views/services/full-services/Index.vue').default
            },
            {
                path: 'scarpa-cleaning',
                component: require('./views/services/scarpa-cleaning/Index.vue').default
            },
            {
                path: 'lagoon',
                component: require('./views/services/lagoon/Index.vue').default,
                children: [
                    {
                        path: 'per-kilo',
                        component: require('./views/services/lagoon/per-kilo/Index.vue').default
                    },
                    {
                        path: 'per-item',
                        component: require('./views/services/lagoon/per-item/Index.vue').default
                    }
                ]
            },
            {
                path: 'per-kilo',
                component: require('./views/services/per-kilo-services/Index.vue').default
            },
            {
                path: 'elux',
                component: require('./views/services/elextrolux/Index.vue').default
            }
        ]
    },
    {
        path: '/customers',
        component: require('./views/customers/Index.vue').default
    },
    {
        path: '/product-purchases',
        component: require('./views/product-purchases/Index.vue').default
    },
    {
        path: '/machines',
        component: require('./views/machines/Index.vue').default,
        children: [
            {
                path: 'lg',
                component: require('./views/machines/LG.vue').default
            },
            {
                path: 'elux',
                component: require('./views/machines/EluxMachines.vue').default
            }
        ]
    },
    {
        path: '/reworks',
        component: require('./views/reworks/Index.vue').default
    },
    {
        path: '/machines/remarks',
        component: require('./views/machines/MachineRemarks.vue').default
    },
    {
        path: '/rfid',
        component: require('./views/rfid/Index.vue').default,
        children: [
            {
                path: 'rfid-cards',
                component: require('./views/rfid/RfidCards.vue').default
            },
            {
                path: 'transactions',
                component: require('./views/rfid/Transactions.vue').default
            },
            {
                path: 'load-transactions',
                component: require('./views/rfid/LoadTransactions.vue').default
            }
        ]
    },
    {
        path: '/discounts',
        component: require('./views/discounts/Index.vue').default
    },
    {
        path: '/loyalty-points',
        component: require('./views/loyalty/Index.vue').default
    },
    {
        path: '/users',
        component: require('./views/users/Index.vue').default
    },
    {
        path: '/expenses',
        component: require('./views/expenses/Index.vue').default
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
    },
    {
        path: '/time-keeping',
        component: require('./views/time-keeping/Index.vue').default
    },
    {
        path: '/external-view',
        component: require('./views/external-view/Index.vue').default,
        children: [
            {
                path: 'events',
                component: require('./views/external-view/events/Index.vue').default,
                children: [
                    {
                        path: ':id',
                        component: require('./views/external-view/events/Event.vue').default
                    },
                    {
                        path: '/',
                        component: require('./views/external-view/events/EventsList.vue').default
                    }
                ],
            },
            {
                path: 'announcements',
                component: require('./views/external-view/announcements/Index.vue').default,
            }
        ]
    },
    {
        path: '/scanner',
        component: require('./views/qrscanner/Index.vue').default
    },
    {
        path: '/lagoon-partners',
        component: require('./views/lagoon-partners/Index.vue').default
    },
    {
        path: '/',
        component: require('./views/layout/TileMenu.vue').default
    },
    {
        path: '*',
        component: require('./views/layout/404.vue').default
    }
];

export default new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});
