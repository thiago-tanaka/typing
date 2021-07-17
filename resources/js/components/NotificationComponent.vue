<template>
    <div>
        <notifications
            :duration="5000"
            width="300"
            animation-name="v-fade-right"
            position="top right">
            <template slot="body" slot-scope="props">
                <div :style="{
                    background:backgroundColor[props.item.type],
                    'border-color': borderColor[props.item.type]
                }" class="custom-template">
                    <v-icon class="custom-template-icon" :style="{color: iconColor[props.item.type]}"
                            aria-hidden="false">
                        mdi-{{ icon[props.item.type] }}
                    </v-icon>
                    <div class="custom-template-content">
                        <div class="custom-template-title">
                            Notification
                        </div>
                        <div class="custom-template-text">
                            {{ props.item.text }}
                        </div>
                    </div>
                    <div class="custom-template-close"
                         @click="props.close">
                        <v-icon aria-hidden="false">
                            mdi-close
                        </v-icon>
                    </div>
                </div>
            </template>
        </notifications>
    </div>
</template>

<script>
export default {
    data() {
        return {
            iconColor: {
                warn: '#dbc014',
                success: '#15C371',
                error: '#c31515',
            },
            backgroundColor: {
                warn: '#f9f7e8',
                success: '#e8f9ed',
                error: '#f9e8e8',
            },
            borderColor: {
                warn: '#f2ebd0',
                success: '#d0f2d1',
                error: '#f2d0d0',
            },
            icon: {
                warn: 'alert',
                success: 'check-underline',
                error: 'alert',
            },
        }
    },
}
</script>

<style lang="scss">

.custom-template {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    text-align: left;
    font-size: 13px;
    margin: 5px;
    margin-bottom: 0;
    align-items: center;
    justify-content: center;

    &, & > div {
        box-sizing: border-box;
    }

    background: #eeeeee;
    border: 2px solid;

    .custom-template-icon {
        flex: 0 1 auto;
        font-size: 26px;
        padding: 0 10px;
    }

    .custom-template-close {
        flex: 0 1 auto;
        padding: 0 20px;
        font-size: 16px;
        opacity: 0.2;
        cursor: pointer;

        &:hover {
            opacity: 0.8;
        }
    }

    .custom-template-content {
        padding: 10px;
        flex: 1 0 auto;

        .custom-template-title {
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 600;
        }
    }
}

.v-fade-right-enter-active,
.v-fade-right-leave-active,
.v-fade-right-move {
    transition: all .5s;
}

.v-fade-right-enter,
.v-fade-right-leave-to {
    opacity: 0;
    transform: translateX(300px) scale(0.2);
}
</style>

