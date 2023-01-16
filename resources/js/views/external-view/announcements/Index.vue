<template>
    <v-container>
        <h4 class="title panel-title">Announcements</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn class="primary ml-0 translucent" round @click="addAnnouncement">
            <v-icon small left>add</v-icon>
            Create new announcement
        </v-btn>
        <v-progress-linear v-if="loading" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-layout>
            <v-flex sm6 lg4 v-for="item in items" :key="item.id">
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer" :elevation="hover ? 12 : 2">
                        <v-card-text>{{item.content}}</v-card-text>
                    <v-card-actions>
                        <v-btn icon @click="editAnnouncement($event, item)">
                            <v-icon>edit</v-icon>
                        </v-btn>
                        <v-btn icon @click="deleteAnnouncement($event, item)" :loading="item.isDeleting">
                            <v-icon>delete_outline</v-icon>
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-tooltip top v-if="item.is_default">
                            <!-- <v-btn slot="activator" icon> -->
                                <v-icon slot="activator" color="blue">bookmark</v-icon>
                            <!-- </v-btn> -->
                            <span>Active as default</span>
                        </v-tooltip>
                        <v-tooltip top v-else>
                            <v-btn slot="activator" icon @click="setDefault($event, item)" :loading="item.settingDefault">
                                <v-icon>bookmark_outline</v-icon>
                            </v-btn>
                            <span>Set as default</span>
                        </v-tooltip>
                    </v-card-actions>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>
        <!-- <v-card class="translucent rounded-card">
            <v-list v-if="items.length" class="transparent" two-line>
                <v-list-tile v-for="item in items" :key="item.id" @click="editAnnouncement(item)">
                    <v-list-tile-content>
                        <span class="title font-weight-bold grey--text">Date {{item.date}}</span>
                        {{item.content}}
                    </v-list-tile-content>
                    <v-list-tile-action>
                        <v-btn icon>
                            <v-icon>delete</v-icon>
                        </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </v-card> -->
        <!-- <pre>{{items}}</pre> -->
        <announcement-dialog v-model="openAnnouncementDialog" :announcement="activeAnnouncement" @save="editContinue" />
    </v-container>
</template>

<script>
import AnnouncementDialog from './AnnouncementDialog.vue';

export default {
    components: {
        AnnouncementDialog
    },
    data() {
        return {
            loading: false,
            items: [],
            activeAnnouncement: null,
            openAnnouncementDialog: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/announcements').then((res, rej) => {
                this.items = res.data.result.data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        editAnnouncement(announcement) {
            this.activeAnnouncement = announcement;
            this.openAnnouncementDialog = true;
        },
        editContinue(data) {
            if(data.mode == 'insert') {
                this.items.push(data.announcement);
            } else {
                this.activeAnnouncement.name = data.announcement.name;
                this.activeAnnouncement.contact_number = data.announcement.contact_number;
                this.activeAnnouncement.email = data.announcement.email;
                this.activeAnnouncement.address = data.announcement.address;
                this.activeAnnouncement.first_visit = data.announcement.first_visit;
                this.activeAnnouncement.crn = data.announcement.crn;
                this.activeAnnouncement.remarks = data.announcement.remarks;
            }
        },
        addAnnouncement() {
            this.activeAnnouncement = null;
            this.openAnnouncementDialog = true;
        },
        deleteAnnouncement(announcement) {
            if(confirm('Delete this announcement?')) {
                Vue.set(announcement, 'isDeleting', true);
                this.$store.dispatch('announcement/deleteAnnouncement', announcement.id).then((res, rej) => {
                    this.items = this.items.filter(c => c.id != announcement.id);
                }).finally(() => {
                    Vue.set(announcement, 'isDeleting', false);
                })
            }
        },
        editAnnouncement($e, item) {
            $e.stopPropagation();
            this.activeAnnouncement = item;
            this.openAnnouncementDialog = true;
        },
        setDefault($e, item) {
            $e.stopPropagation();
            if(confirm(`Set as default announcement?`)) {
                Vue.set(item, 'settingDefault', true);
                this.$store.dispatch('announcement/setDefault', item.id).then((res, rej) => {
                    this.items = this.items.filter(e => e.id != res.data.eventId);
                    this.items.find(e => e.is_default).is_default = false;
                    item.is_default = true;
                    Vue.set(item, 'settingDefault', false);
                }).finally(() => {
                    Vue.set(item, 'settingDefault', false);
                });
            }
        },
        deleteAnnouncement($e, item) {
            $e.stopPropagation();
            if(confirm('Are you sure you want to delete this announcement?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('announcement/deleteEvent', item.id).then((res, rej) => {
                    this.items = this.items.filter(e => e.id != res.data.eventId);
                    Vue.set(item, 'isDeleting', false);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
