<template>
    <div class="mt-2 p-3">
        <v-card
            class=" mx-auto p-3"
            max-width="600"
            outlined
        >
            <div class="">Lesson {{ lesson.name }}</div>
            <div class="">
                <input v-model="lesson.text1" type="text" class="mt-2 form-control">
                <input v-model="lesson.text2" type="text" class="mt-2 form-control">
                <input v-model="lesson.text3" type="text" class="mt-2 form-control">
                <input v-model="lesson.text4" type="text" class="mt-2 form-control">

                <div class="d-flex justify-content-end py-3">
                    <v-btn
                        @click="submit"
                        color="primary"
                    >
                        save
                    </v-btn>

                </div>
            </div>

        </v-card>

    </div>
</template>

<script>
export default {
    props: {
        lesson: Object,
    },
    methods: {
        submit() {
            this.request('put', `/lesson/${this.lesson.id}`, this.lesson)
        }
    }
};
</script>


