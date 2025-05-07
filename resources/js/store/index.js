import { createStore } from 'vuex';
import * as isvek from "bvi";
import { getCookie, setCookie, deleteCookie, deleteCookiesWithPrefix } from '@/utils/cookies';


export default createStore({
    state: {
        lastSlider: null,
        bvi: null,               // Экземпляр плагина bvi
        bviSettings: {           // Настройки по умолчанию
            target: '.open-bvi',
            fontSize: 24,
            theme: 'black',
            speech: false,
            reload: true,
            panelHide: true
        },
        isBviActive: false       // Флаг активности панели
    },
    mutations: {
        setLastSlider(state, value) {
            state.lastSlider = value;
        },
        setBviInstance(state, instance) {
            state.bvi = instance;
        },
        setBviActive(state, isActive) {
            state.isBviActive = isActive;
        },
        updateBviSettings(state, settings) {
            state.bviSettings = { ...state.bviSettings, ...settings };
        }
    },
    actions: {
        updateLastSlider({ commit }, value) {
            commit('setLastSlider', value);
        },
        initializeBvi(context) {
            if (!context.state.bvi) {
                const bviInstance = new isvek.Bvi(context.state.bviSettings);
                context.commit('setBviInstance', bviInstance);
            }

            if (getCookie('bvi_panelActive')) {
                context.dispatch('toggleBvi');  // Вызываем через context.dispatch
            }
        },
        toggleBvi({ commit, state }) {
            if (state.bvi) {
                commit('setBviActive', !state.isBviActive);
            }
        },
        updateBviSettings({ commit }, settings) {
            commit('updateBviSettings', settings);
        }
    },
    getters: {
        lastSlider: (state) => state.lastSlider,
        bviInstance: (state) => state.bvi,
        isBviActive: (state) => state.isBviActive,
        bviSettings: (state) => state.bviSettings
    }
});