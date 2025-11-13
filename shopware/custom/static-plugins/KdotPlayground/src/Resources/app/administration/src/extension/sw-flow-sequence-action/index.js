import { ACTION, GROUP } from '../../constant/kdot-action.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {
        modalName() {
            if (this.selectedAction === ACTION.KDOT) {
                return 'sw-flow-kdot-modal';
            }

            return this.$super('modalName');
        },
    },

    methods: {
        getActionDescriptions(sequence) {
            if(sequence.actionName === ACTION.KDOT){
                return this.$tc('kdot-action.description')
            }
            return this.$super('getActionDescriptions', sequence)
        },

        getActionTitle(actionName) {
            if (actionName === ACTION.KDOT) {
                return {
                    value: actionName,
                    icon: 'regular-tag',
                    label: this.$tc('kdot-action.label'),
                    group: GROUP
                }
            }

            return this.$super('getActionTitle', actionName);
        },
    },
});