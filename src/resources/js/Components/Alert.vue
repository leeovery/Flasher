<template>
    <div :class="messageClass"
         v-show="show"
    >
        <div class="ui left aligned container">

            <i class="close icon"
               v-show="dismissable"
               @click="hideAlert"
            ></i>

            <template v-if="header">
                <div class="ui left aligned header __header">
                    <i :class="[alarmIcon, 'icon']"></i>
                    {{ header }}
                </div>
                <p>
                    <slot></slot>
                </p>
            </template>

            <div v-else>
                <i :class="[alarmIcon, 'icon']"></i>
                <slot></slot>
            </div>

        </div>
    </div>
</template>

<script lang="babel" type="text/babel">
    import $ from "jquery";

    export default {
        props: {
            type: {
                // success | error | warning | info
                'default': 'info'
            },
            header: {
                'default': ''
            },
            // Controls styling, based on where in page you
            // want the Alert.
            placement: {
                // flush-banner | standard | inpage-flush | in-form | inpage
                'default': 'flush-banner'
            },
            // Whether to show hide X in corner or not
            // if we dont show this the alert will be fixed
            // unless we also set a timer.
            dismissable: {
                type: Boolean,
                'default': false
            },
            // If set to an int, then alert will disappear
            // after the set number of seconds.
            timeout: {
                'default': null
            },
            // If set to true, then the visibility of the alert
            // is controlled by Semantic UI form controls. i.e.
            // when we have errors, show the message.
            formErrors: {
                type: Boolean,
                'default': false
            }
        },

        data() {
            return {
                show: true
            }
        },

        ready() {
            if (this.timeout !== null) {
                //noinspection JSUnresolvedFunction
                setTimeout(() => {
                    this.hideAlert();
                }, this.timeout * 1000)
            }
        },

        methods: {
            hideAlert() {
                let ele = $(this.$el);
                ele.height(ele.height())
                   .width(ele.width())
                   .slideUp('normal', () => {
                       this.show = false;
                       ele.remove();
                   });
            }
        },

        computed: {
            messageClass() {
                // ui {{ type | lowercase }} {{ placement | lowercase }} message alert
                //noinspection JSUnresolvedVariable
                return [
                    "ui message alert",
                    this.type.toLowerCase(),
                    this.placement.toLowerCase(),
                    { 'visible': !this.formErrors }
                ];
            },
            alarmIcon() {
                //noinspection JSUnresolvedVariable
                return {
                    'alarm': this.type == 'error',
                    'info': this.type == 'info',
                    'warning': this.type == 'warning',
                    'checkmark': this.type == 'success'
                }
            }
        }
    }
</script>

<style lang="scss">
    .ui.message.alert {
        &.success {
            background-color: #bdfc97;
            /*box-shadow: 0 0 0 1px #81ff41 inset, 0 0 0 0 rgba(0, 0, 0, 0);*/
            box-shadow: none;
            border-bottom: 2px solid #8cd460;

            &, i, a, .header {
                color: #376500;
            }
        }

        &.error {
            background-color: #fea3a3;
            /*box-shadow: 0 0 0 1px #fe3f40 inset, 0 0 0 0 rgba(0, 0, 0, 0);*/
            box-shadow: none;
            border-bottom: 2px solid #fe3f40;

            &, i, a, .header {
                color: #8e191e;
            }
        }

        &.warning {
            background-color: #fff07f;
            /*box-shadow: 0 0 0 1px #ffe43a inset, 0 0 0 0 rgba(0, 0, 0, 0);*/
            box-shadow: none;
            border-bottom: 2px solid #fcc60a;

            &, i, a, .header {
                color: #b16d00;
            }
        }

        &.info {
            background-color: #7ee3fc;
            /*box-shadow: 0 0 0 1px #37c7fc inset, 0 0 0 0 rgba(0, 0, 0, 0);*/
            box-shadow: none;
            border-bottom: 2px solid #37c7fc;

            &, i, a, .header {
                color: #00648a;
            }
        }

        &.flush-banner, &.inpage-flush {
            margin-top: 0;
            margin-bottom: 0;
            z-index: 50;
        }

        &.in-form, &.in-page {
            /*border-radius: 6px;*/
            margin-bottom: 30px;
        }

        &.standard {
            /*nowt here*/
        }

        .__header {
            font-size: 15px !important;

            i.alarm.icon {
                font-size: 15px;
                margin: 0;
                margin-right: 5px !important;
                line-height: 15px;
            }
        }

        a {
            /*font-style: italic;*/
            text-decoration: underline;
            font-weight: 700;

            &:hover {
                text-decoration: none;
            }
        }
    }
</style>