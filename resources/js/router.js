import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

const routes = [
    // {
    //     path: '/',
    //     component: require('./views/Home.vue').default,
    //     meta: {
    //         breadcrumb: 'Home'
    //     }
    // },
    {
        path: '/login',
        component: require('./views/auth/LoginPage.vue').default
    },
    {
        path: '/developer',
        component: require('./views/developer/Index.vue').default,
        meta: {
            breadcrumb: 'Developer'
        },
        children: [
            {
                path: 'clients',
                component: require('./views/developer/clients/Index.vue').default,
                meta: {
                    breadcrumb: 'Clients'
                },
                children: [
                    {
                        path: 'by-clients',
                        component: require('./views/developer/clients/ByClient.vue').default,
                        meta: {
                            breadcrumb: 'By clients'
                        },
                        alias: '/'
                    },
                    {
                        path: 'by-branches',
                        component: require('./views/developer/clients/ByBranch.vue').default,
                        meta: {
                            breadcrumb: 'By branches'
                        }
                    },
                    {
                        path: 'add',
                        component: require('./views/developer/clients/AddEdit.vue').default,
                        meta: {
                            breadcrumb: 'Add new client'
                        }
                    },
                    {
                        path: 'branches/:clientId',
                        component: require('./views/developer/branches/ClientBranches.vue').default,
                        meta: {
                            breadcrumb: 'Branches'
                        }
                    },
                    {
                        path: ':id',
                        component: require('./views/developer/clients/AddEdit.vue').default,
                        meta: {
                            breadcrumb: 'Client info'
                        }
                    },
                    {
                        path: ':id/view-machines',
                        component: require('./views/developer/clients/ViewMachines.vue').default,
                        meta: {
                            breadcrumb: 'Client info'
                        }
                    }
                ]
            }
        ]
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
        path: '/dashboard',
        component: require('./views/dashboard/Index.vue').default,
        meta: {
            breadcrumb: "Dashboard"
        },
        alias: '/'
    },
    {
        path: '/branch',
        component: require('./views/branch/Index.vue').default,
        meta: {
            breadcrumb: 'Branch'
        },
        children: [
            {
                path: 'profile',
                component: require('./views/branch/manage/Profile.vue').default,
                meta: {
                    breadcrumb: 'Branch profile'
                }
            },
            {
                path: 'discounts',
                component: require('./views/branch/discounts/DiscountList.vue').default,
                meta: {
                    breadcrumb: 'Discounts'
                }
            },
            {
                path: 'loyalty',
                component: require('./views/branch/loyalty/LoyaltySetup.vue').default,
                meta: {
                    breadcrumb: 'Loyalty setup'
                }
            },
            {
                path: 'manage',
                component: require('./views/branch/manage/BranchList.vue').default,
                meta: {
                    breadcrumb: 'Branches'
                },
                alias: '/'
            }
        ]
    },
    {
        path: '/expenses',
        component: require('./views/expenses/Index.vue').default,
        meta: {
            breadcrumb: 'Expenses'
        },
        children: [
            {
                path: 'expense-list',
                component: require('./views/expenses/ExpenseList.vue').default,
                meta: {
                    breadcrumb: 'Expenses'
                }
            },
            {
                path: 'expense-types',
                component: require('./views/expenses/ExpenseTypes.vue').default,
                meta: {
                    breadcrumb: 'Expense types'
                }
            }
        ]
    },
    {
        path: '/items',
        component: require('./views/items/Index.vue').default,
        meta: {
            breadcrumb: 'Items'
        },
        children: [
            {
                path: 'product-list',
                component: require('./views/items/products/ProductList.vue').default,
                meta: {
                    breadcrumb: 'Products'
                }
            },
            {
                path: 'service-list',
                component: require('./views/items/services/ServiceList.vue').default,
                meta: {
                    breadcrumb: 'Services'
                }
            },
            {
                path: 'others',
                component: require('./views/items/products/ProductList.vue').default,
                meta: {
                    breadcrumb: 'Other items'
                }
            },
            {
                path: 'services/add',
                component: require('./views/items/services/AddEditService.vue').default,
                meta: {
                    breadcrumb: 'Add service'
                }
            },
            {
                path: 'services/:id',
                component: require('./views/items/services/AddEditService.vue').default,
                meta: {
                    breadcrumb: 'Edit service'
                }
            },
            {
                path: 'product/add',
                component: require('./views/items/products/AddEditProduct.vue').default,
                meta: {
                    breadcrumb: 'Add product'
                }
            },
            {
                path: 'product/:id',
                component: require('./views/items/products/AddEditProduct.vue').default,
                meta: {
                    breadcrumb: 'Edit product'
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
                path: 'employees',
                component: require('./views/people/employees/EmployeeList.vue').default,
                meta: {
                    breadcrumb: 'Employees'
                }
            },
            {
                path: 'suppliers',
                component: require('./views/people/suppliers/SupplierList.vue').default,
                meta: {
                    breadcrumb: 'Suppliers'
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
        path: '/reports',
        component: require('./views/reports/Index.vue').default,
        meta: {
            breadcrumb: 'Reports'
        },
        children: [
            {
                path: 'transactions',
                component: require('./views/reports/Transactions.vue').default,
                meta: {
                    breadcrumb: 'Transactions'
                },
                children: [
                    {
                        path: 'pos-transactions',
                        component: require('./views/reports/transactions/PosTransaction.vue').default,
                        meta: {
                            breadcrumb: 'POS Transactions'
                        },
                        children: [
                            {
                                path: 'by-customers',
                                component: require('./views/reports/transactions/pos/ByCustomers.vue').default,
                                meta: {
                                    breadcrumb: 'By customers'
                                },
                                alias: '/'
                            },
                            {
                                path: 'by-items',
                                component: require('./views/reports/transactions/pos/ByItems.vue').default,
                                meta: {
                                    breadcrumb: 'By items'
                                }
                            }
                        ],
                        alias: '/'
                    },
                    {
                        path: 'rfid-transactions',
                        component: require('./views/reports/transactions/RfidTransactions.vue').default,
                        meta: {
                            breadcrumb: 'RFID Services'
                        }
                    },
                    {
                        path: 'topup-transactions',
                        component: require('./views/reports/transactions/TopupTransactions.vue').default,
                        meta: {
                            breadcrumb: 'RFID Top ups'
                        }
                    }
                ]
            },
            // {
            //     path: 'unpaid-transactions',
            //     component: require('./views/reports/UnpaidTransactions.vue').default,
            //     meta: {
            //         breadcrumb: 'Unpaid transactions'
            //     }
            // },
            // {
            //     path: 'receipts',
            //     component: require('./views/reports/Receipts.vue').default,
            //     meta: {
            //         breadcrumb: 'Receipts'
            //     }
            // },
            {
                path: 'inventories',
                component: require('./views/reports/Inventories.vue').default,
                meta: {
                    breadcrumb: 'Inventories'
                }
            },
            // {
            //     path: 'expenses',
            //     component: require('./views/reports/Expenses.vue').default,
            //     meta: {
            //         breadcrumb: 'Expenses'
            //     }
            // },
            {
                path: 'purchases',
                component: require('./views/reports/Purchases.vue').default,
                meta: {
                    breadcrumb: 'Purchases'
                }
            },
            {
                path: 'trashed',
                component: require('./views/reports/trashed/Index.vue').default,
                meta: {
                    breadcrumb: 'Trashed'
                },
                children: [
                    {
                        path: 'transactions',
                        component: require('./views/reports/trashed/TrashedTransactions.vue').default,
                        meta: {
                            breadcrumb: 'Transactions'
                        }
                    },
                    {
                        path: 'transaction-items',
                        component: require('./views/reports/trashed/TrashedTransactionItems.vue').default,
                        meta: {
                            breadcrumb: 'Transaction items'
                        },
                    }
                ]
            }
        ]
    },
    {
        path: '/rfid',
        component: require('./views/rfid/Index.vue').default,
        meta: {
            breadcrumb: 'RFID'
        },
        children: [
            {
                path: 'service-prices',
                component: require('./views/rfid/service-prices/ServicePricesList.vue').default,
                meta: {
                    breadcrumb: 'Service prices'
                }
            },
            {
                path: 'cards/:cardType?',
                component: require('./views/rfid/cards/CardsList.vue').default,
                meta: {
                    breadcrumb: 'RFID Cards'
                }
            },
            {
                path: 'transactions/services',
                component: require('./views/rfid/transactions/ServiceTransactionList.vue').default,
                meta: {
                    breadcrumb: 'RFID Transactions'
                }
            },
            {
                path: 'transactions/top-up',
                component: require('./views/rfid/transactions/TopUpTransactionList.vue').default,
                meta: {
                    breadcrumb: 'RFID Top up'
                }
            }
        ]
    },
    {
        path: '/sys-defaults',
        component: require('./views/sys-defaults/Index.vue').default,
        meta: {
            breadcrumb: 'System defaults'
        },
        children: [
            {
                path: 'branch',
                component: require('./views/sys-defaults/SelectDefaultBranch.vue').default,
                meta: {
                    breadcrumb: 'Select branch'
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
