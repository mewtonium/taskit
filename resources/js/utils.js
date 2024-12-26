import Notify from 'simple-notify';

export const toast = ({ title, text = null, status = 'info' }) => {
    return (new Notify({
        title,
        status,
        text,
        type: 'filled',
        effect: 'slide',
        showCloseButton: true,
        position: 'bottom right',
    }))
};
