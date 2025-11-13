import template from './sw-flow-kdot-modal.html.twig';

const { Component, Context } = Shopware;

Component.register('sw-flow-kdot-modal', {
    template,

    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {};
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            const { config } = this.sequence;
        },


        onClose() {
            this.$emit('modal-close');
        },

        getConfig() {
            return {};
        },

        onAddAction() {
            const config = this.getConfig();
            const data = {
                ...this.sequence,
                config,
            };

            this.$emit('process-finish', data);
        },
    },
});