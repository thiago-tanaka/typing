<template>
    <div class="container">
        <notification/>
        <div class="row justify-content-center">
            <div class="col-12">
                <v-app>
                    <v-tabs v-model="tab">
                        <v-tab v-for="unit in units" :key="unit.id">UNIT {{ unit.name }}</v-tab>
                        <v-tabs-items v-model="tab">
                            <v-tab-item
                                transition="no-transition"
                                v-for="unit in units"
                                :key="unit.id"
                            >
                                <v-card flat>
                                    <lesson-component
                                        :unit="unit"
                                        :key="lesson.id"
                                        v-for="lesson in unit.lessons"
                                        :lesson="lesson"
                                    ></lesson-component>
                                </v-card>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-tabs>
                </v-app>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            tab: 0,
            units: []
        }
    },
    created() {
        this.getUnits()
    },
    methods: {
        getUnits() {
            axios.get('/units').then((response) => {
                this.units = response.data.units
            })
        }
    },
}
</script>
