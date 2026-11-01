import { ref } from 'vue';
import axios from 'axios';

const settings = ref({
    phone_number1: '',
    phone_number2: '',
    phone_number3: '',
    account_name: '',
    account_number: '',
    bank_name: '',
    is_paystack_enabled: false,
    is_whatsapp_enabled: false,
    dispatch_return: ''
});

let isLoaded = false;
let loadPromise = null;

export function useSettings() {
    const loadSettings = async () => {
        if (isLoaded) return settings.value;
        
        if (loadPromise) return loadPromise;
        
        loadPromise = (async () => {
            try {
                const response = await axios.get('/settings', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                settings.value = {
                    phone_number1: response.data.phone_number1 || '',
                    phone_number2: response.data.phone_number2 || '',
                    phone_number3: response.data.phone_number3 || '',
                    account_name: response.data.account_name || '',
                    account_number: response.data.account_number || '',
                    bank_name: response.data.bank_name || '',
                    is_paystack_enabled: response.data.is_paystack_enabled === true,
                    is_whatsapp_enabled: response.data.is_whatsapp_enabled === true,
                    dispatch_return: response.data.dispatch_return || ''
                };
                isLoaded = true;
            } catch (error) {
                console.error('Failed to fetch settings:', error);
                isLoaded = true;
            }
            return settings.value;
        })();
        
        return loadPromise;
    };

    const getPhoneNumbers = () => {
        const phones = [];
        if (settings.value.phone_number1) phones.push(settings.value.phone_number1);
        if (settings.value.phone_number2) phones.push(settings.value.phone_number2);
        if (settings.value.phone_number3) phones.push(settings.value.phone_number3);
        return phones;
    };

    const getPrimaryPhone = () => {
        return settings.value.phone_number1 || settings.value.phone_number2 || settings.value.phone_number3 || '';
    };

    return {
        settings,
        loadSettings,
        getPhoneNumbers,
        getPrimaryPhone
    };
}